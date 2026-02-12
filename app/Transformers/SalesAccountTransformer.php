<?php

namespace App\Transformers;

use App\Models\Account;
use League\Fractal\TransformerAbstract;

class SalesAccountTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Account $account)
    {
        return [
            'id' => $account->id,
            'slug' => $account->slug,
            'product_item_id' => $account->product_item_id,
            'title' => $account->title,
            'description' => $account->description,
            'code' => $account->code,
            'winrate' => $account->winrate,
            'skin' => $account->skin,
            'heroes' => $account->heroes,
            'discount_type' => $account->discount_type,
            'discount_amount' => (float) $account->discount_amount,
            'discount' => $account->discount,
            'real_price' => (float) $account->productItem->price,
            'discount_price' => $account->price,
            'cover_images' => $account->pictures->map(fn ($picture) => $picture->url),
            'how_to_order' => $account->productItem->product->how_to_order,
            'created_at' => $account->created_at,
            'updated_at' => $account->updated_at,
        ];
    }
}
