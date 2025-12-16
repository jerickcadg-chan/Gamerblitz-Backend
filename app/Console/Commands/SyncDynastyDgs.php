<?php

namespace App\Console\Commands;

use App\Constants\ProviderConstant;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\Setting;
use App\Services\DynastyGdsService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SyncDynastyDgs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-dynasty-dgs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync products and product items from Dynasty GDS API into the local database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting Dynasty GDS product synchronization...');

        $baseUrl      = Setting::getByKey('dynasty_gds_api_url');
        $email        = Setting::getByKey('dynasty_gds_email');
        $password     = Setting::getByKey('dynasty_gds_password');

        $exchangeRate = get_exchange_rate('MYR', Setting::getBaseCurrency());
        $log          = Log::channel('dynasty_gds');

        Cache::put('dynasty-gds-sync', true, 120);

        try {
            if (!$email || !$password) {
                $log->warning('⚠️ Missing Dynasty GDS API email or password in setting — skipping sync.');
                $this->warn('⚠️ Missing Dynasty GDS API email or password in setting — skipping sync.');
                return;
            }

            if (!$baseUrl) {
                $log->warning('⚠️ Missing Dynasty GDS API URL in setting — skipping sync.');
                $this->warn('⚠️ Missing Dynasty GDS API URL in setting — skipping sync.');
                return;
            }

            $fallbacks = [
                'public' => Setting::getByKey('margin_public'),
                'silver' => Setting::getByKey('margin_silver'),
                'gold'   => Setting::getByKey('margin_gold'),
                'vip'    => Setting::getByKey('margin_vip'),
            ];

            $products = Product::where('provider', ProviderConstant::DYNASTY_DGS)->get();

            if ($products->isEmpty()) {
                $this->warn('⚠️  No products found for provider: Dynasty GDS');
                return;
            }

            $this->line("Found {$products->count()} products to sync.\n");

            $progressBar = $this->output->createProgressBar($products->count());
            $progressBar->setFormat('Syncing products: [%bar%] %percent:3s%% | %current%/%max%');
            $progressBar->start();

            foreach ($products as $product) {
                try {
                    $this->syncProduct($product, $exchangeRate, $fallbacks);
                    $this->disableInactiveProductItems($product);
                } catch (Exception $e) {
                    $this->error("\n💥 Error syncing {$product->name}: {$e->getMessage()}");
                }

                $progressBar->advance();
            }

            $progressBar->finish();

            $this->newLine(2);
            $this->info('🎉 Dynasty GDS product sync completed successfully!');
        } finally {
            Cache::forget('dynasty-gds-sync');
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
    private function syncProduct(Product $product, float $exchangeRate, array $fallbacks): void
    {
        $dynasty = new DynastyGdsService();

        $response = $dynasty->productInfo($product->provider_code_dynasty_dgs);
        $payload = $response['denoms'];

        // Update input format by provider
        $product->update([
            'input_format' => $this->mapRequireInfo($response['requiredInfos'])
        ]);

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
     * Disable all product items not from Dynasty GDS.
     * 
     * @param Product $product
     * 
     * @return void
     */
    private function disableInactiveProductItems(Product $product): void
    {
        $affected = ProductItem::where('product_id', $product->id)
            ->where('provider', '!=', ProviderConstant::DYNASTY_DGS)
            ->where('status', 'active')
            ->where('is_locked', 0)
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
            ->where('code', $item['denomCode'])
            ->where('provider', ProviderConstant::DYNASTY_DGS)
            ->first();

        $baseData = [
            'name'         => $item['name'],
            'status'       => 'active',
            'country_code' => 'MY',
            'provider'     => ProviderConstant::DYNASTY_DGS,
            'capital'      => $item['price'] * $exchangeRate,
            'sync_at'      => now(),
        ];

        if ($productItem) {
            if (in_array($productItem->status, ['active', 'empty'])) {
                $productItem->update($baseData);
            } else {
                return;
            }
        } else {
            ProductItem::create(array_merge($baseData, [
                'product_id'    => $product->id,
                'code'          => $item['denomCode'],
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

    /**
     * @param array $requiredInfos
     * 
     * @return array
     */
    private function mapRequireInfo(array $requiredInfos): array
    {
        $mapped = [];

        foreach ($requiredInfos as $info) {
            // type = option jika selection tidak kosong
            $isOption = !empty($info['selection']);

            $mapped[] = (object)[
                'name'        => $info['name'], // ← langsung pakai name dari provider
                'type'        => $isOption ? 'option' : 'text',
                'label'       => $info['description'], // boleh pakai description (contohmu)
                'placeholder' => $info['description'], // sesuai permintaan
                'options'     => $isOption
                    ? array_map(fn($s) => [
                        'name'  => $s['name'],
                        'value' => $s['code'],
                    ], $info['selection'])
                    : []
            ];
        }

        return $mapped;
    }
}
