<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchVariant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:variant';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Bangjeff product & variant';

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
     * @return string
     */
    public function handle(): string
    {
        foreach (Product::whereNotNull('code')->get() as $product) {
            $variants = Http::withHeaders([
                'Authorization' => 'Bearer '. config('array.bangjeff.api_key'),
                'Accept' => 'application/json'
            ])->post(config('array.bangjeff.url').'/api/v3/variant', [
                'code' => $product->code
            ]);

            if (isset($variants->collect()['data'])){
                foreach ($variants->collect()['data'] as $variant) {
                    $productItem = ProductItem::where('code', $variant['code'])->first();

                    $data = [
                        'price' => round($variant['price'] + ($variant['price'] * $product->markup_user / 100)),
                        'price_reseller' => round($variant['price'] + ($variant['price'] * $product->markup_reseller_silver / 100)),
                        'capital' => $variant['price'],
                        'stock' => $variant['isActive'] ? 99999 : 0
                    ];

                    if ($productItem) {
                        $productItem->update($data);
                        $this->info('Update variant ' . $variant['code']);
                    } else {
                        $createData = $data;
                        $createData['code'] = $variant['code'];
                        $createData['name'] = $variant['name'];
                        $createData['product_id'] = $product->id;

                        ProductItem::create($createData);
                        $this->info('Create variant ' . $variant['code']);
                    }
                }
            }
        }

        return 'ok';
    }
}
