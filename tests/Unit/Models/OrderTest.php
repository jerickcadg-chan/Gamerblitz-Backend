<?php

namespace Tests\Unit\Models;

use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function testFactory(): void
    {
        $order = Order::factory()
            ->customerUser()
            ->create();

        foreach (array_keys($order->getAttributes()) as $fillable) {
            $this->assertNotNull($order->{$fillable});
        }
    }

    public function testFillable(): void
    {
        $order = new Order();
        $this->assertEquals([
            'code', 'user_id', 'cust_email', 'cust_phone_number', 'product_item_id', 'cust_account',
            'payment_method', 'payment_status', 'order_status', 'qty', 'price', 'admin_fee', 'total_price',
            'total_income', 'note', 'expired_at', 'mg_invoice'
        ], $order->getFillable());
    }

    public function testDates(): void
    {
        $order = new Order();
        $this->assertEquals(['created_at', 'updated_at'], $order->getDates());
    }

    public function testToArray(): void
    {
        $order = Order::factory()->create()->fresh();
        $array = $order->toArray();

        $this->assertSame(array_keys($array), [
            "id",
            "client_id",
            "code",
            "user_id",
            "product_item_id",
            "discount_id",
            "cust_account",
            "cust_phone_number",
            "cust_email",
            "payment_method",
            "payment_status",
            "order_status",
            "qty",
            "price",
            "capital",
            "admin_fee",
            "discount_price",
            "total_price",
            "total_income",
            "payment_url",
            "payment_code",
            "payment_id",
            "mg_invoice",
            "note",
            "expired_at",
            "created_at",
            "updated_at"
        ]);
    }

    public function testOrderShouldHaveClient(): void
    {
        $order = Order::factory()->create();
        $this->assertInstanceOf(Client::class, $order->client);
    }
}
