<?php

namespace App\Console\Commands;

use Exception;
use App\Models\Product;
use App\Models\Setting;
use App\Models\ProductItem;
use Illuminate\Console\Command;
use App\Constants\ProviderConstant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncVexaGame extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-vexa-game';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync products and product items from VexaGame API into the local database.';

    /**
     * Execute the console command.
     * 
     * @return void
     */
    public function handle(): void
    {
        $this->info('🚀 Starting VexaGame product synchronization...');

        $baseUrl      = Setting::getByKey('vexagame_api_url');
        $token        = Setting::getByKey('vexagame_api_token');
        $apiUrl       = rtrim($baseUrl, '/') . '/v2/product-item';
        $exchangeRate = get_exchange_rate('IDR', Setting::getBaseCurrency());
        $log          = Log::channel('vexagame');
        Cache::put('vexagame-sync', true, 120);

        try {
            if (!$token) {
                $log->warning('⚠️ Missing VexaGame API token in setting — skipping sync.');
                $this->warn('⚠️ Missing VexaGame API token in setting — skipping sync.');
                return;
            }

            if (!$baseUrl) {
                $log->warning('⚠️ Missing VexaGame API URL in setting — skipping sync.');
                $this->warn('⚠️ Missing VexaGame API URL in setting — skipping sync.');
                return;
            }

            $fallbacks = [
                'public' => Setting::getByKey('margin_public'),
                'silver' => Setting::getByKey('margin_silver'),
                'gold'   => Setting::getByKey('margin_gold'),
                'vip'    => Setting::getByKey('margin_vip'),
            ];

            $products = Product::where('provider', ProviderConstant::VEXAGAME)->get();

            if ($products->isEmpty()) {
                $this->warn('⚠️  No products found for provider: VEXAGAME');
                return;
            }

            $this->line("Found {$products->count()} products to sync.\n");

            $progressBar = $this->output->createProgressBar($products->count());
            $progressBar->setFormat('Syncing products: [%bar%] %percent:3s%% | %current%/%max%');
            $progressBar->start();

            foreach ($products as $product) {
                try {
                    $this->syncProduct($product, $apiUrl, $token, $exchangeRate, $fallbacks);
                    $this->disableInactiveProductItems($product);
                } catch (Exception $e) {
                    $this->error("\n💥 Error syncing {$product->name}: {$e->getMessage()}");
                }

                $progressBar->advance();
            }

            $progressBar->finish();

            $this->newLine(2);
            $this->info('🎉 VexaGame product sync completed successfully!');
        } finally {
            Cache::forget('vexagame-sync');
        }
    }

    /**
     * Sync a single product and its items.
     * 
     * @param Product $product
     * @param string $apiUrl
     * @param string $token
     * @param float $exchangeRate
     * @param array $fallbacks
     * 
     * @return void
     */
    private function syncProduct(Product $product, string $apiUrl, string $token, float $exchangeRate, array $fallbacks): void
    {
        $log      = Log::channel('vexagame');
        $response = Http::withHeaders(['Authorization' => $token])
            ->timeout(15)
            ->get($apiUrl, ['product_slug' => $product->provider_code_vexa]);

        if ($response->failed()) {
            $message = "API request failed ({$response->status()}) for product: {$product->name}";

            $log->error("⚠️ {$message}");
            $this->warn("\n⚠️ {$message}");
            return;
        }

        $payload = $response->json()['payload'] ?? [];
        if (empty($payload)) {
            $this->warn("\n⚠️  No product items found for: {$product->name}");
            return;
        }

        $this->updateProductDefaultMargins($product, $fallbacks);

        $updatedCount = 0;
        foreach ($payload as $item) {
            $this->syncProductItem($product, $item, $exchangeRate);
            $updatedCount++;
        }

        $this->line("\n✅ Synced {$updatedCount} items for {$product->name}.");
    }

    /**
     * Disable all product items not from VexaGame.
     * 
     * @param Product $product
     * 
     * @return void
     */
    private function disableInactiveProductItems(Product $product): void
    {
        $affected = ProductItem::where('product_id', $product->id)
            ->where('provider', '!=', ProviderConstant::VEXAGAME)
            ->where('status', 'active')
            ->update(['status' => 'empty']);

        if ($affected > 0) {
            $this->line("🧹 Marked {$affected} inactive items as 'empty' for {$product->name}.");
        }
    }

    /**
     * Sync a single product item.
     * 
     * @param Product $product
     * @param array $item
     * @param float $exchangeRate
     * 
     * @return void
     */
    private function syncProductItem(Product $product, array $item, float $exchangeRate): void
    {
        $productItem = ProductItem::where('product_id', $product->id)
            ->where('code', $item['code'])
            ->where('provider', ProviderConstant::VEXAGAME)
            ->first();

        $baseData = [
            'name'         => $item['name'],
            'status'       => 'active',
            'country_code' => 'ID',
            'provider'     => ProviderConstant::VEXAGAME,
            'capital'      => $item['price_raw'] * $exchangeRate,
            'sync_at'      => now(),
        ];

        if ($productItem && in_array($productItem->status, ['active', 'empty'])) {
            $productItem->update($baseData);
        } else {
            ProductItem::create(array_merge($baseData, [
                'product_id'    => $product->id,
                'code'          => $item['code'],
                'stock'         => null,
                'margin'        => $product->markup_user,
                'margin_silver' => $product->markup_reseller_silver,
                'margin_gold'   => $product->markup_reseller_gold,
                'margin_vip'    => $product->markup_reseller_vip,
            ]));
        }
    }

    /**
     * Ensure product has valid default margin values.
     * 
     * @param Product $product
     * @param array $fallbacks
     * 
     * @return void
     */
    private function updateProductDefaultMargins(Product $product, array $fallbacks): void
    {
        $product->update([
            'markup_user'            => $this->useFallbackIfNonPositive($product->markup_user, $fallbacks['public']),
            'markup_reseller_silver' => $this->useFallbackIfNonPositive($product->markup_reseller_silver, $fallbacks['silver']),
            'markup_reseller_gold'   => $this->useFallbackIfNonPositive($product->markup_reseller_gold, $fallbacks['gold']),
            'markup_reseller_vip'    => $this->useFallbackIfNonPositive($product->markup_reseller_vip, $fallbacks['vip']),
        ]);
    }

    /**
     * Return fallback value if input is null or non-positive.
     * 
     * @param mixed $value
     * @param mixed $fallback
     * 
     * @return float
     */
    private function useFallbackIfNonPositive(mixed $value, mixed $fallback): float
    {
        $fallback = is_numeric($fallback) ? (float) $fallback : 0.0;

        return (is_null($value) || (float) $value <= 0)
            ? $fallback
            : (float) $value;
    }
}
