<?php

namespace Database\Factories;

use App\Models\Client;
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
            'xendit_callback_token' => $this->faker->uuid(),
            'xendit_token' => $this->faker->uuid(),
        ];
    }

    public function firstOrCreate(string $domain)
    {
        return $this->state(function (array $attributes) use ($domain) {
            return Client::firstOrCreate(
                ['host' => $domain],
                $attributes
            )->toArray();
        });
    }
}
