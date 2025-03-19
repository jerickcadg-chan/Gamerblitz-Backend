<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductItem>
 */
class ProductItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'name' => $this->faker->word(),
            'code' => $this->faker->unique()->numerify('PRD###'),
            'stock' => $this->faker->numberBetween(0, 1000),
            'price' => $this->faker->randomFloat(2, 1, 1000),
            'price_reseller' => $this->faker->randomFloat(2, 1, 1000),
            'capital_silver' => $this->faker->randomFloat(2, 1, 1000),
            'capital_gold' => $this->faker->randomFloat(2, 1, 1000),
            'capital_platinum' => $this->faker->randomFloat(2, 1, 1000),
            'capital_diamond' => $this->faker->randomFloat(2, 1, 1000),
            "type" => ['account', 'topup'][array_rand(['account', 'topup'])],
        ];
    }
}
