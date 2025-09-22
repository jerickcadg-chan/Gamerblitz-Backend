<?php

namespace App\Console\Commands;

use App\Constants\ProviderConstant;
use App\Data\LapakGaming\Category;
use App\Data\LapakGaming\ProductItem as AppProductItem;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\Setting;
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
        $token = Setting::getByKey(Setting::KEY_LAPAKGAMING_API_TOKEN);
        $baseUrl = Setting::getByKey(Setting::KEY_LAPAKGAMING_API_URL);
        $fallbackMarginPublic = Setting::getByKey('margin_public');
        $fallbackMarginSilver = Setting::getByKey('margin_silver');
        $fallbackMarginGold = Setting::getByKey('margin_gold');
        $fallbackMarginVip = Setting::getByKey('margin_vip');

        $log = Log::channel('lapakgaming');

        if (!$token) {
            throw new \Exception('Missing LapakGaming api token in setting');
        }

        if (!$baseUrl) {
            throw new \Exception('Missing LapakGaming api url in setting');
        }

        $categoriesUrl = $baseUrl . '/api/category';       // e.g., Mobile Legends, Genshin Impact
        $productItemsUrl = $baseUrl . '/api/product';      // e.g., Diamond 50, Diamond 100

        $products = Product::query()
            ->where('status', Product::ACTIVE)
            ->where('provider', ProviderConstant::LAPAKGAMING)
            ->get();

        $countryCodes = $products->pluck("provider_country")->unique();

        $allLgCategories = collect();

        foreach ($countryCodes as $c) {
            $log->info('LapakGaming: fetching categories', ['url' => $categoriesUrl, 'country_code' => $c]);

            $response = Http::withToken($token)->get($categoriesUrl, [
                "country_code" => $c
            ]);

            if ($response->failed()) {
                $log->error('LapakGaming: fetching categories failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                continue;
            }

            $log->info('categories response', [
                'url' => $categoriesUrl,
                "counttry_code" => $c,
                'response' => $response->json(),
            ]);

            $allLgCategories = $allLgCategories->concat(collect($response->json("data.categories")));
        }

        $lapakGamingCurrency = 'IDR';
        $exchangeRate = get_exchange_rate($lapakGamingCurrency, Setting::getBaseCurrency());

        foreach ($products as $product) {
            $lgCategory = ($allLgCategories)
                ->where('code', $product->provider_code)
                ->where('country_code', strtolower($product->provider_country))
                ->first();

            if (!$lgCategory) {
                $msg = "LapakGaming: Product not found: name={$product->name} code={$product->provider_code} country={$product->provider_country}";
                $this->error($msg);
                $log->error($msg, [
                    'lgProduct' => $lgCategory,
                ]);
                continue; // skip products not in provider's response
            }

            $lgCategory = Category::from($lgCategory);

            $log->info('LapakGaming: fetching product items', [
                'url' => $productItemsUrl,
                'category' => $product->provider_code,
                'country' => $product->provider_country,
            ]);

            // PERF: fetch items concurrently
            $itemsResponse = Http::withToken($token)->get($productItemsUrl, [
                'category_code' => $product->provider_code,
                'country_code' => $product->provider_country ?? 'id',
            ]);

            if ($itemsResponse->failed()) {
                $msg = "LapakGaming: fetching product items failed: {$product->provider_code} {$product->proivder_country}";
                $this->error($msg);
                $log->error($msg, [
                    'status' => $itemsResponse->status(),
                    'body' => $itemsResponse->body(),
                ]);
                continue;
            }

            $lgItems = AppProductItem::collect($itemsResponse->json('data.products'));

            $log->info('product items', [
                'url' => $productItemsUrl,
                'category_code' => $product->provider_code,
                'country_code' => $product->provider_country ?? 'id',
                'response' => $lgItems,
            ]);

            try {
                DB::beginTransaction();

                $product->input_format = $lgCategory->forms ?? $product->input_format;
                $product->check_uid = $lgCategory->check_id;
                $product->updated_at = now();
                $product->markup_user = $product->markup_user ?: $fallbackMarginPublic;
                $product->markup_reseller_silver = $product->markup_reseller_silver ?: $fallbackMarginSilver;
                $product->markup_reseller_gold = $product->markup_reseller_gold ?: $fallbackMarginGold;
                $product->markup_reseller_vip = $product->markup_reseller_vip ?: $fallbackMarginVip;
                $product->save();

                foreach ($lgItems as $item) {
                    $productItem = ProductItem::where('product_id', $product->id)->where('code', $item->code)->firstOrNew([
                        'product_id' => $product->id,
                        'code' => $item->code,
                    ]);

                    $marginPublicUser = $productItem->margin ?: $product->markup_user;
                    $marginSilver = $productItem->margin_silver ?: $product->markup_reseller_silver;
                    $marginGold = $productItem->margin_gold ?: $product->markup_reseller_gold;
                    $marginVip = $productItem->margin_vip ?: $product->markup_reseller_vip;

                    $productItem->name = $item->name;
                    $productItem->capital = $item->price * $exchangeRate;
                    $productItem->stock = null;

                    $productItem->margin = $marginPublicUser;
                    $productItem->margin_silver = $marginSilver;
                    $productItem->margin_gold = $marginGold;
                    $productItem->margin_vip = $marginVip;
                    $productItem->status = $item->status === 'available' ? 'active' : 'empty';
                    $productItem->sync_at = now();
                    $productItem->save();
                }

                DB::commit();
            } catch (\Exception $e) {
                $msg = "LapakGaming: Failed to update product: " . $e->getMessage();
                $log->error($msg, [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $this->error($msg);
                DB::rollBack();
            }
        }
    }
}
