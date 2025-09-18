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
        $baseCurrency = config('app.base_currency', 'IDR');

        // key = target_currency, value = list of [currency_code => rate]
        $rates = [
            'IDR' => [
                'USD' => 16353.55,
                'PHP' => 287.15,
                'IDR' => 1.0, // base to itself
            ],
            'USD' => [
                'IDR' => 0.000061,
                'PHP' => 0.0175,
                'USD' => 1.0,
            ],
            'PHP' => [
                'IDR' => 0.00348,
                'USD' => 57.11,
                'PHP' => 1.0,
            ],
        ];

        // pick only the target_currency = baseCurrency
        $filtered = collect($rates[$baseCurrency] ?? [])->map(function ($rate, $currencyCode) use ($baseCurrency, $now) {
            return [
                'currency_code'   => $currencyCode,
                'target_currency' => $baseCurrency,
                'rate'            => $currencyCode === $baseCurrency ? 1.0 : $rate,
                'effective_at'    => $now,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        })->values()->toArray();

        ExchangeRate::truncate();
        ExchangeRate::upsert(
            $filtered,
            ['currency_code', 'target_currency', 'effective_at'],
            ['rate']
        );
    }
}
