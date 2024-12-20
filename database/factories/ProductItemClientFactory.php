<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ProductItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductItemClient>
 */
class ProductItemClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_item_id' => ProductItem::factory(),
            'client_id' => Client::factory(),
            'margin' => $this->faker->randomFloat(2, 0, 15),
            'is_active' => true,
        ];
    }
}
