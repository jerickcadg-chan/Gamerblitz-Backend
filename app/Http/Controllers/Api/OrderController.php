<?php

namespace App\Http\Controllers\Api;

use App\Constants\StatusConst;
use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Constants\ProductItemTypeConstant;
use App\Models\Setting;
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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public $userId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->userId = Auth::id() ?? null;

            return $next($request);
        });
    }

    public function index()
    {
        $filter = $this->filter();
        $itemNameFilter = Arr::where($this->filter(), function ($value) {
            return $value["target"] === 'item_name';
        });
        if (count($itemNameFilter) > 0) {
            $itemNameProductFilter = [
                'type'   => '$has',
                'target' => 'productItem',
                'value' => [
                    [
                        'type' => '$has',
                        'target' => 'product',
                        'value' => array_values($itemNameFilter),
                    ]
                ]
            ];
            $filter = Arr::except($filter, array_keys($itemNameFilter));
            $filter = array_merge([$itemNameProductFilter], $filter);
        }

        $orders = Order::with('user', 'productItem.product')->latest()
            ->filter($filter)
            ->where('user_id', $this->userId)
            ->when(request('order_code'), function ($query) {
                return $query->where('code', 'like', '%' . request('order_code') . '%');
            });

        return api_status_ok(paginateTransformer($orders, new OrderTransformer, [], \request('limit') ?? 10));
    }

    public function stats()
    {
        $orders = Order::where('user_id', $this->userId)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN order_status = 'done' THEN 1 ELSE 0 END), 0) as `done`,
                COALESCE(SUM(CASE WHEN order_status = 'in-process' THEN 1 ELSE 0 END), 0) as `in-process`,
                COALESCE(SUM(CASE WHEN order_status = 'expired' THEN 1 ELSE 0 END), 0) as `expired`,
                COALESCE(SUM(CASE WHEN order_status = 'canceled' THEN 1 ELSE 0 END), 0) as `canceled`
            ")
            ->first()
            ->toArray();

        return api_status_ok($orders);
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

    public function store(OrderRequest $request, OrderService $orderService)
    {
        try {
            $order = $orderService->store($request);

            if (is_array($order)) {
                throw ValidationException::withMessages($order);
            }

            if (is_string($order)) {
                return api_status_warning($order);
            }

            return api_status_ok(transformer($order, new OrderTransformer));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return \api_status_error($e);
        }
    }

    public function getPaymentMethods()
    {
        $paymentMethod = PaymentMethod::filter($this->filter())->orderBy('created_at', 'asc')->get();

        return api_status_ok(transformer($paymentMethod, new PaymentMethodTransformer));
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
        $xenditCallbackKey = Setting::getByKey(Setting::KEY_XENDIT_CALLBACK_KEY);
        $hCallbackToken = $request->header('x-callback-token');
        $validCallbackKey = $xenditCallbackKey == $hCallbackToken;
        if (!$validCallbackKey) {
            return api_status_warning("callback token didn't register yet, or invalid token!!!!");
        }

        $code = isset($request->qr_code) ? $request->qr_code['external_id'] : $request->external_id;

        $order = Order::where('code', $code)->first();

        if (is_null($request->id) || is_null($order)) {
            return api_status_warning('Order not found');
        }

        switch ($request->status) {
            case 'COMPLETED':
            case 'PAID':
                $orderService->processOrder($order);
                $orderService->updateStatus($order, StatusConst::ON_PROCESS);
                break;

            case 'EXPIRED':
                $orderService->updateStatus($order, StatusConst::EXPIRED);
                break;

            default:
                return api_status_warning('Invalid request status');
        }

        return api_status_ok(transformer($order, new OrderTransformer));
    }

    public function agenCallback(Request $request, OrderService $orderService)
    {
        // TODO: fix auth
        $order = Order::where('code', $request->code)->first();

        if (empty($order)) {
            return api_status_warning("Wrong number!");
        }

        $productItem = $order->productItem;

        switch ($request->status) {
            case 'SUCCESS':
            case 'Sukses':
                $orderService->updateStatus($order, null, Order::DONE);

                $note = strtolower($productItem->product->category) === Product::VOUCHER ? $request->voucher : "Nickname : $request->nickname - $request->status_desc";

                $orderService->updateNote($order, $note);
                $orderService->updateCapital($order, $request->total_price);
                break;
            case 'REFUNDED';
            case 'Gagal';
                $orderService->updateStatus($order, null, Order::REFUNDED);
                $orderService->updateNote($order, $request->status_desc);

                if ($request->payment_method === PaymentMethod::BALANCE) {
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

        return api_status_ok($order);
    }

    public function checkNickname()
    {
        if (in_array(request('game'), ['Free Fire', 'Mobile Legends'])) {
            $checkNickname = Http::get(config('array.mitra-gamers.url') . '/api/check-nickname', [
                'customer_no' => request('customer_no'),
                'game' => request('game')
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
            'nickname' => "Game not supported",
            'error' => null
        ]);
    }
}
