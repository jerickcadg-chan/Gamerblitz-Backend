<?php

namespace App\Console\Commands;

use App\Services\DynastyDgsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DynastyDgsGameList extends Command
{
    /**
     * @var string
     */
    protected $signature = 'dynasty-dgs:game-list';

    /**
     * @var string
     */
    protected $description = 'Get Dynasty DGS game list with balance info';

    public function handle()
    {
        $dynastyGdsService = new DynastyDgsService();
        
        $productList = $dynastyGdsService->productList();
        $balanceInfo = $dynastyGdsService->balance(); 

        // Struktur final JSON
        $data = [
            'balance_info' => $balanceInfo,
            'products'     => $productList,
        ];

        $path = 'dynasty-dgs/game-list.json';

        Storage::put($path, json_encode($data, JSON_PRETTY_PRINT));

        $this->info("Dynasty GDS game list saved to storage/{$path}");
    }
}