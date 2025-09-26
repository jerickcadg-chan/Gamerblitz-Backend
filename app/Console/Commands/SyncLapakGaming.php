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
        $memstart = memory_get_usage();

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

        $countryCodes = Product::query()
            ->distinct()->pluck('provider_country')->values();

        foreach ($countryCodes as $countryCode) {
            $this->line("Processing country $countryCode");

            $products = Product::select([
                'id',
                'name',
                'code',
                'provider_code',
                'provider_country',
                'markup_user',
                'markup_reseller_silver',
                'markup_reseller_gold',
                'markup_reseller_vip',
                'input_format',
            ])
                ->where('status', Product::ACTIVE)
                ->where('provider', ProviderConstant::LAPAKGAMING)
                ->where('provider_country', strtoupper($countryCode))
                ->cursor();

            $response = Http::withToken($token)->get($categoriesUrl, ["country_code" => $countryCode]);

            if ($response->failed()) {
                $log->error('LapakGaming: fetching categories failed', ['country_code' => $countryCode, 'status' => $response->status()]);
                continue;
            }

            $lgCategories = collect($response->json("data.categories"));

            $lapakGamingCurrency = 'IDR';
            $exchangeRate = get_exchange_rate($lapakGamingCurrency, Setting::getBaseCurrency());

            foreach ($products as $product) {
                $this->line("Processing product {$product->name}");

                $matchProduct = $lgCategories
                    ->where('code', $product->provider_code)
                    ->where('country_code', strtolower($product->provider_country))
                    ->first();

                if (!$matchProduct) {
                    $msg = "LapakGaming: Product not found: name={$product->name} code={$product->provider_code} country={$product->provider_country}";
                    $log->error($msg);
                    continue; // skip products not in provider's response
                }

                $lgCategory = Category::from($matchProduct);

                // PERF: fetch items concurrently
                $itemsResponse = Http::withToken($token)->get($productItemsUrl, [
                    'category_code' => $product->provider_code,
                    'country_code' => $product->provider_country ?? 'id',
                ]);

                if ($itemsResponse->failed()) {
                    $msg = "LapakGaming: fetching product items failed: {$product->provider_code} {$product->proivder_country}";
                    $log->error($msg, ['status' => $itemsResponse->status()]);
                    continue;
                }

                $log->info('product items', [
                    'url' => $productItemsUrl,
                    'category_code' => $product->provider_code,
                    'country_code' => $product->provider_country ?? 'id',
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

                    foreach ($itemsResponse->json('data.products') as $lgItem) {
                        $item = AppProductItem::from($lgItem);
                        $this->line("Processing item {$item->code}");

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
                        'product' => $product->name,
                        'item' => $item->code ?? null,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    DB::rollBack();
                }
            }

        }

        $this->line("Start: " . $memstart);
        $this->line("End: " . memory_get_usage());
        $this->line("Peak: " . memory_get_peak_usage());
    }
}
