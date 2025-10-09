<?php

namespace App\Console\Commands;

use App\Constants\ProviderConstant;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\Setting;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

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
    protected $description = 'Sync products and product items from VexaGame API into local database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting VexaGame product sync...');
        
        $baseUrl   = Setting::getByKey('vexagame_api_url');
        $token     = Setting::getByKey('vexagame_api_token');
        $orderUrl  = rtrim($baseUrl, '/') . '/v2/product-item';

        $fallbackMarginPublic = Setting::getByKey('margin_public');
        $fallbackMarginSilver = Setting::getByKey('margin_silver');
        $fallbackMarginGold   = Setting::getByKey('margin_gold');
        $fallbackMarginVip    = Setting::getByKey('margin_vip');

        $exchangeRate = get_exchange_rate('IDR', Setting::getBaseCurrency());

        $products = Product::where('provider', ProviderConstant::VEXAGAME)->get();

        if ($products->isEmpty()) {
            $this->warn('⚠️  No products found for VexaGame provider.');
            return;
        }

        $this->line("Found {$products->count()} products to sync.");

        $progressBar = $this->output->createProgressBar($products->count());
        $progressBar->start();

        foreach ($products as $product) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => $token,
                ])->timeout(15)->get($orderUrl, [
                    'product_slug' => $product->provider_code
                ]);

                if ($response->failed()) {
                    $this->error("\n❌ Failed to fetch product: {$product->name} ({$product->provider_code})");
                    $this->line($response->body());
                    $progressBar->advance();
                    continue;
                }

                $payload = $response->json()['payload'] ?? [];
                if (empty($payload)) {
                    $this->warn("\n⚠️  No product items found for: {$product->name}");
                    $progressBar->advance();
                    continue;
                }

                // Update product margin defaults
                $product->update([
                    'markup_user'              => $this->useFallbackIfNonPositive($product->markup_user, $fallbackMarginPublic),
                    'markup_reseller_silver'   => $this->useFallbackIfNonPositive($product->markup_reseller_silver, $fallbackMarginSilver),
                    'markup_reseller_gold'     => $this->useFallbackIfNonPositive($product->markup_reseller_gold, $fallbackMarginGold),
                    'markup_reseller_vip'      => $this->useFallbackIfNonPositive($product->markup_reseller_vip, $fallbackMarginVip),
                ]);

                $updatedCount = 0;
                $skippedCount = 0;

                foreach ($payload as $item) {
                    $productItem = ProductItem::where('product_id', $product->id)
                        ->where('code', $item['code'])
                        ->first();

                    if (!$productItem) {
                        $skippedCount++;
                        continue;
                    }

                    $marginPublic = $this->useFallbackIfNonPositive($productItem->margin, $product->markup_user);
                    $marginSilver = $this->useFallbackIfNonPositive($productItem->margin_silver, $product->markup_reseller_silver);
                    $marginGold   = $this->useFallbackIfNonPositive($productItem->margin_gold, $product->markup_reseller_gold);
                    $marginVip    = $this->useFallbackIfNonPositive($productItem->margin_vip, $product->markup_reseller_vip);

                    $productItem->update([
                        'provider'       => ProviderConstant::VEXAGAME,
                        'capital'        => $item['price_raw'] * $exchangeRate,
                        'margin'         => $marginPublic,
                        'margin_silver'  => $marginSilver,
                        'margin_gold'    => $marginGold,
                        'margin_vip'     => $marginVip,
                        'sync_at'        => now(),
                    ]);

                    $updatedCount++;
                }

                $this->line("\n✅ Synced {$updatedCount} items (skipped {$skippedCount}) for {$product->name}.");

            } catch (Exception $e) {
                $this->error("\n💥 Error syncing {$product->name}: " . $e->getMessage());
                continue;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
        $this->info('🎉 VexaGame product sync completed successfully!');
    }

    /**
     * Use fallback value if the given value is null or non-positive.
     *
     * @param mixed $value
     * @param mixed $fallback
     * @return float
     */
    private function useFallbackIfNonPositive(mixed $value, mixed $fallback): float
    {
        $fallback = is_numeric($fallback) ? (float)$fallback : 0.0;
        return (is_null($value) || (float)$value <= 0)
            ? $fallback
            : (float)$value;
    }
}