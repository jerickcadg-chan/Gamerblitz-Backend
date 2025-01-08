<?php

namespace App\Transformers;

use App\Models\Order;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     */
    protected array $defaultIncludes = [
        'productItem', 'product', 'user',
    ];

    /**
     * List of resources possible to include
     */
    protected array $availableIncludes = [
        'vouchers',
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($order)
    {
        return [
            'id' => $order->id,
            'code' => $order->code,
            'created_at' => parse_date_time_full($order->created_at),
            'created_at_simple' => parse_date($order->created_at),
            'cust_email' => $order->cust_email,
            'cust_phone_number' => $order->cust_phone_number,
            'cust_account' => $order->productItem->product->slug !== 'mobile-legends-joki-rank' ? json_decode($order->cust_account ): '',
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status_translated,
            'order_status' => $order->order_status_translated,
            'order_status_raw' => $order->order_status_raw,
            'qty' => $order->qty,
            'price' => rp_format($order->price),
            'discount_price' => rp_format($order->discount_price),
            'admin_fee' => rp_format($order->admin_fee),
            'total_price' => rp_format($order->total_price),
            'discount_name' => $order->discount->name ?? null,
            'payment_url' => $order->payment_url,
            'payment_url_full' => $order->payment_url_full,
            'payment_code' => $order->payment_code,
            'payment_id' => $order->payment_id,
            'expired_at' => parse_date_time_full($order->expired_at),
            'expired_at_raw' => $order->expired_at,
            'note' => $order->note,
        ];
    }

    public function includeProductItem(Order $order)
    {
        return $this->item($order->productItem, new ProductItemTransformer);
    }

    public function includeProduct(Order $order)
    {
        return $this->item($order->productItem->product, new ProductTransformer);
    }

    public function includeVouchers(Order $order)
    {
        if ($order->vouchers) {
            return $this->collection($order->vouchers, new VoucherTransformer);
        }

        return $this->null();
    }

    public function includeUser(Order $order)
    {
        if ($order->user) {
            return $this->item($order->user, new UserTransformer);
        }

        return $this->null();
    }
}
