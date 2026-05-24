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
    public function run(): void
    {
        $path = database_path('imports/product_items_v2.sql');

        if (! file_exists($path)) {
            $this->command->warn("SQL file not found at: {$path}");
            return;
        }

        $sql = file_get_contents($path);
        DB::unprepared($sql);
    }
}
