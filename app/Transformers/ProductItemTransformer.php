<?php

namespace App\Transformers;

use App\Models\ProductItem;
use League\Fractal\TransformerAbstract;

class ProductItemTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     */
    protected array $defaultIncludes = [
    ];

    /**
     * List of resources possible to include
     */
    protected array $availableIncludes = [
        'product',
    ];

    /**
     * A Fractal transformer.
     *
     * @param ProductItem $productItem
     * @return array
     */
    public function transform(ProductItem $productItem): array
    {
        return [
            'id' => $productItem->id,
            'code' => $productItem->code,
            'name' => $productItem->name,
            'stock' => $productItem->stock,
            'product_item_category_id' => $productItem->product_item_category_id,
            'original_price' => $productItem->real_price,
            'discount_price' => $productItem->discount_price,
            'total_price' => $productItem->total_price,
        ];
    }

    public function includeProduct(ProductItem $productItem)
    {
        return $this->item($productItem->product, new ProductTransformer);
    }
}
