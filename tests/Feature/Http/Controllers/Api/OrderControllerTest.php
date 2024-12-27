<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Mail\SendSettlementNotif;
use App\Models\Balance;
use App\Models\BalanceHistory;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductClient;
use App\Models\ProductItem;
use App\Models\ProductItemClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    public function test_order_failed_email_should_be_email_format()
    {
        $response = $this->postJson('/api/order', [
            'email' => 'not-email-format',
            'cust_phone_number' => '081234567890',
            'product_item_id' => 1,
            'qty' => 1,
            'payment_method' => 'cash'
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_order_failed_cust_phone_number_should_be_required()
    {
        $response = $this->postJson('/api/order', [
            'product_item_id' => 1,
            'qty' => 1,
            'payment_method' => 'cash'
        ]);
        $response->assertStatus(422);

        $response->assertJsonValidationErrors('cust_phone_number');
    }

    public function test_order_failed_product_item_id_should_exists_in_product_items_table()
    {
        $response = $this->postJson('/api/order', [
            'cust_phone_number' => '081234567890',
            'product_item_id' => 1,
            'qty' => 1,
            'payment_method' => 'cash'
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('product_item_id');
    }

    public function test_order_failed_qty_should_be_required()
    {
        $response = $this->postJson('/api/order', [
            'cust_phone_number' => '081234567890',
            'product_item_id' => 1,
            'payment_method' => 'cash'
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('qty');
    }

    public function test_order_failed_payment_method_should_be_required()
    {
        $response = $this->postJson('/api/order', [
            'cust_phone_number' => '081234567890',
            'product_item_id' => 1,
            'qty' => 1
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('payment_method');
    }

    public function test_order_failed_because_stock_not_enough()
    {
        $payementMethod = PaymentMethod::factory()->create(['vendor' => PaymentMethod::SALDO]);
        $product = Product::factory()->create();
        $productItem = ProductItem::factory()
            ->for($product)
            ->create([
                'stock' => 0
            ]);

        $response = $this->postJson('/api/order', [
            'cust_phone_number' => '081234567890',
            'product_item_id' => $productItem->id,
            'qty' => 1,
            'payment_method' => $payementMethod->name,
        ]);

        $response->assertStatus(400);
        $this->assertSame($response->json(), $this->response_status_warning(trans('order.out_of_stock')));
    }

    public function test_order_failed_bacause_no_balance_without_auth(): void
    {
        $payementMethod = PaymentMethod::factory()->create(['vendor' => PaymentMethod::SALDO]);
        $product = Product::factory()->create();
        $productItem = ProductItem::factory()
            ->for($product)
            ->create();
        $response = $this->postJson('/api/order', [
            'cust_phone_number' => '081234567890',
            'product_item_id' => $productItem->id,
            'qty' => 1,
            'payment_method' => $payementMethod->name,
        ]);
        $response->assertStatus(400);
        $this->assertSame($response->json(), $this->response_status_warning(trans('order.no_balance')));
    }

    public function test_order_failed_bacause_no_balance_with_auth(): void
    {
        $payementMethod = PaymentMethod::factory()->create(['vendor' => PaymentMethod::SALDO]);
        $product = Product::factory()->create();
        $productItem = ProductItem::factory()
            ->for($product)
            ->create();

        $response = $this->postJson('/api/order', [
            'cust_phone_number' => '081234567890',
            'product_item_id' => $productItem->id,
            'qty' => 1,
            'payment_method' => $payementMethod->name,
        ]);
        $response->assertStatus(400);
        $this->assertSame($response->json(), $this->response_status_warning(trans('order.no_balance')));
    }

    public function test_order_success_using_saldo()
    {
        try {
            config(['array.mail.notification' => 'test@test.com']);
            Mail::fake();
            $payementMethod = PaymentMethod::factory()->create(['vendor' => PaymentMethod::SALDO]);
            $product = Product::factory()->create();
            $amountBalance = 1000000;
            $balance = Balance::factory()
                ->for($this->user)
                ->create([
                    'amount' => $amountBalance,
                ]);

            ProductClient::factory()
                ->for($product, 'product')
                ->for($this->user->client)
                ->create();

            $this->actingAs($this->user);

            $productItem = ProductItem::factory()
                ->for($product)
                ->count(10)
                ->create([
                    'price' => 0,
                    'capital' => 5000,
                    'price_reseller' => 0,
                    'type' => 'topup'
                ])
                ->each(function (ProductItem $productItem) {
                    $productItem->productItemClients()->save(ProductItemClient::factory()->make([
                        'margin' => 5,
                        'client_id' => $this->user->client->id
                    ]));
                });

            ProductItemClient::factory()
                ->for($productItem->first())
                ->for($this->user->client)
                ->create();

            $productItemId = $productItem->random()->id;

            $response = $this->postJson('/api/order', [
                'cust_phone_number' => '081234567890',
                'product_item_id' => $productItemId,
                'qty' => 1,
                'payment_method' => $payementMethod->name,
            ]);

            /** @var Order $order */
            $order = Order::whereProductItemId($productItemId)->first();
            $this->assertSame(Order::SETTLEMENT, $order->payment_status, 'Payment status should be settlement');
            $this->assertSame(Order::INPROCESS, $order->order_status, 'Order status should be in-process');
            $this->assertTrue($balance->fresh()->amount == $amountBalance - $order->total_price, 'Balance should be reduced');

            /** @var BalanceHistory $balanceHistory */
            $balanceHistory = BalanceHistory::whereBalanceId($balance->id)->first();
            $this->assertTrue($balanceHistory->latest_balance == $balance->fresh()->amount, 'Balance history should be created');
            $this->assertTrue($balanceHistory->amount == -$order->total_price, 'Balance history amount should be equal to order total price');

            $paymentHistory = $order->histories->firstWhere('type', 'payment');
            $orderHistory = $order->histories->firstWhere('type', 'order');
            $this->assertSame(Order::SETTLEMENT, $paymentHistory->status, 'History payment status should be settlement');
            $this->assertSame(Order::INPROCESS, $orderHistory->status, 'History order status should be in-process');

            $response->assertStatus(200);
            Mail::assertQueued(SendSettlementNotif::class);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function test_order_success_using_qris_xendit()
    {
        try {
            config(['array.mail.notification' => 'test@test.com']);
            config(['array.xendit.url' => 'https://api.xendit.com']);
            $payementMethod = PaymentMethod::factory()->qris()->create();
            $product = Product::factory()->create();

            $xenditResponse = [
                "id" => "qr_61cb3576-3a25-4d35-8d15-0e8e3bdba4f2",
                "reference_id" => "order-id-1666420204",
                "business_id" => "58cd618ba0464eb64acdb246",
                "type" => "DYNAMIC",
                "currency" => "IDR",
                "amount" => 10000,
                "channel_code" => "ID_DANA",
                "status" => "ACTIVE",
                "qr_string" => "QRSTRING",
                "expires_at" => "2022-10-23T09:56:43.60445Z",
                "created" => "2022-10-22T06:30:05.86474Z",
                "updated" => "2022-10-22T06:30:05.86474Z",
                "basket" => null,
                "metadata" => null
            ];
            Http::fake([
                config('array.xendit.url') . '/qr_codes' => Http::response($xenditResponse, 200),
            ]);

            ProductClient::factory()
                ->for($product, 'product')
                ->for($this->user->client)
                ->create();

            $this->actingAs($this->user);

            $productItem = ProductItem::factory()
                ->for($product)
                ->count(10)
                ->create([
                    'price' => 0,
                    'capital' => 5000,
                    'price_reseller' => 0,
                    'type' => 'topup'
                ])
                ->each(function (ProductItem $productItem) {
                    $productItem->productItemClients()->save(ProductItemClient::factory()->make([
                        'margin' => 5,
                        'client_id' => $this->user->client->id
                    ]));
                });

            ProductItemClient::factory()
                ->for($productItem->first())
                ->for($this->user->client)
                ->create();

            $productItemId = $productItem->random()->id;

            $response = $this->postJson('/api/order', [
                'cust_phone_number' => '081234567890',
                'product_item_id' => $productItemId,
                'qty' => 1,
                'payment_method' => $payementMethod->name,
            ]);

            /** @var Order $order */
            $order = Order::whereProductItemId($productItemId)->first();
            $this->assertSame((float) $order->total_price, $order->price + ($order->price * $payementMethod->admin_fee / 100), "Total price should be equal to price + admin fee");
            $this->assertSame(Order::PENDING, $order->payment_status, 'Payment status should be settlement');
            $this->assertSame(Order::WAITING_PAYMENT, $order->order_status, 'Order status should be in-process');
            $this->assertSame(null, $order->payment_url, 'Payment url should be null');
            $this->assertSame($xenditResponse['qr_string'], $order->payment_code, 'Payment code should be equal to xendit qr');
            $this->assertSame($xenditResponse['id'], $order->payment_id, 'Payment id should be equal to xendit id');

            $paymentHistory = $order->histories->firstWhere('type', 'payment');
            $orderHistory = $order->histories->firstWhere('type', 'order');
            $this->assertSame(Order::PENDING, $paymentHistory->status, 'History payment status should be settlement');
            $this->assertSame(Order::WAITING_PAYMENT, $orderHistory->status, 'History order status should be in-process');

            $response->assertStatus(200);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function test_order_success_using_va_xendit()
    {
        try {
            config(['array.mail.notification' => 'test@test.com']);
            config(['array.xendit.url' => 'https://api.xendit.com']);
            $payementMethod = PaymentMethod::factory()->va()->create();
            $product = Product::factory()->create();

            $xenditResponse = [
                "invoice_url" => "https://invoice-staging.xendit.co/web/invoices/5f4b7a3c0f0d4d0001b2f3d7",
                "id" => "5f4b7a3c0f0d4d0001b2f3d7",
            ];
            Http::fake([
                config('array.xendit.url') . '/v2/invoices' => Http::response($xenditResponse, 200),
            ]);

            ProductClient::factory()
                ->for($product, 'product')
                ->for($this->user->client)
                ->create();

            $this->actingAs($this->user);

            $productItem = ProductItem::factory()
                ->for($product)
                ->count(10)
                ->create([
                    'price' => 0,
                    'capital' => 5000,
                    'price_reseller' => 0,
                    'type' => 'topup'
                ])
                ->each(function (ProductItem $productItem) {
                    $productItem->productItemClients()->save(ProductItemClient::factory()->make([
                        'margin' => 5,
                        'client_id' => $this->user->client->id
                    ]));
                });

            ProductItemClient::factory()
                ->for($productItem->first())
                ->for($this->user->client)
                ->create();

            $productItemId = $productItem->random()->id;

            $response = $this->postJson('/api/order', [
                'cust_phone_number' => '081234567890',
                'product_item_id' => $productItemId,
                'qty' => 1,
                'payment_method' => $payementMethod->name,
            ]);

            /** @var Order $order */
            $order = Order::whereProductItemId($productItemId)->first();
            $this->assertSame((float) $order->total_price, $order->price + $payementMethod->admin_fee, "Total price should be equal to price + admin fee");
            $this->assertSame(Order::PENDING, $order->payment_status, 'Payment status should be settlement');
            $this->assertSame(Order::WAITING_PAYMENT, $order->order_status, 'Order status should be in-process');
            $this->assertSame($xenditResponse['invoice_url'], $order->payment_url, 'Payment url should be equal to xendit invoice url');
            $this->assertSame($xenditResponse['id'], $order->payment_id, 'Payment id should be equal to xendit id');

            $paymentHistory = $order->histories->firstWhere('type', 'payment');
            $orderHistory = $order->histories->firstWhere('type', 'order');
            $this->assertSame(Order::PENDING, $paymentHistory->status, 'History payment status should be settlement');
            $this->assertSame(Order::WAITING_PAYMENT, $orderHistory->status, 'History order status should be in-process');

            $response->assertStatus(200);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->generateSuperAdminUser();
    }
}
