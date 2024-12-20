<?php

namespace Database\Factories;

use App\Constants\ProductConstant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'code' => $this->faker->unique()->numerify('PRD-#####'),
            'category' => $this->faker->randomElement(ProductConstant::getValues()),
            'description' => $this->faker->sentence(),
            'company' => $this->faker->company(),
            'how_to_order' => $this->faker->sentence(),
            'input_format' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'markup_reseller' => $this->faker->randomFloat(2, 0, 1),
            'markup_user' => $this->faker->randomFloat(2, 0, 1),
        ];
    }
}
