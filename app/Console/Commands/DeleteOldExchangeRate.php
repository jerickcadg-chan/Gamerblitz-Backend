<?php

namespace App\Console\Commands;

use App\Models\ExchangeRate;
use App\Models\FetchVarianJob;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteOldExchangeRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-old-exchange-rate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to delete old exchange rate.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       $this->deleteOldCurrencies();
       $this->deleteOldSync();
    }

    private function deleteOldCurrencies()
    {
        $baseCurrency = Setting::getBaseCurrency();

        $currencies = ExchangeRate::where('currency_code', '!=', $baseCurrency)
            ->distinct()
            ->pluck('currency_code');

        foreach ($currencies as $currency) {
            $latestId = ExchangeRate::where('currency_code', $currency)
                ->orderByDesc('effective_at')
                ->value('id');

            ExchangeRate::where('currency_code', $currency)
                ->where('id', '!=', $latestId)
                ->where('effective_at', '<', Carbon::now()->subDays(7))
                ->delete();
        }
    }

    private function deleteOldSync()
    {
        FetchVarianJob::where('created_at', '<', Carbon::now()->subDays(3))->delete();
    }
}
