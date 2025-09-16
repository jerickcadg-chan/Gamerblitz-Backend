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

            'price' => $order->price,
            'discount_price' => $order->discount_price,
            'turnover' => $order->turnover,
            'admin_fee' => $order->admin_fee,
            'total_price' => $order->total_price,

            'converted_price' => $order->converted_price,
            'converted_discount_price' => $order->converted_discount_price,
            'converted_turnover' => $order->converted_turnover,
            'converted_admin_fee' => $order->converted_admin_fee,
            'converted_total_price' => $order->converted_total_price,

            'currency_code' => $order->currency_code,
            'converted_currency_code' => $order->converted_currency_code,

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
