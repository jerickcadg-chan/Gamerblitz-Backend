<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'host' => str(config('app.url'))->after('https://')->after('http://'),
            'logo' => $this->faker->imageUrl(),
            'description' => $this->faker->sentence(),
            'user_token' => $this->faker->uuid(),
        ];
    }
}
