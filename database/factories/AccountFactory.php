<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_item_id' => \App\Models\ProductItem::factory(),
            'client_id' => \App\Models\Client::factory(),
            'title' => $this->faker->word(),
            'information' => encrypt($this->faker->sentence()),
            'description' => $this->faker->sentence(),
            'slug' => $this->faker->slug(),
            'code' => $this->faker->unique()->numerify('ACC-#####'),
            'winrate' => $this->faker->randomFloat(2, 0, 1),
            'skin' => $this->faker->randomFloat(2, 0, 1),
            'heroes' => $this->faker->randomFloat(2, 0, 1),
        ];
    }
}
