<?php

namespace App\Transformers;

use App\Constants\DefaultRole;
use League\Fractal\TransformerAbstract;
use App\Models\ProductItem;

class ProductItemTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'product'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($productItem)
    {
        return [
            'id' => $productItem->id,
            'name' => $productItem->name,
            'stock' => $productItem->stock,
            'original_price' => rp_format($productItem->real_price),
            'discount_price' => rp_format($productItem->discount_price),
            'total_price' => rp_format($productItem->total_price),
            'original_price_raw' => (int)$productItem->real_price,
            'discount_price_raw' => $productItem->discount_price,
            'total_price_raw' => $productItem->total_price,
        ];
    }

    public function includeProduct(ProductItem $productItem)
    {
        return $this->item($productItem->product, new ProductTransformer);
    }
}
