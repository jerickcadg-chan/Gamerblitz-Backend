<?php

namespace App\Transformers;

use App\Models\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
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
        'productItems',
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Product $product)
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'category' => $product->productCategory->name,
            'company' => $product->company,
            'input_format' => json_decode($product->input_format),
            'description' => $product->description,
            'how_to_order' => $product->how_to_order,
            'status' => $product->status,
            'picture' => $product->product_picture,
            'cover' => $product->product_cover,
            'ordering' => $product->ordering,
            'provider_country' => $product->provider_country,
            'product_category_items'=> $product->productItemCategories,
            'meta_title' => $product->meta_title,
            'meta_keyword' => $product->meta_keyword,
            'meta_description' => $product->meta_description
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
