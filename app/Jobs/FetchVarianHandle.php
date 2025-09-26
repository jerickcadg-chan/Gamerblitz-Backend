<?php

namespace App\Jobs;

use App\Console\Commands\FetchVariant;
use App\Models\FetchVarianJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;

class FetchVarianHandle implements ShouldQueue
{
    use Queueable;

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
        Artisan::call('fetch:variant');

        FetchVarianJob::find($this->statusId)->update([
            'status' => 'DONE'
        ]);
    }
}
