<?php

namespace App\Services;

use App\Constants\ProductConstant;
use App\Constants\ProductItemTypeConstant;
use App\Http\Requests\OrderRequest;
use App\Mail\OrderAccountSucceed;
use App\Mail\SendErrorNotif;
use App\Mail\SendSettlementNotif;
use App\Models\Balance;
use App\Models\Order;
use App\Models\Voucher;
use App\Models\Discount;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductItem;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class OrderService
{
    public function store(OrderRequest $request): Order|string
    {
        try {
            DB::beginTransaction();

            /** @var ProductItem $productItem */
            $productItem = ProductItem::find($request->product_item_id);

            $paymentMethod = PaymentMethod::where('name', $request->payment_method)->first();

            $price = $this->calculatePrice($request, $productItem, $paymentMethod, $request->qty);

            if ($productItem->stock == 0) {
                DB::commit();

                return trans('order.out_of_stock');
            };

            if ($request->payment_method === PaymentMethod::SALDO) {
                if (!auth()->user()) {
                    DB::commit();

                    return trans('auth.you_should_login');
                }

                $balance = Balance::query()
                    ->lockForUpdate()
                    ->where(
                        'user_id',
                        auth()->user()->id
                    )->first() ?? new Balance(['amount' => 0]);

                if ($balance->amount < $price['total_price']) {
                    DB::commit();

                    return trans('order.no_balance');
                };
            }

            $paymentStatus = $request->payment_method === PaymentMethod::SALDO ? Order::SETTLEMENT : Order::PENDING;

            if ($request->payment_method === PaymentMethod::SALDO) {
                $orderStatus = $productItem->type == ProductItemTypeConstant::ACCOUNT ? Order::DONE : Order::INPROCESS;
            } else {
                $orderStatus = Order::WAITING_PAYMENT;
            }

            $order = new Order;
            $order->productItem()->associate($productItem);
            $order->user()->associate(auth()->user() ?? null);
            $order->discount()->associate($price['discount']);
            $order->cust_account = $request->cust_account;
            $order->cust_email = $request->cust_email;
            $order->cust_phone_number = $request->cust_phone_number;
            $order->payment_method = $request->payment_method;
            $order->payment_status = $paymentStatus;
            $order->order_status = $orderStatus;
            $order->qty = $request->qty;
            $order->price = $price['price'];
            $order->capital = $price['capital'];
            $order->admin_fee = $price['admin_fee'];
            $order->discount_price = $price['discount_price'];
            $order->total_price = $price['total_price'];
            $order->total_income = $price['total_income'];
            $order->expired_at = Carbon::parse(now())->addHours(config('array.order.expired_hours'));
            $order->note = $request->note;
            $order->client()->associate(client());
            $order->save();

            if ($order->discount) {
                $order->discount->used += 1;
                $order->discount->save();
            }

            if ($paymentMethod->vendor == 'xendit') {
                $invoice = $this->createXenditInvoice($order);

                if ($order->payment_method == PaymentMethod::QRIS) {
                    $order->payment_code = $invoice->qr_string;
                }

                $order->payment_url = $invoice->invoice_url ?? null;
                $order->payment_id = $invoice->id;
                $order->save();
            }

            $this->createHistory($order->id, $paymentStatus, 'payment');
            $this->createHistory($order->id, $orderStatus, 'order');

            if ($paymentMethod->name == PaymentMethod::SALDO) {
                if ($productItem->type == ProductItemTypeConstant::ACCOUNT) {
                    $this->sentAccountCredentialsToUser($order);
                }
                $this->setOrderSettlement($order);

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
            DB::rollback();
            throw_custom_exception($e);
            throw new Exception($e->getMessage());
        } catch (GuzzleException $e) {
            DB::rollback();
            throw_custom_exception($e);
            throw new Exception($e->getMessage());
        }
    }

    public function calculatePrice(OrderRequest $request, ProductItem $productItem, PaymentMethod $paymentMethod, $qty = 1): array
    {
        if ($productItem->product->category == ProductConstant::JOKI) {
            $joki = json_decode($request->note);
            $jokiResult = $this->calculateJokiMLPrice(
                $joki->startRank,
                $joki->startRankGrade,
                $joki->startStars,
                $joki->targetRank,
                $joki->targetRankGrade,
                $joki->targetStars
            );

            $price = $jokiResult['price'];
            $capital = $jokiResult['capital'];

            $disc = [
                'disc_id' => null,
                'nominal' => 0
            ];
        } else {
            $price = $productItem->price * $qty;
            $capital = $productItem->capital * $qty;

            $disc = get_active_discount($price, $productItem->product_id, $productItem->id, $qty);
        }

        if ($request->discount_code) {
            $discount = Discount::active()->where('code', $request->discount_code)->first();

            $disc = [
                'disc_id' => $discount->id ?? null,
                'nominal' => $discount->nominal ? calc_discount($productItem->real_price, $discount->disc_type, $discount->nominal) : 0
            ];
        } else {
            $disc = get_active_discount($productItem->real_price, $productItem->product_id, $productItem->id);
        }

        $xenditFee = $this->calculateXenditFee($productItem, $paymentMethod);
        $forAdmin = 0;

        if ($xenditFee == 'no-admin' && $paymentMethod->name != PaymentMethod::SALDO) {
            $xenditFee = rand(30, 100);
            $forAdmin = $xenditFee;
        }

        $totalPrice = $productItem->real_price - $disc['nominal'] + $xenditFee;

        $totalIncome = $productItem->real_price - $disc['nominal'] + $forAdmin - $capital;

        return [
            'price' => $productItem->real_price,
            'capital' => $capital,
            'admin_fee' => $xenditFee,
            'discount_price' => $disc['nominal'],
            'total_price' => $totalPrice,
            'total_income' => $totalIncome,
            'discount' => $disc['disc_id']
        ];
    }

    public function calculateXenditFee(ProductItem $productItem, PaymentMethod $paymentMethod)
    {
        switch ($paymentMethod->admin_type) {
            case 'percentage':
                return $productItem->real_price * ($paymentMethod->admin_fee / 100);

            case 'nominal':
                return $paymentMethod->admin_fee;

            default:
                return 0;
        }
    }

    /**
     * @throws GuzzleException
     */
    public function createXenditInvoice(Order $order)
    {
        $xenditToken = client()->xendit_token;
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($xenditToken . ':')
        ];

        switch ($order->payment_method) {
            case PaymentMethod::QRIS:
                $response = Http::withHeaders($headers)->post(config('array.xendit.url') . '/qr_codes', [
                    'external_id' => $order->code,
                    'type' => 'DYNAMIC',
                    'amount' => (int) $order->total_price,
                    'callback_url' => route('callback.xendit'),
                ]);
                break;

            default:
                $response = Http::withHeaders($headers)->post(config('array.xendit.url') . '/v2/invoices', [
                    'external_id' => $order->code,
                    'amount' => (int) $order->total_price,
                    'payer_email' => $order->cust_email ?? config('array.mail.no_reply'),
                    'description' => $order->productItem->name . " " . $order->productItem->product->name
                ]);
                break;
        }

        return json_decode($response->getBody());
    }

    /**
     * @deprecated will be removed
     */
    public function createBangJeffOrder(Order $order)
    {
        if (empty($order->productItem->product->code)) {
            return false;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('array.bangjeff.api_key'),
            'Accept' => 'application/json'
        ])->post(config('array.bangjeff.url') . '/api/v3/checkout', [
            'code' => $order->productItem->code,
            'referenceNumber' => $order->code,
            'qty' => $order->qty,
            'inputs' => json_decode($order->cust_account)
        ]);

        $response = $response->collect();

        if ($response['error'] === true) {
            if ($response['message'] === "Invalid ID") {
                throw new Exception($response['message']);
            } else {
                Mail::to(config('array.mail.notification'))->queue(new SendErrorNotif($order, $response['message']));
            }
        } else {
            $order->bangjeff_invoice = $response['data']['invoiceNumber'];
            $order->save();
        }

        return $response;
    }

    public function setOrderSettlement(Order $order)
    {
        if ($order->payment_status === Order::PENDING) {
            $this->updateStatus($order, Order::SETTLEMENT, Order::INPROCESS);

            if ($order->cust_email) {
                // \Mail::to($order->cust_email)->queue(new SendOrderNotif($order));
            }
        }

        //            if ($order->productItem->product->category == ProductConstant::VOUCHER) {
        //                $this->sendVoucher($order);
        //            }

        $this->sendSettlementNotif($order);
    }

    public function updateStatus(Order $order, $paymentStatus = null, $orderStatus = null, $note = null)
    {
        if ($paymentStatus) {
            $order->payment_status = $paymentStatus;
            $order->save();

            $this->createHistory($order->id, $paymentStatus, 'payment', $note);
        }

        if ($orderStatus) {
            $order->order_status = $orderStatus;
            $order->save();

            $this->createHistory($order->id, $orderStatus, 'order', $note);
        }
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

    public function updateNote(Order $order, $note)
    {
        $order->note = $note;
        $order->save();
    }

    public function updateCapital(Order $order, $capital)
    {
        if ($capital !== $order->productItem->capital) {
            $order->capital = $capital;
            $order->total_income = $order->total_price - $capital;
            $order->save();

            $order->productItem->capital = $capital;
            $order->save();
        }
    }


    /**
     * @throws \Exception
     */
    public function createMitraGamersOrder(Order $order)
    {
        if (empty($order->productItem->code)) {
            return false;
        }

        $path = str(config('array.mitra-gamers.url'))->replaceEnd("/", "")->append('/api/v2/transaction')->value();

        $response = Http::withHeaders([
            'Valid-token' => base64_encode("b72ec94c-8884-4f46-8ba0-fa8363a48ddf"),
            'Accept' => 'application/json'
        ])->post($path, [
            'ref_id' => $order->code,
            'code' => $order->productItem->code,
            'qty' => $order->qty,
            'payment_method' => 'balance',
            'platform' => 'api',
            'customer_no' => CustAccountService::idExtractor($order->productItem->product->name, $order->cust_account),
            'selling_price' => $order->price - $order->discount_price,
            'note' => $order->note
        ]);

        if (!$response->ok()) {
            // $this->updateStatus(
            //     order: $order,
            //     orderStatus: Order::INPROCESS,
            // );
            // Mail::to(config('array.mail.notification'))->queue(new SendErrorNotif($order, $response->json("message")));

            return json_decode($response->collect());
        }
        $response = json_decode($response->collect());

        if ($response->payload) {
            $order->vexa_invoice = $response->payload->id;
            $order->save();
        }

        return $response;
    }

    public function sentAccountCredentialsToUser(Order $order)
    {
        $order->productItem->stock = 0;
        $order->productItem->save();
        $this->updateStatus(
            order: $order,
            orderStatus: Order::DONE,
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
        /** @var Product $product */
        $product = Product::whereCategory(ProductConstant::JOKI)->first();
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
