<?php

namespace App\Console\Commands;

use App\Models\ExchangeRate;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AutomaticExchangeRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:automatic-exchange-rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatic exchange rates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $apiUrl = Setting::getByKey('exchangerates_api_url');
            $apiKey = Setting::getByKey('exchangerates_api_key');
            $baseCurrency = Setting::getBaseCurrency();

            $exchangeRates = ExchangeRate::select('id', 'currency_code', 'rate', 'effective_at')
                ->orderByDesc('effective_at')
                ->when(request('currency_code'), fn($q) => $q->where('currency_code', 'like', '%' . request('currency_code') . '%'))
                ->get()
                ->unique('currency_code')
                ->values();

            foreach ($exchangeRates as $key => $exchangeRate) {
                $response = Http::get("{$apiUrl}/latest", [
                    'access_key' => $apiKey,
                    'base' => $exchangeRate->currency_code,
                ]);

                if ($response->failed()) {
                    Log::error('Exchange rate error', $response->json());
                }

                $data = $response->json();
                $rates = $data['rates'][$baseCurrency];

                if (floatval($rates) != floatval($exchangeRate->rate)) {
                    $newRate = new ExchangeRate();
                    $newRate->currency_code = $exchangeRate->currency_code;
                    $newRate->target_currency = $baseCurrency;
                    // $newRate->rate = ceil($rates * 10000) / 10000;
                    $newRate->rate = $rates;
                    $newRate->effective_at = now();
                    $newRate->save();
                }
            }
        } catch (\Exception $e) {
            Log::error("Exchange rate payment error {$e->getMessage()}");
        }
    }
}
