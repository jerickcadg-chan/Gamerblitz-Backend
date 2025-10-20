<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdatePaymentMethodCCTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethod = PaymentMethod::where('slug', 'CARDS')->first();

        if ($paymentMethod) {
            $paymentMethod->update([
                'additional_input' => [
                    [
                        'name' => 'first_name',
                        'type' => 'text',
                        'label' => 'First Name',
                        'options' => [],
                        'placeholder' => 'First Name',
                    ],
                    [
                        'name' => 'last_name',
                        'type' => 'text',
                        'label' => 'Last Name',
                        'options' => [],
                        'placeholder' => 'Last Name',
                    ],
                    [
                        'name' => 'cvc',
                        'type' => 'number',
                        'label' => 'CVC / CVN',
                        'options' => [],
                        'placeholder' => 'CVC / CVN',
                    ],
                    [
                        'name' => 'card_number',
                        'type' => 'number',
                        'label' => 'Card Number',
                        'options' => [],
                        'placeholder' => 'Card Number',
                    ],
                    [
                        'name' => 'expiry_month',
                        'type' => 'option',
                        'label' => 'Expiry Month',
                        'options' => [
                            ['name' => 'Januari',   'value' => '01'],
                            ['name' => 'Februari',  'value' => '02'],
                            ['name' => 'Maret',     'value' => '03'],
                            ['name' => 'April',     'value' => '04'],
                            ['name' => 'Mei',       'value' => '05'],
                            ['name' => 'Juni',      'value' => '06'],
                            ['name' => 'Juli',      'value' => '07'],
                            ['name' => 'Agustus',   'value' => '08'],
                            ['name' => 'September', 'value' => '09'],
                            ['name' => 'Oktober',   'value' => '10'],
                            ['name' => 'November',  'value' => '11'],
                            ['name' => 'Desember',  'value' => '12'],
                        ],
                        'placeholder' => 'Expiry Month',
                    ],
                    [
                        'name' => 'expiry_year',
                        'type' => 'text',
                        'label' => 'Expiry Year',
                        'options' => [],
                        'placeholder' => 'Expiry Year',
                    ],
                ]
            ]);
        }
    }
}
