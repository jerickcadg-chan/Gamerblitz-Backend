<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class FetchVarianHandle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     * For whitelabel reseller sites, sync from the whitelabel provider.
     * Other providers are skipped gracefully if not configured.
     */
    public function handle(): void
    {
        // Primary sync for whitelabel reseller
        try {
            Artisan::call('app:sync-whitelabel');
            Log::info('FetchVarianHandle: Whitelabel sync completed.');
        } catch (\Exception $e) {
            Log::error('FetchVarianHandle: Whitelabel sync failed - ' . $e->getMessage());
        }

        // Other providers - skip gracefully if not configured
        try {
            Artisan::call('app:sync-lapak-gaming');
        } catch (\Exception $e) {
            Log::warning('FetchVarianHandle: LapakGaming sync skipped - ' . $e->getMessage());
        }

        try {
            Artisan::call('app:sync-vexa-game');
        } catch (\Exception $e) {
            Log::warning('FetchVarianHandle: VexaGame sync skipped - ' . $e->getMessage());
        }

        try {
            Artisan::call('app:sync-dynasty-dgs');
        } catch (\Exception $e) {
            Log::warning('FetchVarianHandle: DynastyDGS sync skipped - ' . $e->getMessage());
        }
    }
}
