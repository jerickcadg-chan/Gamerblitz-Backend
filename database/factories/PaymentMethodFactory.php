<?php

namespace Database\Factories;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentMethod>
 */
class PaymentMethodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vendor' => 'manual',
            'name' => 'saldo',
            'admin_fee' => 0,
            'admin_type' => 'percentage',
            'slug' => '#saldo',
        ];
    }

    public function qris(): PaymentMethodFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'vendor' => 'xendit',
                'name' => 'qris',
                'admin_fee' => 2.0,
                'admin_type' => 'percentage',
                'slug' => '#qris',
            ];
        });
    }

    public function va(): PaymentMethodFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'vendor' => 'xendit',
                'name' => 'va',
                'admin_fee' => 5000.00,
                'admin_type' => 'nominal',
                'slug' => '#va',
            ];
        });
    }
}
