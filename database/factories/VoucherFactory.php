<?php

namespace Database\Factories;

use App\Models\Voucher;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoucherFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Voucher::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product_item_id' => 13,
            'serial_number' => rand(100000, 999999),
            'password' => rand(1000000000000000, 9999999999999999),
            'capital' => 17300,
            'vendor' => 'Unipin',
            'status' => 'ready',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
