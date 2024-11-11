<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '. config('array.bangjeff.api_key'),
            'Accept' => 'application/json'
        ])->post(config('array.bangjeff.url').'/api/v3/product');

        $products = $response->collect();

        $text = '';

        foreach ($products['data'] as $product) {
            $text .= "===============\n";
            $text .= "Nama : ".$product['name']."\n";
            $text .= "Kode : ".$product['code']."\n";
            $text .= "Input : ".json_encode($product['inputs'])."\n";
            $text .= "===============\n";
        }

        $filePath = public_path('storage/products.txt');

        file_put_contents($filePath, $text);

        $this->info("saved to $filePath");
    }
}
