<?php

namespace App\Console\Commands;

use App\Constants\CurrencyConstant;
use App\Constants\ProviderConstant;
use App\Models\Product;
use App\Models\ProductItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncLapakGaming extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-lapak-gaming';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize data with Lapak Gaming';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $token = config('array.lapakgaming.token');
        $baseUrl = config('array.lapakgaming.url');
        $categoriesUrl = $baseUrl . '/api/category';       // e.g., Mobile Legends, Genshin Impact
        $productItemsUrl = $baseUrl . '/api/product';      // e.g., Diamond 50, Diamond 100

        Log::info('LapakGaming: fetching categories', ['url' => $categoriesUrl]);

        $response = Http::withToken($token)->get($categoriesUrl);

        if (!$response->successful()) {
            Log::error('LapakGaming: fetching categories failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return;
        }

        $lgCategories = collect($response->json('data.categories'));

        Log::channel('lapakgaming')->info('categories', [
            'url' => $categoriesUrl,
            'response' => $lgCategories,
        ]);

        $products = Product::query()
            ->where('status', Product::ACTIVE)
            ->where('provider', ProviderConstant::LAPAKGAMING)
            ->get();

        foreach ($products as $product) {
            $lgProduct = $lgCategories->firstWhere('code', $product->provider_code);

            if (!$lgProduct) {
                continue; // skip products not in provider's response
            }

            Log::info('LapakGaming: fetching product items', [
                'url' => $productItemsUrl,
                'category' => $product->provider_code,
                'country' => $product->provider_country,
            ]);

            // TODO: fetch items concurrently
            $itemsResponse = Http::withToken($token)->get($productItemsUrl, [
                'category_code' => $product->provider_code,
                'country_code' => $product->provider_country ?? 'id',
            ]);

            if (!$itemsResponse->successful()) {
                Log::error('LapakGaming: fetching product items failed', [
                    'status' => $itemsResponse->status(),
                    'body' => $itemsResponse->body(),
                ]);
                continue;
            }

            $lgItems = collect($itemsResponse->json('data.products'));

            Log::channel('lapakgaming')->info('product items', [
                'url' => $productItemsUrl,
                'category_code' => $product->provider_code,
                'country_code' => $product->provider_country ?? 'id',
                'response' => $lgItems,
            ]);

            try {
                DB::beginTransaction();

                $product->update([
                    // TODO: normalize forms as input_format
                    'input_format' => $lgProduct['forms'] ?? $product->input_format,
                    'updated_at' => now(),
                ]);

                foreach ($lgItems as $item) {
                    $productItem = ProductItem::where('product_id', $product->id)->where('code', $item['code'])->firstOrNew([
                        'product_id' => $product->id,
                        'code' => $item['code'],
                    ]);

                    $marginPublicUser = $productItem->margin ?: $product->markup_user;
                    $marginSilver = $productItem->margin_silver ?: $product->markup_reseller_silver;
                    $marginGold = $productItem->margin_gold ?: $product->markup_reseller_gold;
                    $marginVip = $productItem->margin_vip ?: $product->markup_reseller_vip;

                    $productItem->name = $item['name'];
                    $productItem->capital = $item['price'];
                    $productItem->stock = null;
                    $productItem->currency_code = CurrencyConstant::IDR;

                    $productItem->margin = $marginPublicUser;
                    $productItem->margin_silver = $marginSilver;
                    $productItem->margin_gold = $marginGold;
                    $productItem->margin_vip = $marginVip;
                    $productItem->status = $item['status'] === 'available' ? 'active' : 'empty';
                    $productItem->sync_at = now();
                    $productItem->save();
                }

                DB::commit();
            } catch (\Exception $e) {
                Log::error('LapakGaming: failed to update product', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                DB::rollBack();
            }
        }
    }
}
