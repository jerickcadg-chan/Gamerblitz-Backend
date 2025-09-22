<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use Illuminate\Support\Str;

class SeedGpdsGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-gpds-games';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed gpds games from gpds-games.csv';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $imageMap = json_decode(
            file_get_contents(resource_path('data/gpds-games-images.json')),
            true
        );
        $csv = Reader::createFromPath(resource_path('data/gpds-games.csv'), 'r');

        $productInserted = 0;
        $categoryInserted = 0;

        $countryCodes = [
            'Brazil' => 'BR',
            'Indonesia' => 'ID',
            'Malaysia' => 'MY',
            'Philippines' => 'PH',
            'Singapore' => 'SG',
            'United States' => 'US'
            // ...add more if needed later
        ];

        try {
            DB::beginTransaction();

            foreach ($csv as $i => $record) {
                // skip header
                if ($i === 0) {
                    continue;
                }

                $name = $record[0];
                $code = $record[1];
                $categoryName = $record[2];
                $provider = $record[3] === "LG" ? "lapakgaming" : "manual";
                $countryName = $record[4] ?? "";
                $country = $countryCodes[$countryName] ?? "PH";

                $category = ProductCategory::firstOrCreate(
                    ['name' => $categoryName],
                    ['slug' => Str::slug($categoryName)],
                );
                $product = Product::firstOrCreate([
                    'name' => $name,
                    'code' => $code,
                ], [
                        'code' => $code,
                        'product_category_id' => $category->id,
                        'description' => '',
                        'company' => '',
                        'how_to_order' => '',
                        'input_format' => null,
                        'slug' => slugify($name),
                        'markup_reseller_silver' => 0,
                        'markup_reseller_gold' => 0,
                        'markup_reseller_vip' => 0,
                        'markup_user' => 0,
                        // 'product_joki' => '',
                        // 'default_picture' => '',
                        // 'default_cover' => '',
                        // 'ordering' => '',
                        'status' => 'active',
                        'provider' => $provider,
                        'provider_code' => $code,
                        'provider_country' => $country,
                        // 'check_uid' => '',
                        // 'meta_title' => '',
                        // 'meta_keyword' => '',
                        // 'meta_description' => '',
                    ]);
                if ($category->wasRecentlyCreated) {
                    $categoryInserted++;
                }
                if ($product->wasRecentlyCreated) {
                    $imageUrl = $imageMap[$name];
                    $response = Http::get($imageUrl);
                    if ($response->successful()) {
                        $path = "image/" . $product->slug . '.' . pathinfo($imageUrl, PATHINFO_EXTENSION);
                        Storage::put($path, $response->body());
                        $product->default_picture = $path;
                        $product->save();
                    } else {
                        echo "Failed to fetch image for $name -> $imageUrl";
                    }
                    $productInserted++;
                }
                // print_r($product->toArray());
            }

            DB::commit();

            echo "Inserted $productInserted new games and $categoryInserted new categories.";
        } catch (\Exception $e) {
            echo $e->getMessage();
            DB::rollBack();
        }
    }
}
