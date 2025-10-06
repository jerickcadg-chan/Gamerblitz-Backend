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
use App\Models\Product;
use App\Transformers\OrderTransformer;
use App\Transformers\PaymentMethodTransformer;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

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

        $orders = Order::with('user', 'productItem.product', 'paymentMethod')->latest()
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
                COALESCE(SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END), 0) as `success`,
                COALESCE(SUM(CASE WHEN status = 'on-process' THEN 1 ELSE 0 END), 0) as `on-process`,
                COALESCE(SUM(CASE WHEN status = 'expired' THEN 1 ELSE 0 END), 0) as `expired`,
                COALESCE(SUM(CASE WHEN status = 'refunded' THEN 1 ELSE 0 END), 0) as `refunded`
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
        }

        return api_status_ok(transformer($order, new OrderTransformer(), ['reviews']));
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
        $paymentMethod = PaymentMethod::filter($this->filter())
            ->orderByRaw('COALESCE(ordering, 999999) ASC')
            ->isActive()
            ->get();

        return api_status_ok(transformer($paymentMethod, new PaymentMethodTransformer()));
    }

    public function xenditCallback(Request $request, OrderService $orderService, DepositService $depositService)
    {
        $xenditCallbackKey = Setting::getByKey(Setting::KEY_XENDIT_CALLBACK_KEY);
        $hCallbackToken = $request->header('x-callback-token');
        $validCallbackKey = $xenditCallbackKey == $hCallbackToken;
        if (!$validCallbackKey) {
            Log::info(json_encode($request->header(), JSON_PRETTY_PRINT));
            return api_status_warning("callback token didn't register yet, or invalid token!!!!");
        }

        $payload = PaymentCallbackPayload::from($request->all());
        $paymentId = $payload->data->payment_request_id;
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

    public function hitpayCallback(Request $request, OrderService $orderService, DepositService $depositService)
    {
        $data = $request->all();
        $salt = Setting::getByKey('hitpay_salt_key');
        $hmac = $request->header('hitpay-signature');

        Log::info('HitPay Webhook received', $data);

        if (!$hmac) {
            return response()->json(['message' => 'Missing HMAC'], 400);
        }

        $calculatedHmac = hash_hmac('sha256', json_encode($data), $salt);

        if (!hash_equals($hmac, $calculatedHmac)) {
            Log::warning('HitPay Webhook HMAC mismatch', [
                'expected' => $calculatedHmac,
                'received' => $hmac,
            ]);

            return api_status_warning('Invalid HMAC');
        }

        $reference = $data['payment_request']['reference_number'] ?? null;
        $order = Order::where('code', $reference)->first();
        $deposit = Deposit::where('code', $reference)->first();

        if (!$order && !$deposit) {
            return api_status_warning('Transaction not found');
        }

        if ($order && $data['payment_request']['status'] === 'completed') {
            $orderService->processOrder($order);
            $orderService->updateStatus($order, StatusConst::ON_PROCESS);

            return api_status_ok([
                'order' => transformer($order, new OrderTransformer())
            ]);
        }

        if ($deposit && $data['payment_request']['status'] === 'completed') {
            $depositService->handlePaymentSettlement($deposit);

            return api_status_ok([
                'deposit' => transformer($deposit, new DepositTransformer())
            ]);
        }

        return api_status_ok([
            'message' => 'Payment not completed'
        ]);
    }

    public function billplzCallback(Request $request, OrderService $orderService, DepositService $depositService)
    {
        $data = $request->all();

        Log::info('Billplz Webhook Received', $data);

        $billId = $data['id'] ?? null;
        $state  = $data['state'] ?? null;
        $isPaid = filter_var($data['paid'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if (!$billId) {
            return api_status_warning('Missing bill ID');
        }

        $order   = Order::where('payment_id', $billId)->first();
        $deposit = Deposit::where('payment_id', $billId)->first();

        if (!$order && !$deposit) {
            Log::warning('Billplz Webhook: Transaction not found', $data);
            return api_status_warning('Transaction not found');
        }

        if ($order && $isPaid && $state === 'paid') {
            $orderService->processOrder($order);
            $orderService->updateStatus($order, 'on_process');

            return api_status_ok([
                'order' => transformer($order, new OrderTransformer())
            ]);
        }

        if ($deposit && $isPaid && $state === 'paid') {
            $depositService->handlePaymentSettlement($deposit);

            return api_status_ok([
                'deposit' => transformer($deposit, new DepositTransformer())
            ]);
        }

        return api_status_ok([
            'message' => 'Payment not completed'
        ]);
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
