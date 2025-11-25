<?php

namespace App\Console\Commands;

use App\Models\ExchangeRate;
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
        ExchangeRate::where('effective_at', '<', Carbon::now()->subDays(7))->delete();
    }
}
