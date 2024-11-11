<?php

namespace App\Services;

use App\Constants\ProductConstant;
use App\Mail\SendErrorNotif;
use App\Mail\SendSettlementNotif;
use App\Models\Balance;
use App\Models\Order;
use App\Models\Voucher;
use App\Models\Discount;
use App\Models\PaymentMethod;
use App\Models\ProductItem;
use App\Mail\SendOrderNotif;
use App\Transformers\OrderTransformer;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client as GuzzleClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class OrderService
{
    /**
     * @throws Exception
     */
    public function store($request)
    {
        try {
            DB::beginTransaction();

            $productItem = ProductItem::find($request->product_item_id);

            $paymentMethod = PaymentMethod::where('name', $request->payment_method)->first();

            $price = $this->calculatePrice($request, $productItem, $paymentMethod);

            if ($productItem->stock == 0) return trans('order.out_of_stock');

            if ($request->payment_method === PaymentMethod::SALDO) {
                if (!auth()->user()) return trans('order.no_balance');

                $balance = Balance::lockForUpdate()->where('user_id', auth()->user()->id)->first() ?? 0;

                if ($balance->amount < $price['total_price']) return trans('order.no_balance');
            }

            $paymentStatus = $request->payment_method === PaymentMethod::SALDO ? Order::SETTLEMENT : Order::PENDING;

            $orderStatus = $request->payment_method === PaymentMethod::SALDO ? Order::INPROCESS : Order::WAITING_PAYMENT;

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
                $this->setOrderSettlement($order);

                BalanceService::update($balance, [
                    'balanceable_type' => Order::class,
                    'balanceable_id' => $order->id,
                    'amount' => -$order->total_price,
                    'description' => "Transaksi $order->code"
                ]);

                $this->createBangJeffOrder($order);
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

    public function calculatePrice($request, $productItem, $paymentMethod): array
    {
        if ($request->discount_code) {
            $discount = Discount::active()->where('code', $request->discount_code)->first();

            $disc = [
                'disc_id' => $discount->id ?? null,
                'nominal' => $discount->nominal ? calc_discount($productItem->real_price, $discount->disc_type, $discount->nominal) : 0
            ];
        } else {
            $disc = get_active_discount($productItem->real_price, $productItem->product_id, $productItem->id);
        }

        $adminFee = $this->calculateAdminFee($productItem, $paymentMethod);
        $forAdmin = 0;

        if ($adminFee == 'no-admin' && $paymentMethod->name != PaymentMethod::SALDO) {
            $adminFee = rand(30,100);
            $forAdmin = $adminFee;
        }

        $totalPrice = $productItem->real_price - $disc['nominal'] + $adminFee;

        $totalIncome = $productItem->real_price - $disc['nominal'] + $forAdmin - $productItem->capital;

        return [
            'price' => $productItem->real_price,
            'capital' => $productItem->capital,
            'admin_fee' => $adminFee,
            'discount_price' => $disc['nominal'],
            'total_price' => $totalPrice,
            'total_income' => $totalIncome,
            'discount' => $disc['disc_id']
        ];
    }

    public function calculateAdminFee($productItem, $paymentMethod)
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
    public function createXenditInvoice($order)
    {
        $client = new GuzzleClient([
            'headers' => ['Content-Type' => 'application/json'],
            'auth' => [config('array.xendit.token'), null]
        ]);

        switch ($order->payment_method) {
            case PaymentMethod::QRIS:
                $r = $client->request('POST', config('array.xendit.url').'/qr_codes', [
                    'body' => json_encode([
                        'external_id' => $order->code,
                        'type' => 'DYNAMIC',
                        'amount' => (int) $order->total_price,
                        'callback_url' => route('callback.xendit'),
                    ])
                ]);

                break;

            default:
                $r = $client->request('POST', config('array.xendit.url').'/v2/invoices', [
                    'body' => json_encode([
                        'external_id' => $order->code,
                        'amount' => (int) $order->total_price,
                        'payer_email' => $order->cust_email ?? config('array.mail.no_reply'),
                        'description' => $order->productItem->name ." ". $order->productItem->product->name
                    ])
                ]);

                break;
        }

        return json_decode($r->getBody());
    }

    /**
     * @throws Exception
     */
    public function createBangJeffOrder(Order $order)
    {
        if (empty($order->productItem->product->code)) {
            return false;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '. config('array.bangjeff.api_key'),
            'Accept' => 'application/json'
        ])->post(config('array.bangjeff.url').'/api/v3/checkout', [
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
                 \Mail::to(config('array.mail.notification'))->queue(new SendErrorNotif($order, $response['message']));
            }
        } else {
            $order->bangjeff_invoice = $response['data']['invoiceNumber'];
            $order->save();
        }

        return $response;
    }

    public function setOrderSettlement($order)
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

    public function sendSettlementNotif($order)
    {
         \Mail::to(config('array.mail.notification'))->queue(new SendSettlementNotif($order));
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
}
