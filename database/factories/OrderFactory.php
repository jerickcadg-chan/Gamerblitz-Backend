<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\ProductItem;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $productItem = ProductItem::find(rand(1, 5));

        return [
            'user_id' => rand(1,40),
            'cust_phone_number' => '62'. rand(1000000000, 9999999999),
            'product_item_id' => $productItem->id,
            'cust_account' => collect([
                'player_id' => rand(100000, 999999),
                'server_id' => rand(1000, 9999),
                'nickname' => $this->faker->firstName
            ])->toJson(),
            'payment_method' => 'bca',
            'payment_status' => 'settlement',
            'order_status' => 'done',
            'qty' => 1,
            'price' => $productItem->price,
            'capital' => $productItem->capital,
            'admin_fee' => 100,
            'discount_price' => 0,
            'total_price' => $productItem->price + 100,
            'total_income' => $productItem->price,
            'created_at' => now(),
            'updated_at' => \Carbon\Carbon::parse(now())->addMinutes(12)
        ];
    }
}
