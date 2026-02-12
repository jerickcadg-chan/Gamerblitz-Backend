<?php

namespace Database\Factories;

use App\Models\FlashSaleProductItem;
use App\Models\ProductItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FlashSaleProductItem>
 */
class FlashSaleProductItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'price' => 40000,
            'stock' => 100,
            'product_item_id' => ProductItem::factory(),
        ];
    }
}
