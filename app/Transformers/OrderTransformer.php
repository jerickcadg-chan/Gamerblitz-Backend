<?php

namespace App\Transformers;

use App\Models\Order;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\NullResource;
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
     * @param Order $order
     * @return array
     */
    public function transform(Order $order): array
    {
        return [
            'id' => $order->id,
            'code' => $order->code,
            'created_at' => parse_date_time_full($order->created_at),
            'created_at_raw' => $order->created_at,
            'created_at_simple' => parse_date($order->created_at),
            'cust_email' => $order->cust_email,
            'cust_phone_number' => $order->cust_phone_number,
            'cust_account' => json_decode($order->cust_account),
            'payment_method' => $order->payment_method,
            'status' => $order->status,
            'qty' => $order->qty,
            'price' => currency_format($order->price, $order->currency_code),
            'discount_price' => currency_format($order->discount_price, $order->currency_code),
            'turnover' => currency_format($order->turnover, $order->currency_code),
            'admin_fee' => currency_format($order->admin_fee, $order->currency_code),
            'total_price' => currency_format($order->total_price, $order->currency_code),
            'discount_name' => $order->discount->name ?? null,
            'payment_url' => $order->payment_url,
            'payment_code' => $order->payment_code,
            'payment_id' => $order->payment_id,
            'expired_at' => parse_date_time_full($order->expired_at),
            'expired_at_raw' => $order->expired_at,
            'note' => $order->note,
        ];
    }

    public function includeProductItem(Order $order): Item
    {
        return $this->item($order->productItem, new ProductItemTransformer);
    }

    public function includeProduct(Order $order): Item
    {
        return $this->item($order->productItem->product, new ProductTransformer);
    }

    public function includeVouchers(Order $order): NullResource|Collection
    {
        if ($order->vouchers) {
            return $this->collection($order->vouchers, new VoucherTransformer);
        }

        return $this->null();
    }

    public function includeUser(Order $order): NullResource|Item
    {
        if ($order->user) {
            return $this->item($order->user, new UserTransformer);
        }

        return $this->null();
    }
}
