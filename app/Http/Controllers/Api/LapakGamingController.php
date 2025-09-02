<?php

namespace App\Http\Controllers\Api;

use App\Data\LapakGaming\OrderCallbackPayload;
use App\Data\LapakGaming\ProductUpdateCallbackPayload;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductItem;
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
        $order = Order::query()
            ->where('code', $payload->data->reference_id)
            ->where('provider_ref', $payload->data->tid)
            ->first();

        if (!$order) {
            return response([
                'message' => 'FAILED',
                'reason' => 'Invalid ref id',
            ]);
        }

        $productItem = $order->productItem;

        switch ($payload->data->status) {
            case "SUCCESS":
                $orderService->updateStatus($order, null, Order::DONE);
                break;
            case "REFUNDED":
                $orderService->updateStatus($order, null, Order::REFUNDED);
                break;
            case "PENDING":
            default:
                break;
        }

        return response([
            'message' => 'SUCCESS',
        ], 200);
    }
}
