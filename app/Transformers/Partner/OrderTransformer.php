<?php

namespace App\Transformers\Partner;

use App\Models\Order;
use App\Transformers\ProductItemTransformer;
use App\Transformers\ProductTransformer;
use App\Transformers\UserTransformer;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\NullResource;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param Order $order
     * @return array
     */
    public function transform(Order $order): array
    {
        return [
            'id' => $order->id,
            'code' => $order->code,
            'created_at' => parse_date_time_full($order->created_at),
            'item' => [
                'id' => $order->productItem->id,
                'name' => $order->productItem->full_name,
            ],
            'cust_account' => json_decode($order->cust_account),
            'status' => $order->status,
            'qty' => $order->qty,

            'price' => $order->price,
            'discount_price' => $order->discount_price,
            'admin_fee' => $order->admin_fee,
            'total_price' => $order->total_price,
            'payment_code' => $order->payment_code,

            'expired_at' => $order->expired_at,
            'note' => $order->note,
        ];
    }
}