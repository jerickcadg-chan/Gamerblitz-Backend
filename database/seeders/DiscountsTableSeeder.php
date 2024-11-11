<?php

use Illuminate\Database\Seeder;
use App\Models\Discount;

class DiscountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        \Illuminate\Support\Facades\DB::table('discounts')->truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        factory(Discount::class, 10)->create();
    }
}
