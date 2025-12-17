<?php

namespace App\Console\Commands;

use App\Services\DynastyGdsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DynastyGdsGameList extends Command
{
    /**
     * @var string
     */
    protected $signature = 'dynasty-gds:game-list';

    /**
     * @var string
     */
    protected $description = 'Get Dynasty GDS game list with balance info';

    public function handle()
    {
        $dynastyGdsService = new DynastyGdsService();
        
        $productList = $dynastyGdsService->productList();
        $balanceInfo = $dynastyGdsService->balance(); 

        // Struktur final JSON
        $data = [
            'balance_info' => $balanceInfo,
            'products'     => $productList,
        ];

        $path = 'dynasty-gds/game-list.json';

        Storage::put($path, json_encode($data, JSON_PRETTY_PRINT));

        $this->info("Dynasty GDS game list saved to storage/{$path}");
    }
}