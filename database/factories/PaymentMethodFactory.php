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
                'vendor' => PaymentMethod::QRIS,
                'name' => 'xendit',
                'admin_fee' => 0,
                'admin_type' => 'percentage',
                'slug' => '#qris',
            ];
        });
    }
}
