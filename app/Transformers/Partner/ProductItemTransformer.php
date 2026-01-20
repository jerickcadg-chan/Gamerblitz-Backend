<?php

namespace App\Transformers\Partner;

use App\Constants\CurrencyConstant;
use App\Models\ProductItem;
use App\Models\Setting;
use App\Transformers\ProductTransformer;
use League\Fractal\TransformerAbstract;

class ProductItemTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(ProductItem $productItem)
    {
        $meta = $productItem->productItemCategoryMeta;
        $metaCurrency = CurrencyConstant::metadata(Setting::getBaseCurrency());
        $price = number_format($productItem->real_price, 2, $metaCurrency['decimal'] ?? '.', ',');

        return [
            'code' => $productItem->id,
            'name' => $productItem->name,
            'stock' => $productItem?->stock ?? '∞',
            'price' => $price,
            'category' => $meta?->productItemCategory->name,
            'picture' => $meta?->picture->url,
            'input_format' => json_decode($productItem->product->input_format),
        ];
    }
}
