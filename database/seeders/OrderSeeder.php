<?php

namespace Database\Seeders;

use App\Constants\StatusConst;
use App\Models\Order;
use App\Models\User;
use App\Models\ProductItem;
use App\Models\Discount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::pluck('id')->all();
        $items = ProductItem::pluck('id')->all();
        $discounts = Discount::pluck('id')->all();

        if (empty($items)) {
            $this->command->warn('No product_items found, skipping OrderSeeder.');
            return;
        }

        foreach (range(1, 20) as $i) {
            $userId = fake()->optional()->randomElement($users);
            $productItemId = fake()->randomElement($items);
            $discountId = fake()->optional()->randomElement($discounts);

            $qty = fake()->numberBetween(1, 5);
            $price = fake()->randomFloat(2, 10000, 200000);
            $capital = $price - fake()->numberBetween(1000, 5000);
            $adminFee = fake()->randomElement([0, 1000, 2000]);
            $discountPrice = $discountId ? fake()->randomElement([1000, 2000, 5000]) : 0;
            $totalPrice = ($price * $qty) + $adminFee - $discountPrice;
            $totalIncome = ($price - $capital) * $qty + $adminFee - $discountPrice;

            $exchangeRate = 0.000065; // contoh IDR ke USD
            $convertedPrice = round($price * $exchangeRate, 2);
            $convertedCapital = round($capital * $exchangeRate, 2);
            $convertedAdminFee = round($adminFee * $exchangeRate, 2);
            $convertedDiscountPrice = round($discountPrice * $exchangeRate, 2);
            $convertedTotalPrice = round($totalPrice * $exchangeRate, 2);
            $convertedTotalIncome = round($totalIncome * $exchangeRate, 2);

            Order::create([
                'code' => strtoupper(Str::random(10)),
                'provider_ref' => strtoupper(Str::random(12)),
                'user_id' => $userId,
                'product_item_id' => $productItemId,
                'discount_id' => $discountId,
                'cust_account' => fake()->userName(),
                'cust_phone_number' => fake()->numerify('628#########'),
                'cust_email' => fake()->safeEmail(),
                'payment_method' => fake()->randomElement(['bca-va','qris','gopay']),
                'status' => StatusConst::SUCCESS,
                'qty' => $qty,
                'price' => $price,
                'capital' => $capital,
                'admin_fee' => $adminFee,
                'discount_price' => $discountPrice,
                'total_price' => $totalPrice,
                'total_income' => $totalIncome,
                'payment_url' => fake()->url(),
                'payment_code' => fake()->numerify('###-###'),
                'payment_id' => fake()->uuid(),
                'note' => fake()->sentence(),
                'expired_at' => now()->addMinutes(30),
                'currency_code' => 'IDR',
                'converted_currency_code' => 'USD',
                'exchange_rate' => $exchangeRate,
                'converted_price' => $convertedPrice,
                'converted_capital' => $convertedCapital,
                'converted_admin_fee' => $convertedAdminFee,
                'converted_discount_price' => $convertedDiscountPrice,
                'converted_total_price' => $convertedTotalPrice,
                'converted_total_income' => $convertedTotalIncome,
            ]);
        }
    }
}
