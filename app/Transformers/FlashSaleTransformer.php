<?php

namespace App\Transformers;

use App\Models\FlashSale;
use League\Fractal\TransformerAbstract;

class FlashSaleTransformer extends TransformerAbstract
{
    public function __construct(protected float $exchangeRate = 1.0)
    {
    }

    /**
     * List of resources to automatically include
     */
    protected array $defaultIncludes = [
        'product_item',
        'product',
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
        $percentOff = 0;
        if ($flashSale->productItem->real_price > 0) {
            $percentOff = (($flashSale->productItem->real_price - $flashSale->price)
                           / $flashSale->productItem->real_price) * 100;
        }
        return [
            'id' => $flashSale->id,
            'name' => $flashSale->name,
            'stock' => $flashSale->stock,
            'price' => sprintf("%.2f", $flashSale->price * $this->exchangeRate),
            'percent_off' => round($percentOff),
        ];
    }

    public function includeProductItem(FlashSale $flashSale)
    {
        return $this->item($flashSale->productItem, new ProductItemTransformer($this->exchangeRate));
    }

    public function includeProduct(FlashSale $flashSale)
    {
        return $this->item($flashSale->productItem->product, new ProductTransformer());
    }
}
