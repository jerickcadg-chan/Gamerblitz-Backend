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
        'productItem', 'product', 'user', 'paymentMethod',
    ];

    /**
     * List of resources possible to include
     */
    protected array $availableIncludes = [
        'vouchers', 'reviews'
    ];

    /**
     * A Fractal transformer.
     *
     * @param Order $order
     * @return array
     */
    public function transform(Order $order): array
    {
        // <CHANGE> Handle ecommerce orders - include ecommerce data if available
        $data = [
            'id' => $order->id,
            'code' => $order->code,
            'created_at' => parse_date_time_full($order->created_at),
            'created_at_raw' => $order->created_at,
            'created_at_simple' => parse_date($order->created_at),
            'cust_email' => $order->cust_email,
            'cust_phone_number' => $order->cust_phone_number,
            'cust_account' => json_decode($order->cust_account),
            'payment_method_id' => $order->payment_method_id,
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
            'payment_descriptor' => $order->payment_descriptor,
            'payment_id' => $order->payment_id,
            'expired_at' => $order->expired_at,
            'expired_at_raw' => $order->expired_at,
            'note' => $order->note,
            // <CHANGE> Add ecommerce order flag
            'is_ecommerce_order' => !empty($order->ecommerce_order_id),
            'ecommerce_order_id' => $order->ecommerce_order_id,
        ];

        return $data;
    }

    // <CHANGE> Handle null productItem for ecommerce orders
    public function includeProductItem(Order $order): Item|NullResource
    {
        // Skip productItem for ecommerce orders or if productItem is null
        if (!empty($order->ecommerce_order_id) || !$order->productItem) {
            return $this->null();
        }
        return $this->item($order->productItem, new ProductItemTransformer);
    }

    // <CHANGE> Handle null product for ecommerce orders
    public function includeProduct(Order $order): Item|NullResource
    {
        // Skip product for ecommerce orders or if productItem/product is null
        if (!empty($order->ecommerce_order_id) || !$order->productItem || !$order->productItem->product) {
            return $this->null();
        }
        return $this->item($order->productItem->product, new ProductTransformer);
    }

    public function includeVouchers(Order $order): NullResource|Collection
    {
        if ($order->vouchers) {
            return $this->collection($order->vouchers, new VoucherTransformer);
        }

        return $this->null();
    }

    // <CHANGE> Handle null paymentMethod
    public function includePaymentMethod(Order $order): Item|NullResource
    {
        if ($order->paymentMethod) {
            return $this->item($order->paymentMethod, new PaymentMethodTransformer());
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

    public function includeReviews(Order $order): Collection|NullResource
    {
        if ($order->reviews) {
            return $this->collection($order->reviews, new ReviewTransformer());
        }

        return $this->null();
    }
}