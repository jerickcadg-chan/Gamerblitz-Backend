<?php

namespace App\Console\Commands;

use App\Services\DynastyGdsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DynastyGdsDenomList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dynasty-gds:denom-list {product_code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Dynasty GDS denom list by product code';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $productCode = $this->argument('product_code');

        $dynastyGdsService = new DynastyGdsService();
        $data = $dynastyGdsService->productInfo($productCode);

        $path = "dynasty-gds/game-list-{$productCode}.json";

        Storage::put($path, json_encode($data, JSON_PRETTY_PRINT));

        $this->info("Dynasty GDS game list saved to storage/{$path}");
    }
}