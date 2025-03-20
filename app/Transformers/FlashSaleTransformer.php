<?php

namespace App\Transformers;

use App\Models\FlashSale;
use App\Models\FlashSaleProductItem;
use League\Fractal\TransformerAbstract;

class FlashSaleTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     */
    protected array $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     */
    protected array $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(FlashSale $flashSale)
    {
        return [
            'id' => $flashSale->id,
            'name' => $flashSale->name,
            'start_date' => $flashSale->start_date->format('Y-m-d H:i:s'),
            'end_date' => $flashSale->end_date->format('Y-m-d H:i:s'),
            'items' => $flashSale->items->map(function (FlashSaleProductItem $item) {
                return [
                    'id' => $item->id,
                    'slug' => $item->productItem?->product?->slug,
                    'flash_price' => (double) $item->price,
                    'stock' => (float) $item->stock,
                    'real_price' => $item->productItem?->real_price,
                    'product_name' => $item->productItem?->product?->name,
                    'product_item' => [
                        'id' => $item->productItem?->id,
                        'name' => $item->productItem?->name,
                        'code' => $item->productItem?->code,
                        'cover' => $item->productItem?->product?->product_cover,
                        'picture' => $item->productItem?->product?->product_picture,
                    ],
                ];
            })->toArray(),
        ];
    }
}
