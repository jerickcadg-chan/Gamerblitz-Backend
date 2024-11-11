<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Services\OrderService;
use App\Models\Order;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('orders')->truncate();
        DB::table('order_histories')->truncate();
        Schema::enableForeignKeyConstraints();

        Order::factory()->count(20)->create();

        $orders = Order::all();

        foreach ($orders as $key => $order) {
            $status = [
                [
                    'type' => 'payment',
                    'status' => 'pending'
                ],
                [
                    'type' => 'payment',
                    'status' => 'sattlement'
                ],
                [
                    'type' => 'order',
                    'status' => 'in-process'
                ],
                [
                    'type' => 'order',
                    'status' => 'done'
                ],
            ];

            for ($i=0; $i <= 3; $i++) {
                DB::table('order_histories')->insert([
                    'order_id' => $order->id,
                    'status' => $status[$i]['status'],
                    'type' => $status[$i]['type'],
                    'created_at' => \Carbon\Carbon::parse(now())->addMinutes($i * 3),
                    'updated_at' => \Carbon\Carbon::parse(now())->addMinutes($i * 3),
                ]);
            }
        }
    }
}
