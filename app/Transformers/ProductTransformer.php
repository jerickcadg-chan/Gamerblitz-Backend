<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Product;

class ProductTransformer extends TransformerAbstract
{
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
            'category' => $product->category,
            'company' => $product->company,
            'input_format' => json_decode($product->input_format),
            'description' => $product->description,
            'how_to_order' => $product->how_to_order,
            'status' => $product->status,
            'picture' => $product->picture->url,
        ];
    }
}
