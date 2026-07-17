<?php

namespace Database\Seeders;

use App\Constants\ProviderConstant;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\ProductCategory;
use App\Models\Setting;
use App\Services\PictureService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SyncWhitelabelProductSeeder extends Seeder
{
    /**
     * API timeout in seconds
     */
    private const API_TIMEOUT = 30;

    /**
     * Number of retry attempts for API calls
     */
    private const RETRY_ATTEMPTS = 3;

    /**
     * Delay between retries in milliseconds
     */
    private const RETRY_DELAY = 1000;

    /**
     * PictureService instance
     */
    private PictureService $pictureService;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->pictureService = new PictureService();

        $this->command->info('Starting Whitelabel Product Sync...');

        // Get API credentials from settings
        $token = Setting::getByKey('whitelabel_api_token');
        $baseUrl = Setting::getByKey('whitelabel_api_url');

        if (empty($token) || empty($baseUrl)) {
            $this->logAndOutput('error', 'Whitelabel API credentials not configured. Please set whitelabel_api_token and whitelabel_api_url in settings.');
            return;
        }

        // Fetch products from API
        $products = $this->fetchProductsFromApi($baseUrl, $token);

        if ($products === null) {
            $this->logAndOutput('error', 'Failed to fetch products from Whitelabel API.');
            return;
        }

        if (empty($products)) {
            $this->logAndOutput('warn', 'No products found in Whitelabel API response.');
            return;
        }

        $this->command->info("Found " . count($products) . " products from API.");

        $successCount = 0;
        $errorCount = 0;
        $sourceProviderCodes = [];

        foreach ($products as $productData) {
            if (!empty($productData['id'])) {
                $sourceProviderCodes[] = (string) $productData['id'];
            }

            try {
                $this->syncProduct($productData);
                $successCount++;
                $this->command->info("Synced: {$productData['name']}");
            } catch (\Throwable $e) {
                $errorCount++;
                $this->logAndOutput('error', "Failed to sync product '{$productData['name']}': " . $e->getMessage());
            }
        }

        $this->deactivateProductsMissingFromSource(array_values(array_unique($sourceProviderCodes)));

        $this->command->info("Sync completed. Success: {$successCount}, Errors: {$errorCount}");
    }

    /**
     * Fetch products from Whitelabel API
     */
    private function fetchProductsFromApi(string $baseUrl, string $token): ?array
    {
        try {
            $response = Http::timeout(self::API_TIMEOUT)
                ->retry(self::RETRY_ATTEMPTS, self::RETRY_DELAY)
                ->withHeaders([
                    'Authorization' => $token,
                    'Accept' => 'application/json',
                ])
                ->get("{$baseUrl}/partner/product");

            if ($response->failed()) {
                Log::channel('whitelabel')->error('Whitelabel API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $payload = $response->json('payload');

            if (!is_array($payload)) {
                Log::channel('whitelabel')->error('Invalid API response format', [
                    'response' => $response->json(),
                ]);
                return null;
            }

            return $payload;
        } catch (\Throwable $e) {
            Log::channel('whitelabel')->error('Whitelabel API error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Sync a single product
     */
    private function syncProduct(array $productData): void
    {
        $providerCodeWhitelabel = $productData['id'] ?? null;

        if (empty($providerCodeWhitelabel)) {
            throw new \Exception('Product ID is missing from API response');
        }

        DB::transaction(function () use ($productData, $providerCodeWhitelabel) {
            // Handle category
            $productCategoryId = $this->syncCategory($productData['category'] ?? null);

            // Handle images
            $picturePath = $this->downloadAndUploadImage($productData['picture'] ?? null, 'picture');
            $coverPath = $this->downloadAndUploadImage($productData['cover'] ?? null, 'cover');

            // Prepare product data
            // Generate code from product name initials (e.g., "Mobile Legends" -> "ML")
            $productName = $productData['name'] ?? '';
            $generatedCode = $this->generateCodeFromName($productName);

            // Determine if description/how_to_order is raw (not from Quill editor)
            $description = $productData['description'] ?? null;
            $howToOrder = $productData['how_to_order'] ?? null;
            $isRawDescription = !$this->isQuillContent($description);
            $isRawHowToOrder = !$this->isQuillContent($howToOrder);

            $productFields = [
                'name' => $productName,
                'code' => $generatedCode,
                'company' => $productData['company'] ?? null,
                'input_format' => is_array($productData['input_format'] ?? null)
                    ? json_encode($productData['input_format'])
                    : ($productData['input_format'] ?? null),
                'description' => $description,
                'how_to_order' => $howToOrder,
                'is_raw_description' => $isRawDescription,
                'is_raw_how_to_order' => $isRawHowToOrder,
                'slug' => $productData['slug'] ?? Str::slug($productName),
                'product_category_id' => $productCategoryId,
                'provider' => ProviderConstant::WHITELABEL,
                'provider_code_whitelabel' => $providerCodeWhitelabel,
                'provider_country' => $productData['provider_country'] ?? 'PH',
                'status' => $this->normalizeProductStatus($productData['status'] ?? null),
            ];

            // Only update picture/cover if we successfully downloaded new ones
            if ($picturePath) {
                $productFields['default_picture'] = $picturePath;
            }
            if ($coverPath) {
                $productFields['default_cover'] = $coverPath;
            }

            // Find existing product by provider_code_whitelabel
            $existingProduct = Product::where('provider_code_whitelabel', $providerCodeWhitelabel)->first();

            if ($existingProduct) {
                // Preserve images if new ones failed to download
                if (!$picturePath && $existingProduct->default_picture) {
                    unset($productFields['default_picture']);
                }
                if (!$coverPath && $existingProduct->default_cover) {
                    unset($productFields['default_cover']);
                }

                // Smart status sync:
                // - If GPDS says active AND product was previously auto-disabled by sync: re-enable it.
                // - If GPDS says active AND product was manually disabled by admin: keep it disabled.
                // - If GPDS says inactive: disable it and mark as auto-disabled.
                $gpdsStatus = $productFields['status'];

                if ($gpdsStatus === Product::ACTIVE) {
                    if ($existingProduct->is_auto_disabled) {
                        // Was auto-disabled before, GPDS re-enabled it — re-enable on our side too
                        $productFields['status'] = Product::ACTIVE;
                        $productFields['is_auto_disabled'] = false;
                    } else {
                        // Respect the current local status (could be manually disabled or already active)
                        unset($productFields['status']);
                        unset($productFields['is_auto_disabled']);
                    }
                } else {
                    // GPDS says inactive — auto-disable it
                    $productFields['status'] = Product::INACTIVE;
                    $productFields['is_auto_disabled'] = true;
                }

                $existingProduct->update($productFields);
            } else {
                // New product — use GPDS status directly
                $productFields['is_auto_disabled'] = ($productFields['status'] === Product::INACTIVE);
                Product::create($productFields);
            }
        });
    }

    /**
     * Sync product category
     */
    private function syncCategory(?string $categoryName): ?int
    {
        if (empty($categoryName)) {
            return null;
        }

        $category = ProductCategory::updateOrCreate(
            ['name' => $categoryName],
            ['slug' => Str::slug($categoryName)]
        );

        return $category->id;
    }

    /**
     * Deactivate whitelabel products that are no longer returned by the source API.
     * Marks them as auto-disabled so they can be re-enabled if GPDS re-enables them.
     */
    private function deactivateProductsMissingFromSource(array $sourceProviderCodes): void
    {
        if (empty($sourceProviderCodes)) {
            return;
        }

        $staleProducts = Product::where('provider', ProviderConstant::WHITELABEL)
            ->whereNotNull('provider_code_whitelabel')
            ->whereNotIn('provider_code_whitelabel', $sourceProviderCodes)
            ->where('status', Product::ACTIVE) // Only deactivate currently active products
            ->get();

        foreach ($staleProducts as $product) {
            $product->update([
                'status'          => Product::INACTIVE,
                'is_auto_disabled' => true,
            ]);

            $affectedItems = ProductItem::where('product_id', $product->id)
                ->where('provider', ProviderConstant::WHITELABEL)
                ->where('status', ProductItem::STATUS_ACTIVE)
                ->where('is_locked', 0)
                ->update(['status' => ProductItem::STATUS_EMPTY]);

            $this->logAndOutput('warn', "Auto-disabled missing GPDS product '{$product->name}' and {$affectedItems} active items.");
        }
    }

    /**
     * Convert provider product status values into local visibility values.
     */
    private function normalizeProductStatus(mixed $status): string
    {
        $normalized = strtolower(trim((string) ($status ?? 'inactive')));

        return in_array($normalized, ['active', '1', 'true', 'available', 'ready', 'enabled', 'in_stock'], true)
            ? Product::ACTIVE
            : Product::INACTIVE;
    }

    /**
     * Download image from URL and upload to local storage
     */
    private function downloadAndUploadImage(?string $imageUrl, string $type): ?string
    {
        if (empty($imageUrl)) {
            return null;
        }

        try {
            return $this->pictureService->insertFromUrl('image', $imageUrl);
        } catch (\Throwable $e) {
            Log::channel('whitelabel')->warning("Failed to download {$type} image", [
                'url' => $imageUrl,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Generate code from product name initials (letters only)
     * If single word: use the whole word in uppercase (e.g., "Growtopia" -> "GROWTOPIA")
     * If multiple words: use initials (e.g., "Mobile Legends" -> "ML", "Call of Duty: Mobile" -> "CODM")
     */
    private function generateCodeFromName(string $name): string
    {
        if (empty($name)) {
            return '';
        }

        // Remove all non-letter characters except spaces (this handles :, -, etc.)
        $cleanedName = preg_replace('/[^a-zA-Z\s]/', '', $name);

        // Split the name into words
        $words = preg_split('/\s+/', trim($cleanedName));

        // Filter out empty words
        $words = array_filter($words, fn($word) => !empty($word));

        // If only one word, return the whole word in uppercase
        if (count($words) === 1) {
            return strtoupper(reset($words));
        }

        // Get the first letter of each word for multiple words
        $initials = '';
        foreach ($words as $word) {
            $initials .= mb_substr($word, 0, 1);
        }

        return strtoupper($initials);
    }

    /**
     * Check if content is from Quill editor (simple HTML) or raw complex HTML
     * 
     * Quill editor typically produces: p, br, strong, em, u, s, a, ul, ol, li, h1-h6, blockquote, pre, code, img
     * If content contains advanced tags like style, head, body, div, span, script, meta, link, etc.
     * then it's raw HTML (not from Quill)
     * 
     * @return bool True if content is from Quill editor (simple HTML), False if raw/complex HTML
     */
    private function isQuillContent(?string $content): bool
    {
        if (empty($content)) {
            return false;
        }

        // If no HTML tags at all, it's not from Quill
        if ($content === strip_tags($content)) {
            return false;
        }

        // Advanced HTML tags that are NOT produced by Quill editor
        $advancedTags = [
            '<style',
            '<head',
            '<body',
            '<html',
            '<div',
            '<span',
            '<script',
            '<meta',
            '<link',
            '<table',
            '<thead',
            '<tbody',
            '<tr',
            '<td',
            '<th',
            '<form',
            '<input',
            '<button',
            '<select',
            '<textarea',
            '<iframe',
            '<canvas',
            '<svg',
            '<section',
            '<article',
            '<header',
            '<footer',
            '<nav',
            '<aside',
        ];

        $lowerContent = strtolower($content);

        foreach ($advancedTags as $tag) {
            if (str_contains($lowerContent, $tag)) {
                // Contains advanced HTML tags = raw HTML, NOT from Quill
                return false;
            }
        }

        // Only simple Quill tags found = from Quill editor
        return true;
    }

    /**
     * Log message and output to console
     */
    private function logAndOutput(string $level, string $message): void
    {
        $logLevel = $level === 'warn' ? 'warning' : $level;
        Log::channel('whitelabel')->{$logLevel}($message);

        match ($level) {
            'error' => $this->command->error($message),
            'warn' => $this->command->warn($message),
            default => $this->command->info($message),
        };
    }
}
