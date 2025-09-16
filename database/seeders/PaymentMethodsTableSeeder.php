<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PaymentMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('payment_methods')->truncate();
        Schema::enableForeignKeyConstraints();

        // $bca = PaymentMethod::create([
        //     'name' => 'atm_bca',
        //     'admin_fee' => 0,
        //     'admin_type' => 'no-admin',
        //     'slug' => 'atm_bca',
        //     'vendor' => null
        // ]);
        //
        // $bca->picture()->create([
        //     'path' => 'img/payment',
        //     'file_name' => 'logo_atm_bca.png',
        // ]);

        $dana = PaymentMethod::create([
            'name' => 'dana',
            'admin_fee' => 1.50,
            'admin_type' => 'percentage',
            'slug' => '#dana',
            'vendor' => 'xendit',
            'currency_code' => 'IDR',
            'category' => 'E-Wallet',
        ]);

        $dana->picture()->create([
            'path' => 'img/payment',
            'file_name' => 'logo_dana.png',
        ]);

        $shopeepay = PaymentMethod::create([
            'name' => 'shopeepay',
            'admin_fee' => 2,
            'admin_type' => 'percentage',
            'slug' => '#shopeepay',
            'vendor' => 'xendit',
            'currency_code' => 'IDR',
            'category' => 'E-Wallet',
        ]);

        $shopeepay->picture()->create([
            'path' => 'img/payment',
            'file_name' => 'logo_shopeepay.png',
        ]);

        $alfamart = PaymentMethod::create([
            'name' => 'alfamart',
            'admin_fee' => 5000,
            'admin_type' => 'nominal',
            'slug' => '#alfamart',
            'vendor' => 'xendit',
            'currency_code' => 'IDR',
            'category' => 'E-Wallet',
        ]);

        $alfamart->picture()->create([
            'path' => 'img/payment',
            'file_name' => 'logo_alfamart.png',
        ]);

        PaymentMethod::insert([
            [
                'name' => 'saldo',
                'admin_fee' => 0,
                'admin_type' => 'no-admin',
                'slug' => 'saldo',
                'vendor' => 'saldo',
                'currency_code' => 'IDR',
                'category' => 'E-Wallet',
            ],
            [
                'name' => 'qris',
                'admin_fee' => '0.90',
                'admin_type' => 'percentage',
                'slug' => '#ewallet',
                'vendor' => 'xendit',
                'currency_code' => 'IDR',
                'category' => 'E-Wallet',
            ]
        ]);
    }
}
