<?php

namespace App\Jobs;

use App\Console\Commands\FetchVariant;
use App\Models\FetchVarianJob;
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
     * @var int
     */
    public int $statusId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $statusId)
    {
        $this->statusId = $statusId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Artisan::call('app:sync-lapak-gaming');
        } catch (\Throwable $e) {
            Log::error("❌ Error in sync-lapak-gaming: " . $e->getMessage());
        }
    
        try {
            Artisan::call('app:sync-vexa-game');
        } catch (\Throwable $e) {
            Log::error("❌ Error in sync-vexa-game: " . $e->getMessage());
        }
        
        FetchVarianJob::find($this->statusId)?->update([
            'status' => 'DONE',
        ]);
    }
}
