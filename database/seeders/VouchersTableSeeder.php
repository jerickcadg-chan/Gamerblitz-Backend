<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Voucher;

class VouchersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        \Illuminate\Support\Facades\DB::table('vouchers')->truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        Voucher::factory()->count(50)->create();
    }
}
