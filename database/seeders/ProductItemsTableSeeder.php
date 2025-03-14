<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\Picture;

class ProductItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(\App\Services\CSVService $service)
    {
        Schema::disableForeignKeyConstraints();
        DB::table('product_items')->truncate();
        Schema::enableForeignKeyConstraints();

        // mobile legends
        ProductItem::insert([
            [
                'product_id' => 1,
                'name' => '86 Diamond',
                'stock' => 100,
                'price' => 21000,
                /*'capital' => 18368,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 1,
                'name' => '172 Diamond',
                'stock' => 100,
                'price' => 42000,
                /*'capital' => 35875,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 1,
                'name' => '257 Diamond',
                'stock' => 100,
                'price' => 42000,
                /*'capital' => 54817,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 1,
                'name' => '344 Diamond',
                'stock' => 100,
                'price' => 84000,
                /*'capital' => 71750,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 1,
                'name' => '429 Diamond',
                'stock' => 100,
                'price' => 104000,
                /*'capital' => 90692,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 1,
                'name' => '514 Diamond',
                'stock' => 100,
                'price' => 124000,
                /*'capital' => 109634,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 1,
                'name' => '600 Diamond',
                'stock' => 100,
                'price' => 145000,
                /*'capital' => 128002,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 1,
                'name' => '706 Diamond',
                'stock' => 100,
                'price' => 165000,
                /*'capital' => 145796,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 1,
                'name' => '878 Diamond',
                'stock' => 100,
                'price' => 207000,
                /*'capital' => 181671,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 1,
                'name' => '1050 Diamond',
                'stock' => 100,
                'price' => 249000,
                /*'capital' => 217546,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 1,
                'name' => '1412 Diamond',
                'stock' => 100,
                'price' => 330000,
                /*'capital' => 291592,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 1,
                'name' => '2195 Diamond',
                'stock' => 100,
                'price' => 480000,
                /*'capital' => 437388,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 1,
                'name' => '3073 Diamond',
                'stock' => 100,
                'price' => 697000,
                /*'capital' => 619059,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 1,
                'name' => '3688 Diamond',
                'stock' => 100,
                'price' => 815000,
                /*'capital' => 728980,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 1,
                'name' => '4032 Diamond',
                'stock' => 100,
                'price' => 899000,
                /*'capital' => 800730,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 1,
                'name' => '5532 Diamond',
                'stock' => 100,
                'price' => 1220000,
                /*'capital' => 1093470,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 1,
                'name' => 'Starlight / Twilight',
                'stock' => 100,
                'price' => 110000,
                /*'capital' => 87000,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 1,
                'name' => 'Starlight Plus',
                'stock' => 100,
                'price' => 230000,
                /*'capital' => 202000,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);

        // free fire
        ProductItem::insert([
            [
                'product_id' => 2,
                'name' => '50 Diamond',
                'stock' => 100,
                'price' => 8000,
                /*'capital' => 6182,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 2,
                'name' => '70 Diamond',
                'stock' => 100,
                'price' => 9500,
                /*'capital' => 8500,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 2,
                'name' => '100 Diamond',
                'stock' => 100,
                'price' => 14000,
                /*'capital' => 12364,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 2,
                'name' => '140 Diamond',
                'stock' => 100,
                'price' => 19000,
                /*'capital' => 17000,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 2,
                'name' => '210 Diamond',
                'stock' => 100,
                'price' => 28500,
                /*'capital' => 25500,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 2,
                'name' => '355 Diamond',
                'stock' => 100,
                'price' => 47000,
                /*'capital' => 42500,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 2,
                'name' => '425 Diamond',
                'stock' => 100,
                'price' => 56500,
                /*'capital' => 51000,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 2,
                'name' => '510 Diamond',
                'stock' => 100,
                'price' => 68000,
                /*'capital' => 61818,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 2,
                'name' => '720 Diamond',
                'stock' => 100,
                'price' => 95000,
                /*'capital' => 85000,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 2,
                'name' => '1075 Diamond',
                'stock' => 100,
                'price' => 142000,
                /*'capital' => 127500,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 2,
                'name' => '2000 Diamond',
                'stock' => 100,
                'price' => 256000,
                /*'capital' => 231818,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 2,
                'name' => '7290 Diamond',
                'stock' => 100,
                'price' => 935000,
                /*'capital' => 850000,*/
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);

        $items = $service->toArray('./database/imports/products.csv');

        foreach ($items as $item) {
            ProductItem::create([
                'product_id' => $item['product_id'],
                'name' => $item['name'],
                'stock' => 100,
                'price' => $item['price'],
                /*'capital' => $item['capital'],*/
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
