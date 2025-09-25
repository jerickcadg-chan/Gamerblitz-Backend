<?php

namespace App\Http\Controllers\Api;

use App\Constants\StatusConst;
use App\Data\LapakGaming\OrderCallbackPayload;
use App\Data\LapakGaming\ProductUpdateCallbackPayload;
use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\ProductItem;
use App\Services\BalanceService;
use App\Services\OrderService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class LapakGamingController extends Controller
{
    public function productUpdateCallback(ProductUpdateCallbackPayload $payload): Response
    {
        $data = $payload->data;
        $meta = $payload->meta;

        $productItem = ProductItem::where('code', $data->code)->first();

        if (!$productItem) {
            Log::info('LapakGaming: product Item not stored', [
                'data' => $payload->toArray()
            ]);
            // acknowledge callback, otherwise they will retry 3 times
            return response([
                'message' => 'SKIPPED',
                'reason' => 'Product item not stored',
            ], 200);
        }

        if ($productItem->sync_at->timestamp > $meta->unix_timestamp) {
            // out of date, skip
            return response([
                'message' => 'SKIPPED',
                'reason' => 'Data out of date',
            ], 200);
        }

        $product = $productItem->product;

        $marginPublicUser = $productItem->margin ?: $product->markup_user;
        $marginSilver = $productItem->margin_silver ?: $product->markup_reseller_silver;
        $marginGold = $productItem->margin_gold ?: $product->markup_reseller_gold;
        $marginVip = $productItem->margin_vip ?: $product->markup_reseller_vip;

        $productItem->capital = $data->price;
        $productItem->margin = $marginPublicUser;
        $productItem->margin_silver = $marginSilver;
        $productItem->margin_gold = $marginGold;
        $productItem->margin_vip = $marginVip;
        $productItem->status = $data->status === 'available' ? 'active' : 'empty';
        $productItem->sync_at = now();
        $productItem->save();
        return response([
            'message' => 'SUCCESS',
        ], 200);
    }

    public function orderCallback(OrderCallbackPayload $payload, OrderService $orderService): Response
    {
        $log = Log::channel('lapakgaming');

        $order = Order::query()
            ->where('code', $payload->data->reference_id)
            ->where('provider_ref', $payload->data->tid)
            ->first();

        if (!$order) {
            $log->error("Order callback received but order not found", [
                'reference_id' => $payload->data->reference_id,
                'tid' => $payload->data->tid,
                'payload' => $payload->toArray(),
            ]);

            return response([
                'message' => 'FAILED',
                'reason' => 'Invalid ref id',
            ]);
        }

        $rawPayload = json_encode($payload, JSON_PRETTY_PRINT);

        switch ($payload->data->status) {
            case "SUCCESS":
                $orderService->updateStatus($order, StatusConst::SUCCESS, $rawPayload);
                break;
            case "REFUNDED":
                $orderService->updateStatus($order, StatusConst::FAILED, $rawPayload);
                if ($order->payment_method === PaymentMethod::BALANCE) {
                    $balance = Balance::where('user_id', $order->user_id)->first();

                    BalanceService::update($balance, [
                        'balanceable_type' => Order::class,
                        'balanceable_id' => $order->id,
                        'amount' => $order->total_price,
                        'description' => "Refund $order->code"
                    ]);
                }
                break;
            case "PENDING":
                $log->error("Order still pending on callback", [
                    'order_id' => $order->id,
                    'payload' => $payload->toArray(),
                ]);
            default:
                break;
        }

        $transactions = collect($payload->data->transactions ?? []);

        $order->serial_number = $transactions->pluck('voucher_code')
            ->filter()
            ->implode(',');

        $order->note = $transactions->pluck('note')
            ->filter()
            ->implode(',');

        $order->save();

        return response([
            'message' => 'SUCCESS',
        ], 200);
    }
}
