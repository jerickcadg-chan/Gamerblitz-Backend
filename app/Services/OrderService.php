<?php

namespace App\Services;

use App\Constants\DefaultRole;
use App\Constants\ProductConstant;
use App\Constants\ProviderConstant;
use App\Constants\StatusConst;
use App\Http\Requests\OrderRequest;
use App\Jobs\LapakGamingOrderHandler;
use App\Mail\OrderAccountSucceed;
use App\Mail\SendErrorNotif;
use App\Mail\SendOrderNotif;
use App\Mail\SendSettlementNotif;
use App\Models\Balance;
use App\Models\ExchangeRate;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Voucher;
use App\Models\Discount;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductItem;
use App\Models\User;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class OrderService
{
    /**
     * @throws Exception|Throwable
     */
    public function store(OrderRequest $request): Order|string|array
    {
        try {
            DB::beginTransaction();

            $authUser = Auth::user();

            $productItem = ProductItem::with('product')->find($request->product_item_id);

            $paymentMethod = PaymentMethod::find($request->payment_method_id);

            $discount = null;

            if ($request->discount_code) {
                $validateDiscountCode = validate_discount_code($request->discount_code, $productItem->id);
                if ($validateDiscountCode['status'] === true) {
                    $discount = $validateDiscountCode['discount'];
                } else {
                    return $validateDiscountCode['message'];
                }
            }

            $baseCurrency = Setting::getBaseCurrency();
            $userCurrency = $request->currency_code;
            $exchangeRate = get_exchange_rate($baseCurrency, $userCurrency);

            [$price, $error] = $this->calculatePrice($request, $productItem, $paymentMethod, $exchangeRate, $request->qty, $discount);

            if ($error != null) {
                DB::rollBack();

                return $error;
            }

            if ($productItem->stock === 0) {
                DB::rollBack();

                return trans('order.out_of_stock');
            };

            if ($paymentMethod->slug === PaymentMethod::BALANCE) {
                if (!$authUser) {
                    DB::rollBack();

                    return trans('auth.you_should_login');
                }

                $balance = Balance::query()
                    ->lockForUpdate()
                    ->where(
                        'user_id',
                        $authUser->id
                    )->first() ?? new Balance(['amount' => 0]);

                if ($balance->amount < $price['total_price']) {
                    DB::rollBack();

                    return trans('order.no_balance');
                };
            }

            $orderStatus = $paymentMethod->slug === PaymentMethod::BALANCE ? Order::INPROCESS : $orderStatus = Order::PENDING;

            $order = new Order();
            $order->productItem()->associate($productItem);
            $order->user()->associate($authUser ?? null);
            $order->discount()->associate($price['discount']);
            $order->cust_account = $request->cust_account;
            $order->cust_email = $request->cust_email;
            $order->cust_phone_number = $request->cust_phone_number;
            $order->payment_method_id = $paymentMethod->id;
            $order->provider = $productItem->product->provider;
            $order->status = $orderStatus;
            $order->qty = $request->qty;
            $order->price = $price['price'];
            $order->capital = $price['capital'];
            $order->turnover = $price['turnover'];
            $order->admin_fee = $price['admin_fee'];
            $order->discount_price = $price['discount_price'];
            $order->total_price = $price['total_price'];
            $order->total_income = $price['total_income'];
            $order->expired_at = Carbon::parse(now())->addHours(1);
            $order->note = $request->note;
            $order->currency_code = $baseCurrency;
            $order->converted_currency_code = $userCurrency;
            $order->exchange_rate = $exchangeRate;
            $order->save();

            if ($order->discount) {
                $order->discount->used += 1;
                $order->discount->save();
            }

            if ($paymentMethod->vendor === PaymentMethod::XENDIT) {
                app(XenditService::class)->createOrderXenditInvoice($order);
            }

            $this->updateStatus($order, StatusConst::PENDING);

            if ($paymentMethod->slug == PaymentMethod::BALANCE) {
                $this->updateStatus($order, StatusConst::ON_PROCESS);
                $this->processOrder($order);

                BalanceService::update($balance, [
                    'balanceable_type' => Order::class,
                    'balanceable_id' => $order->id,
                    'amount' => -$order->total_price,
                    'description' => "Transaction $order->code"
                ]);
            }

            DB::commit();

            return $order;
        } catch (Exception $e) {
            DB::rollBack();
            throw_custom_exception($e);
            throw new Exception($e->getMessage());
        } catch (GuzzleException $e) {
            DB::rollBack();
            throw_custom_exception($e);
            throw new Exception($e->getMessage());
        }
    }

    private function calculatePrice(OrderRequest $request, ProductItem $productItem, PaymentMethod $paymentMethod, float $exchangeRate, int $qty = 1, Discount $discount = null): array
    {
        $price = $productItem->real_price * $qty;
        $capital = $productItem->capital * $qty;

        if (!auth()->user() || auth()?->user()?->role === DefaultRole::CUSTOMER) {
            if ($discount !== null) {
                $disc = [
                    'disc_id' => $discount->id ?? null,
                    'nominal' => $discount->nominal ? calc_discount($productItem->real_price, $discount->disc_type, $discount->nominal) : 0
                ];
            } else {
                $flashSale = get_active_flash_sale($productItem);

                $disc = [
                    'disc_id' => null,
                    'nominal' => $flashSale
                ];
            }
        }

        $adminFee = $this->calculateAdminFee(
            realPrice: $price,
            paymentMethod: $paymentMethod,
            exchangeRate: $exchangeRate
        );

        if ($paymentMethod->vendor === PaymentMethod::MANUAL) {
            $adminFee = $this->generateUniqueCode($paymentMethod->currency_code) / $exchangeRate;
        }

        $totalTurnover =  $price - $disc['nominal'];

        $totalPrice = $totalTurnover + $adminFee;

        $totalIncome = $totalTurnover - $capital;

        $prices = [
            'price' => $price,
            'capital' => $capital,
            'turnover' => $totalTurnover,
            'admin_fee' => $adminFee,
            'discount_price' => $disc['nominal'],
            'total_price' => $totalPrice,
            'total_income' => $totalIncome,
            'discount' => $disc['disc_id']
        ];

        return [$prices, null];
    }

    private function calculateAdminFee(
        $realPrice,
        $paymentMethod,
        $exchangeRate,
    ): float|int {
        if ($paymentMethod->vendor === PaymentMethod::MANUAL && $paymentMethod->slug !== PaymentMethod::BALANCE) {
            return $this->generateUniqueCode($paymentMethod->currency_code);
        }

        $amount = match ($paymentMethod->admin_type) {
            'percentage' => ceil($realPrice / ((100 - $paymentMethod->admin_fee) / 100)) - $realPrice,
            'nominal' => $paymentMethod->admin_fee,
            default => 0,
        };

        return $amount * $exchangeRate;
    }

    public function processOrder(Order $order): void
    {
        $productItem = $order->productItem;
        $provider = $productItem->product->provider;

        if ($provider === ProviderConstant::LAPAKGAMING) {
            LapakGamingOrderHandler::dispatch($order);
        }

        // ... handle other provider
    }

    public function updateStatus(Order $order, $status, $note = null): void
    {
        $order->status = $status;
        $order->save();

        if ($status === StatusConst::ON_PROCESS && $order->provider === ProviderConstant::MANUAL) {
            Mail::queue(new SendSettlementNotif($order));
        }

        if ($order->cust_email) {
            Mail::to($order->cust_email)->queue(new SendOrderNotif($order));
        }

        $this->createHistory($order->id, $status, 'order', $note);
    }

    public function createHistory($orderId, $status, $type, $note = null): bool
    {
        return DB::table('order_histories')->insert([
            'order_id' => $orderId,
            'status' => $status,
            'type' => $type,
            'note' => $note,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function updateVoucherStock($productItem)
    {
        $count = $productItem->vouchers->where('status', 'ready')->count();

        $productItem->stock = $count;
        $productItem->save();

        return $productItem->stock;
    }

    public function updateNote(Order $order, $note): void
    {
        $order->note = $note;
        $order->save();
    }

    public function updateCapital(Order $order, $capital): void
    {
        if ($capital !== $order->productItem->capital) {
            $order->capital = $capital;
            $order->total_income = $order->total_price - $capital;
            $order->save();

            $order->productItem->capital = $capital;
            $order->save();
        }
    }

    public function generateUniqueCode(string $currency = 'IDR'): string
    {
        switch (strtoupper($currency)) {
            case 'IDR':
                return (string) rand(10, 100);

            case 'PHP':
            case 'MYR':
                $value = mt_rand(1, 20) / 100;
                return number_format($value, 2, '.', '');

            case 'USD':
            case 'SGD':
                $value = mt_rand(1, 30) / 1000;
                return number_format($value, 2, '.', '');

            default:
                return (string) rand(1, 50);
        }
    }
}
