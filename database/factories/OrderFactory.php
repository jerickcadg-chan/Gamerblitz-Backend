<?php

namespace Database\Factories;

use App\Constants\StatusConst;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\ProductItem;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

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
        $productItem = ProductItem::factory()->create();

        return [
            'user_id' => User::factory(),
            'client_id' => Client::factory(),
            'cust_phone_number' => '62' . rand(1000000000, 9999999999),
            'product_item_id' => $productItem->id,
            'cust_account' => collect([
                'player_id' => rand(100000, 999999),
                'server_id' => rand(1000, 9999),
                'nickname' => $this->faker->firstName
            ])->toJson(),
            'payment_method' => 'bca',
            'payment_status' => 'settlement',
            'order_status' => StatusConst::SUCCESS,
            'qty' => 1,
            'price' => $productItem->price,
            'capital' => $productItem->capital,
            'admin_fee' => 100,
            'discount_price' => 0,
            'total_price' => $productItem->price + 100,
            'total_income' => $productItem->price - $productItem->capital,
            'created_at' => now(),
            'updated_at' => \Carbon\Carbon::parse(now())->addMinutes(12)
        ];
    }

    public function qris()
    {
        return $this->state(function (array $attributes) {
            return [
                'payment_method' => 'qris',
                'admin_fee' => 2000
            ];
        });
    }

    public function va()
    {
        return $this->state(function (array $attributes) {
            return [
                'payment_id' => '598d91b1191029596846047f',
                'payment_method' => 'va',
                'payment_status' => Order::PENDING,
                'admin_fee' => 5000,
            ];
        });
    }

    public function customerUser()
    {
        Artisan::call('db:seed', ['--class' => 'RolesTableSeeder']);
        $user = User::factory()->create([
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password'
        ]);

        $user->assignRole('Customer');

        return $this->state(function (array $attributes) use ($user) {
            return [
                'user_id' => $user->id
            ];
        });
    }
}
