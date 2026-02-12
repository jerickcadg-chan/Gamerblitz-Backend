<?php

namespace App\Transformers;

use App\Models\Discount;
use League\Fractal\TransformerAbstract;
use App\Models\Product;

class DiscountTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Discount $discount)
    {
        return [
            'id' => $discount->id,
            'name' => $discount->name,
            'code' => $discount->code,
            'description' => $discount->description,
            'nominal' => $discount->nominal,
            'disc_type' => $discount->disc_type,
            'disc_nominal' => $discount->discount,
            'product_type' => $discount->product_type,
            'start_date' => $discount->start_date,
            'end_date' => $discount->end_date,
            'is_active' => $discount->is_active,
            'maximum' => $discount->maximum,
            'used' => $discount->used,
        ];
    }

    public function includeProductItems(Product $product)
    {
        if (is_null($product->productItems)) {
            return $this->null();
        }

        return $this->collection($product->productItems, new ProductItemTransformer);
    }
}
