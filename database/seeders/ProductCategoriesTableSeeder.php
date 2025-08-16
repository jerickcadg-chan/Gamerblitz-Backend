<?php

use Illuminate\Database\Seeder;
use App\Models\ProductCategory;

class ProductCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        \Illuminate\Support\Facades\DB::table('product_categories')->truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        factory(ProductCategory::class, 10)->create();
    }
}
