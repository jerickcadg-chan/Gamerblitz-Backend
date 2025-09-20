<?php

namespace App\Http\Controllers\Api;

use App\Constants\StatusConst;
use App\Data\LapakGaming\CheckUidRequest;
use App\Data\LapakGaming\CheckUidResponse;
use App\Data\Xendit\PaymentCallbackPayload;
use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Services\DepositService;
use App\Transformers\DepositTransformer;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Http\Requests\OrderRequest;
use App\Models\Balance;
use App\Models\Product;
use App\Services\BalanceService;
use App\Transformers\OrderTransformer;
use App\Transformers\PaymentMethodTransformer;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
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

        return api_status_ok(paginateTransformer($orders, new OrderTransformer(), [], \request('limit') ?? 10));
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

            return api_status_ok(transformer($order, new OrderTransformer(), ['vouchers']));
        }

        return api_status_ok(transformer($order, new OrderTransformer()));
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

            return api_status_ok(transformer($order, new OrderTransformer()));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return \api_status_error($e);
        }
    }

    public function getPaymentMethods()
    {
        $paymentMethod = PaymentMethod::filter($this->filter())->orderBy('created_at', 'asc')->get();

        return api_status_ok(transformer($paymentMethod, new PaymentMethodTransformer()));
    }

    public function xenditCallback(Request $request, OrderService $orderService, DepositService $depositService)
    {
        $xenditCallbackKey = Setting::getByKey(Setting::KEY_XENDIT_CALLBACK_KEY);
        $hCallbackToken = $request->header('x-callback-token');
        $validCallbackKey = $xenditCallbackKey == $hCallbackToken;
        if (!$validCallbackKey) {
            return api_status_warning("callback token didn't register yet, or invalid token!!!!");
        }

        $payload = PaymentCallbackPayload::from($request->all());
        $paymentId = $payload->data->payment_id;
        $type = $payload->data->metadata['type'] ?? 'order';

        switch ($type) {
            case 'order':
                $order = Order::where('payment_id', $paymentId)->first();

                if (empty($order)) {
                    return api_status_warning('Order not found');
                }

                switch ($payload->data->status) {
                    case 'SUCCEEDED':
                        $orderService->processOrder($order);
                        $orderService->updateStatus($order, StatusConst::ON_PROCESS);
                        break;

                    case 'EXPIRED':
                        $orderService->updateStatus($order, StatusConst::EXPIRED);
                        break;

                    default:
                        return api_status_warning('Invalid request status');
                }

                return api_status_ok([
                    'order' => transformer($order, new OrderTransformer())
                ]);
            case 'deposit':
                $deposit = Deposit::where('payment_id', $paymentId)->first();

                if (!$deposit) {
                    return api_status_warning("Deposit not found");
                }

                $err = match ($payload->data->status) {
                    'SUCCEEDED' => $depositService->handlePaymentSettlement($deposit),
                    'EXPIRED' => $depositService->handlePaymentExpired($deposit),
                    default => 'Invalid request status'
                };

                if ($err) {
                    return api_status_warning($err);
                }

                return api_status_ok([
                    'deposit' => transformer($deposit, new DepositTransformer())
                ]);
            default:
            break;
        }

        return api_status_warning('Transaction not found');
    }

    public function checkUid()
    {
        $product = Product::where('id', request('product_id'))->firstOrFail();

        $checkUidRequest = new CheckUidRequest(
            $product->provider_code,
            request('user_id'),
            request('additional_id'),
            request('additional_information'),
        );

        if ($product->check_uid !== "active") {
            return api_status_ok([
                'error' => null,
                'name' => null,
            ]);
        }

        $token = Setting::getByKey(Setting::KEY_LAPAKGAMING_API_TOKEN);
        $baseUrl = Setting::getByKey(Setting::KEY_LAPAKGAMING_API_URL);
        $checkUidUrl = $baseUrl . '/api/uid-check';

        $query = http_build_query($checkUidRequest->toArray());

        $response = Http::withToken($token)
            ->withOptions([
                'query' => $query,
            ])
            ->withHeader('X-COUNTRY', $product->provider_country)
            ->post($checkUidUrl);

        if ($response->failed()) {
            return api_status_ok([
                'error' => $response->body(),
                'name' => null,
            ]);
        }

        $checkUidResponse = CheckUidResponse::from($response->json());

        if ($checkUidResponse->code === "SUCCESS") {
            return api_status_ok([
                'error' => null,
                'name' => $checkUidResponse->data->name,
            ]);
        } else {
            return api_status_ok([
                'error' => $checkUidResponse->code,
                'name' => null,
            ]);
        }
    }
}
