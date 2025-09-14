<?php

namespace App\Transformers;

use App\Models\FlashSale;
use League\Fractal\TransformerAbstract;

class FlashSaleTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     */
    protected array $defaultIncludes = [
        'productItem'
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
            'stock' => $flashSale->stock,
            'price' => $flashSale->price,
        ];
    }

    public function includeProductItem(FlashSale $flashSale)
    {
        return $this->item($flashSale->productItem, new ProductItemTransformer());
    }
}
