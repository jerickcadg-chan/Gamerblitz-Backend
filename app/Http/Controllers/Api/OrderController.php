<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Order;
use App\Models\ProductItem;
use App\Models\Voucher;
use App\Models\PaymentMethod;
use App\Models\DigiTransaction;
use App\Constants\ProductConstant;
use App\Services\CustAccountService;
use App\Transformers\DiscountTransformer;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Http\Requests\OrderRequest;
use App\Mail\SendOrderNotif;
use App\Models\Balance;
use App\Models\Product;
use App\Services\BalanceService;
use App\Transformers\OrderTransformer;
use App\Transformers\PaymentMethodTransformer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public $userId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->userId = auth()->user()->id ?? null;

            return $next($request);
        });
    }

    public function index()
    {
        $orders = Order::with('user', 'productItem.product')->latest()
            ->where('user_id', $this->userId)
            ->when(request('order_code'), function ($query) {
                return $query->where('code', 'like', '%'. request('order_code') .'%');
            });

        return api_status_ok(paginateTransformer($orders, new OrderTransformer));
    }

    public function show($order)
    {
        $order = Order::with('user', 'productItem.product')->where('code', $order)->first();

        if (empty($order)) {
            return api_status_warning('Order not found');
        }

        if ($this->userId != null) {
            if ($order->user_id != $this->userId) {
                return api_status_warning('User id not match!');
            }

            return api_status_ok(transformer($order, new OrderTransformer, ['vouchers']));
        }

        return api_status_ok(transformer($order, new OrderTransformer));
    }

    public function store(OrderService $orderService, OrderRequest $request)
    {
        try {
            $order = $orderService->store($request);

            if (is_string($order)) {
                return api_status_warning($order);
            }

            return api_status_ok(transformer($order, new OrderTransformer));
        } catch (\Exception $e) {
            return \api_status_error($e);
        }
    }

    public function getPaymentMethods()
    {
        return api_status_ok(transformer(PaymentMethod::orderBy('created_at', 'asc')->get(), new PaymentMethodTransformer));
    }

    public function getDiscount(Request $request)
    {
        $request->validate([
            'code' => 'required'
        ]);

        $promo = Discount::active()->where('code', $request->code)->first();

        if (empty($promo)) {
            return api_status_warning('Kode voucher tidak ditemukan', 201);
        }

        return api_status_ok(transformer($promo, new DiscountTransformer));
    }


    public function xenditCallback(Request $request, OrderService $orderService)
    {
        if ($request->header('x-callback-token') && $request->header('x-callback-token') != config('array.xendit.callback_token')) {
            return api_status_warning('Invalid token !!!');
        }

        $code = isset($request->qr_code) ? $request->qr_code['external_id'] : $request->external_id;

        $order = Order::where('code', $code)->first();

        if (is_null($request->id) || is_null($order)) {
            return api_status_warning('Order not found');
        }

        switch ($request->status) {
            case 'COMPLETED':
            case 'PAID':
                return $this->setOrderSettlement($order, $orderService);

            case 'EXPIRED':
                return $this->setOrderExpired($order, $orderService);

            default:
                return api_status_warning('Invalid request status');
        }
    }

    public function bangJeffCallback(Request $request, OrderService $orderService)
    {
        if (config('array.enable_log')) {
            Log::info("BANGJEFF LOG - IP {$request->ip()} - REQUEST ". json_encode($request->all()) . " - HEADERS ". json_encode($request->header()));
        }

//        if ($request->ip() != config('array.bangjeff.ip')) {
//            return api_status_warning('Invalid IP !!!');
//        }

        $order = Order::where('vexa_invoice', $request->invoice_number)->first();

        if (empty($order)) {
            return api_status_warning("Wrong number!");
        }

        $productItem = $order->productItem;

        switch ($request->status_code) {
            case 'SUCCESS':
                $orderService->updateStatus($order, null, Order::DONE);

                $note = strtolower($productItem->product->category) === Product::VOUCHER ? $request->voucher : "Nickname : $request->nickname - $request->status_desc";

                $orderService->updateNote($order, $note);
                $orderService->updateCapital($order, $request->total_price);
                break;
            case 'REFUNDED';
                $orderService->updateStatus($order, null, Order::REFUNDED);
                $orderService->updateNote($order, $request->status_desc);

                if ($request->payment_method === PaymentMethod::SALDO) {
                    $balance = Balance::where('user_id', $order->user_id)->first();

                    BalanceService::update($balance, [
                        'balanceable_type' => Order::class,
                        'balanceable_id' => $order->id,
                        'amount' => $order->total_price,
                        'description' => "Refund $order->code"
                    ]);
                }
                break;
            default:
                return api_status_warning('Invalid request status');
        }

        if ($order->cust_email) {
            // \Mail::to($order->cust_email)->queue(new SendOrderNotif($order));
        }

        return api_status_ok($order);
    }


    public function setOrderSettlement($order, $orderService)
    {
        if ($order->payment_status != Order::SETTLEMENT) {
            $orderService->updateStatus($order, Order::SETTLEMENT, Order::INPROCESS);

            if ($order->productItem->product->category == ProductConstant::VOUCHER) {
              $orderService->sendVoucher($order);
            } else {
              if ($order->cust_email) {
                \Mail::to($order->cust_email)->send(new SendOrderNotif($order));
              }
            }

            $orderService->createVexaOrder($order);

            $orderService->sendSettlementNotif($order);

            return \api_status_ok(transformer($order, new OrderTransformer));
        }

        return \api_status_ok('Order sudah terbayar');
    }

    public function setOrderFailed($order, $orderService)
    {
        $orderService->updateStatus($order, null, Order::CANCELED);

        if ($order->cust_email) {
            \Mail::to($order->cust_email)->send(new SendOrderNotif($order));
        }

        return \api_status_ok(transformer($order, new OrderTransformer));
    }

    public function setOrderExpired($order, $orderService)
    {
        $orderService->updateStatus($order, null, Order::EXPIRED);

        if ($order->cust_email) {
            \Mail::to($order->cust_email)->send(new SendOrderNotif($order));
        }

        return \api_status_ok(transformer($order, new OrderTransformer));
    }

    public function checkNickname()
    {
        $productItem = ProductItem::find(request('product_item_id'));

        if (!$productItem) {
            return "product not found";
        }

        if (in_array($productItem->product->name, ['Free Fire', 'Mobile Legends'])) {
            $checkNickname = Http::get(config('array.vexa.url').'/check-nickname', [
                'customer_no' => CustAccountService::idExtractor($productItem->product->name, request('cust_account')),
                'game' => $productItem->product->name
            ]);

            $checkNickname = json_decode($checkNickname->collect());

            if (empty($checkNickname->name)) {
                return api_status_ok([
                    'nickname' => null,
                    'error' => 'ID yang anda masukkan salah, cek kembali ID anda atau baca petunjuk pengisian ID yang berada bawah ini'
                ]);
            }

            return api_status_ok([
                'nickname' => $checkNickname->name,
                'error' => null
            ]);
        }

        return api_status_ok([
            'nickname' => null,
            'error' => null
        ]);
    }
}
