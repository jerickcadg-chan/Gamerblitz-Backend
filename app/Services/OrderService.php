<?php

namespace App\Services;

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
            /** @var User $authUser */
            $authUser = Auth::user();

            /** @var ProductItem $productItem */
            $productItem = ProductItem::find($request->product_item_id);

            $paymentMethod = PaymentMethod::where('name', $request->payment_method)->first();

            [$price, $error] = $this->calculatePrice($request, $productItem, $paymentMethod, $request->qty);

            if ($error != null) {
                DB::rollBack();

                return $error;
            }

            if ($productItem->stock === 0) {
                DB::rollBack();

                return trans('order.out_of_stock');
            };

            if ($request->payment_method === PaymentMethod::SALDO) {
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

            $orderStatus = $request->payment_method === PaymentMethod::SALDO ? Order::INPROCESS : $orderStatus = Order::PENDING;

            $baseCurrency = Setting::getBaseCurrency();
            $userCurrency = $request->currency_code;
            $exchangeRate = 0;

            if ($baseCurrency === $userCurrency) {
                $exchangeRate = 1;
            } else {
                $baseRate = ExchangeRate::effectiveRate($baseCurrency)->value('rate');
                if (!$baseRate) {
                    Log::critical('Missing exchange rate for base currency ' . $baseCurrency);
                }
                $userCurrencyRate = ExchangeRate::effectiveRate($userCurrency)->value('rate');
                if (!$userCurrencyRate) {
                    Log::critical('Missing exchange rate for currency ' . $userCurrency);
                }
                $exchangeRate = !$baseRate || !$userCurrencyRate ? 0 : pivot_exchange_rate($userCurrencyRate, $baseRate);
            }

            $order = new Order;
            $order->productItem()->associate($productItem);
            $order->user()->associate($authUser ?? null);
            $order->discount()->associate($price['discount']);
            $order->cust_account = $request->cust_account;
            $order->cust_email = $request->cust_email;
            $order->cust_phone_number = $request->cust_phone_number;
            $order->payment_method = $request->payment_method;
            $order->status = $orderStatus;
            $order->qty = $request->qty;
            $order->price = $price['price'];
            $order->capital = $price['capital'];
            $order->admin_fee = $price['admin_fee'];
            $order->discount_price = $price['discount_price'];
            $order->total_price = $price['total_price'];
            $order->total_income = $price['total_income'];
            $order->expired_at = Carbon::parse(now())->addHours(1);
            $order->note = $request->note;
            $order->currency_code = $userCurrency;
            $order->converted_currency_code = $baseCurrency;
            $order->exchange_rate = $exchangeRate;
            $order->save();

            if ($order->discount) {
                $order->discount->used += 1;
                $order->discount->save();
            }

            if ($paymentMethod->vendor === 'xendit') {
                app(XenditService::class)->createXenditInvoice($order);
            }

            $this->createHistory($order->id, $orderStatus, 'order');

            if ($paymentMethod->name == PaymentMethod::SALDO) {
                $this->updateStatus($order, StatusConst::ON_PROCESS);
                $this->processOrder($order);

                BalanceService::update($balance, [
                    'balanceable_type' => Order::class,
                    'balanceable_id' => $order->id,
                    'amount' => -$order->total_price,
                    'description' => "Transaksi $order->code"
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

    private function calculatePrice(OrderRequest $request, ProductItem $productItem, PaymentMethod $paymentMethod, int $qty = 1): array
    {
        $price = $productItem->real_price;
        /* if (str($productItem->product->category)->lower() == ProductConstant::JOKI && $productItem->product->product_joki == ProductJoki::JOKI_RANK) { */
            /* $joki = json_decode($request->note); */
            /* $validator = Validator::make((array) $joki, [ */
            /*     'startRank' => 'required|string', */
            /*     'startRankGrade' => 'required|integer', */
            /*     'startStars' => 'required|integer', */
            /*     'targetRank' => 'required|string', */
            /*     'targetRankGrade' => 'required|integer', */
            /*     'targetStars' => 'required|integer' */
            /* ]); */
            /* if ($validator->fails()) { */
            /*     return [null, $validator->messages()->toArray()]; */
            /* } */
            /* $jokiResult = $this->calculateJokiMLPrice( */
            /*     $joki->startRank, */
            /*     $joki->startRankGrade, */
            /*     $joki->startStars, */
            /*     $joki->targetRank, */
            /*     $joki->targetRankGrade, */
            /*     $joki->targetStars */
            /* ); */
            /**/
            /* $price = $jokiResult['price']; */
            /* $capital = $jokiResult['capital']; */
            /**/
            /* $disc = [ */
            /*     'disc_id' => null, */
            /*     'nominal' => 0 */
            /* ]; */
        /* } else { */
            $price = $productItem->real_price * $qty;
            $capital = $productItem->capital * $qty;

            // TODO: fix the logic of get_active_discount
            $disc = get_active_discount($price, $productItem->product_id, $productItem->id, $qty);
        /* } */

        if ($request->discount_code) {
            $discount = Discount::active()->where('code', $request->discount_code)->first();

            $disc = [
                'disc_id' => $discount->id ?? null,
                'nominal' => $discount->nominal ? calc_discount($productItem->real_price, $discount->disc_type, $discount->nominal) : 0
            ];
        } else {
            $disc = get_active_discount($productItem->real_price, $productItem->product_id, $productItem->id);
        }

        $xenditFee = $this->calculateXenditFee(
            realPrice: $price,
            adminFee: $paymentMethod->admin_fee,
            adminType: $paymentMethod->admin_type,
        );
        $forAdmin = 0;

        if ($xenditFee == 'no-admin' && $paymentMethod->name != PaymentMethod::SALDO) {
            $xenditFee = rand(30, 100);
            $forAdmin = $xenditFee;
        }

        $totalPrice = $price - $disc['nominal'] + $xenditFee;

        $totalIncome = $price - $disc['nominal'] + $forAdmin - $capital;

        $prices = [
            'price' => $price,
            'capital' => $capital,
            'admin_fee' => $xenditFee,
            'discount_price' => $disc['nominal'],
            'total_price' => $totalPrice,
            'total_income' => $totalIncome,
            'discount' => $disc['disc_id']
        ];

        return [$prices, null];
    }

    private function calculateXenditFee(
        $realPrice,
        $adminFee,
        $adminType,
    ): float|int
    {
        return match ($adminType) {
            'percentage' => ceil($realPrice / ((100 - $adminFee) / 100)) - $realPrice,
            'nominal' => $adminFee,
            default => 0,
        };
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

        if ($status === StatusConst::ON_PROCESS) {
            $this->sendSettlementNotif($order);
        }

        if ($order->cust_email) {
            Mail::to($order->cust_email)->send(new SendOrderNotif($order));
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

    public function sendVoucher($order)
    {
        $voucher = Voucher::ready()->where('product_item_id', $order->product_item_id)->first();

        if (is_null($voucher)) {
            return api_status_warning('Voucher not found');
        }

        if ($order->cust_email) {
            // \Mail::to($order->cust_email)->queue(new \App\Mail\SendVoucher($order, $voucher));
        }

        $productItem = ProductItem::find($order->product_item_id);

        $voucher->update([
            'status' => 'used',
            'order_id' => $order->id
        ]);

        $order->capital = $voucher->capital;
        $order->total_income = $order->price - $voucher->capital;
        $order->save();

        $this->updateVoucherStock($productItem);
        $this->updateStatus($order, null, Order::DONE);
    }

    public function sendSettlementNotif(Order $order)
    {
        Mail::queue(new SendSettlementNotif($order));
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

    public function sentAccountCredentialsToUser(Order $order): void
    {
        $order->productItem->stock = 0;
        $order->productItem->save();
        $this->updateStatus(
            order: $order,
            status: StatusConst::SUCCESS,
        );

        Mail::to($order->cust_email)->queue(new OrderAccountSucceed($order));
    }

    public function calculateJokiMLPrice($startRankLabel, $startGrade, $startStars, $endRankLabel, $endGrade, $endStars): array
    {
        $rankOptions = $this->rankOptions();

        $price = 0;
        $capital = 0;

        $startRank = collect($rankOptions)->firstWhere('label', $startRankLabel);
        $endRank = collect($rankOptions)->firstWhere('label', $endRankLabel);

        if (!$startRank || !$endRank) {
            throw new \Exception("Invalid rank label provided.");
        }

        $position = 1;

        for ($i = $startRank['index']; $i <= $endRank['index']; $i++) {
            $rank = $rankOptions[$i - 1];

            if (!empty($rank['grades'])) {
                $firstLoop = $i == $startRank['index'] ? $startGrade : $rank['grades'][0];
                $endGradeLoop = $endRank['index'] > $i ? 1 : $endGrade;

                for ($grade = $firstLoop; $grade >= $endGradeLoop; $grade--) {
                    $endLoop = ($grade == $endGrade && $endRank['index'] == $i) ? $endStars : $rank['maxStars'];
                    $currentStar = $position == 1 ? $startStars : 1;

                    for ($star = $currentStar; $star <= $endLoop; $star++) {
                        if ($position > 1 && $star > 0) {
                            $price += $rank['price'];
                            $capital += $rank['capital'];
                        }
                        $position++;
                    }
                }
            } else {
                $currentStar = $position == 1 ? $startStars : $rank['minStars'];
                $maxLoop = $endRank['index'] == $i ? $endStars : $rank['maxStars'];

                for ($star = $currentStar; $star <= $maxLoop; $star++) {
                    if ($position > 1 && $star > 0) {
                        $price += $rank['price'];
                        $capital += $rank['capital'];
                    }
                    $position++;
                }
            }
        }

        return [
            'price' => $price,
            'capital' => $capital
        ];
    }

    private function rankOptions(): array
    {
        $category = ProductCategory::whereSlug(ProductConstant::JOKI)->first();
        /** @var Product $product */
        $product = Product::whereProductCategoryId($category->id)->first();
        $items = ProductItem::where('product_id', $product->id)->orderBy('price')->get()->toArray();

        return [
            [
                'index' => 1,
                'label' => 'Master',
                'price' => $items[0]['total_price'],
                'capital' => $items[0]['capital'],
                'product_item_id' => $items[0]['id'],
                'grades' => [4, 3, 2, 1],
                'minStars' => 0,
                'maxStars' => 4,
            ],
            [
                'index' => 2,
                'label' => 'Grand Master',
                'price' => $items[1]['total_price'],
                'capital' => $items[1]['capital'],
                'product_item_id' => $items[1]['id'],
                'grades' => [5, 4, 3, 2, 1],
                'minStars' => 0,
                'maxStars' => 5,
            ],
            [
                'index' => 3,
                'label' => 'Epic',
                'price' => $items[2]['total_price'],
                'capital' => $items[2]['capital'],
                'product_item_id' => $items[2]['id'],
                'grades' => [5, 4, 3, 2, 1],
                'minStars' => 0,
                'maxStars' => 5,
            ],
            [
                'index' => 4,
                'label' => 'Legend',
                'price' => $items[3]['total_price'],
                'capital' => $items[3]['capital'],
                'product_item_id' => $items[3]['id'],
                'grades' => [5, 4, 3, 2, 1],
                'minStars' => 0,
                'maxStars' => 5,
            ],
            [
                'index' => 5,
                'label' => 'Mythic',
                'price' => $items[4]['total_price'],
                'capital' => $items[4]['capital'],
                'product_item_id' => $items[4]['id'],
                'minStars' => 0,
                'maxStars' => 24,
            ],
            [
                'index' => 6,
                'label' => 'Mythical Honor',
                'price' => $items[5]['total_price'],
                'capital' => $items[5]['capital'],
                'product_item_id' => $items[5]['id'],
                'minStars' => 25,
                'maxStars' => 49,
            ],
            [
                'index' => 7,
                'label' => 'Mythical Glory',
                'price' => $items[6]['total_price'],
                'capital' => $items[6]['capital'],
                'product_item_id' => $items[6]['id'],
                'minStars' => 50,
                'maxStars' => 99,
            ],
            [
                'index' => 8,
                'label' => 'Mythical Immortal',
                'price' => $items[7]['total_price'],
                'capital' => $items[7]['capital'],
                'product_item_id' => $items[7]['id'],
                'minStars' => 100,
                'maxStars' => 5000,
            ],
        ];
    }
}
