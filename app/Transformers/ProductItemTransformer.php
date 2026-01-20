<?php

namespace App\Transformers;

use App\Models\ProductItem;
use App\Models\Setting;
use League\Fractal\TransformerAbstract;

class ProductItemTransformer extends TransformerAbstract
{
    public float $exchangeRate;

    public function __construct(float $exchangeRate = 1)
    {
        $this->exchangeRate = $exchangeRate;
    }
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
        $meta = $productItem->productItemCategoryMeta;
        $percentOff = ($productItem->discount_price > 0 && $productItem->real_price > 0)
            ? round(($productItem->discount_price / $productItem->real_price) * 100)
            : 0;

        return [
            'id' => $productItem->id,
            'code' => $productItem->code,
            'name' => $productItem->name,
            'stock' => $productItem->stock,
            'original_price' => $productItem->real_price * $this->exchangeRate,
            'discount_price' => $productItem->discount_price * $this->exchangeRate,
            'total_price' => $productItem->total_price * $this->exchangeRate,
            'percent_off' => $percentOff,
            'source_original_price' => $productItem->real_price,
            'source_discount_price' => $productItem->discount_price,
            'source_total_price' => $productItem->total_price,
            'source_currency' => Setting::getBaseCurrency(),
            'category' => $meta?->productItemCategory->name,
            'picture' => $meta?->picture->url,
        ];
    }

    public function includeProduct(ProductItem $productItem)
    {
        return $this->item($productItem->product, new ProductTransformer());
    }
}
