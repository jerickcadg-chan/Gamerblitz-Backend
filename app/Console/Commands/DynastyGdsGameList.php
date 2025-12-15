<?php

namespace App\Console\Commands;

use App\Services\DynastyGdsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DynastyGdsGameList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dynasty-gds:game-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dynastyGdsService = new DynastyGdsService();
        $data = $dynastyGdsService->productList();

        $path = 'dynasty-gds/game-list.json';

        Storage::put($path, json_encode($data, JSON_PRETTY_PRINT));

        $this->info("Dynasty GDS game list saved to storage/{$path}");
    }
}