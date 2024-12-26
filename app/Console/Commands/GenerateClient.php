<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Product;
use App\Models\ProductClient;
use App\Models\ProductItemClient;
use App\Models\User;
use Illuminate\Console\Command;

class GenerateClient extends Command
{
    protected $signature = 'app:generate-client';

    protected $description = 'Perintah untuk membuat client baru';

    public function handle()
    {
        $clients = Client::all();
        $clientErrors = [];

        ProductClient::truncate();
        ProductItemClient::truncate();

        foreach ($clients as $client) {
            $this->info($client->name);
            if (User::whereClientId($client->id)->first()) {
                if (!$client->productClients()->where('client_id', $client->id)->exists()) {
                    $this->info('Membuat product client');
                    $productIds = Product::query()->select('id')->get()->map(function ($product) use ($client) {
                        return [
                            'product_id' => $product->id,
                            'client_id' => $client->id
                        ];
                    })->toArray();
                    $client->productClients()->createMany($productIds);
                }

                if (!$client->productItemClients()->where('client_id', $client->id)->exists()) {
                    $this->info('Membuat product item client');
                    $productClients = ProductClient::query()->where('client_id', $client->id)->get();
                    $productItemIds = [];
                    foreach ($productClients as $productClient) {
                        $productItems = $productClient->product->productItems;
                        foreach ($productItems as $productItem) {
                            array_push($productItemIds, [
                                'product_item_id' => $productItem->id,
                                'client_id' => $client->id,
                                'margin' => 0,
                            ]);
                        }
                    }
                    $client->productItemClients()->createMany($productItemIds);
                }
            } else {
                array_push($clientErrors, $client->name);
            }
        }

        if (count($clientErrors) > 0) {
            $this->error('Klien yang tidak memiliki user:');
            foreach ($clientErrors as $clientError) {
                $this->error($clientError);
            }
        } else {
            $this->info('Semua klien memiliki user');
        }
    }
}
