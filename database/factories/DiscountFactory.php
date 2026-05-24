<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Discount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
* @extends Factory<Discount>
*/
class DiscountFactory extends Factory
{
    protected $model = Discount::class;

    public function definition()
    {
        return [
            'client_id' => Client::factory(),
            'name' => $this->faker->word,
            'code' => strtoupper(Str::random(10)),
            'description' => $this->faker->sentence,
            'nominal' => $this->faker->numberBetween(1000, 10000),
            'disc_type' => $this->faker->randomElement(['percentage', 'nominal']),
            'product_type' => $this->faker->randomElement([Discount::ALL, Discount::PRODUCT_TYPE, Discount::PRODUCT_ITEM]),
            'start_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'end_date' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
            'is_active' => $this->faker->boolean,
            'maximum' => $this->faker->numberBetween(1, 100),
            'used' => $this->faker->numberBetween(0, 100),
        ];
    }
}
