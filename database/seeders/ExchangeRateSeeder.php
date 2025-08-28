<?php

namespace Database\Seeders;

use App\Models\ExchangeRate;
use Illuminate\Database\Seeder;

class ExchangeRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $rates = [
            ['currency_code' => 'USD', 'rate' => 1.0,    'effective_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['currency_code' => 'PHP', 'rate' => 57.11,  'effective_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['currency_code' => 'IDR', 'rate' => 16353.55,  'effective_at' => $now, 'created_at' => $now, 'updated_at' => $now],
        ];

        ExchangeRate::insert($rates);
    }
}
