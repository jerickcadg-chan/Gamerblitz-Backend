<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

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
        Artisan::call('app:sync-lapak-gaming');
        Artisan::call('app:sync-vexa-game');
        Artisan::call('app:sync-dynasty-dgs');
    }
}
