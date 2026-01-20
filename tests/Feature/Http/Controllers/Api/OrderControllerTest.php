<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Constants\ProductItemTypeConstant;
use App\Mail\OrderAccountSucceed;
use App\Mail\SendOrderNotif;
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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

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
        $payementMethod = PaymentMethod::factory()->create(['vendor' => PaymentMethod::BALANCE]);
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
        $this->assertSame($response->json("message"), trans('order.out_of_stock'));
    }

    public function test_order_failed_bacause_no_balance_without_auth(): void
    {
        $payementMethod = PaymentMethod::factory()->create(['vendor' => PaymentMethod::BALANCE]);
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
        $this->assertSame($response->json("message"), trans('auth.you_should_login'));
    }

    public function test_order_failed_bacause_no_balance_with_auth(): void
    {
        $payementMethod = PaymentMethod::factory()->create(['vendor' => PaymentMethod::BALANCE]);
        $product = Product::factory()->create();
        $productItem = ProductItem::factory()
            ->for($product)
            ->create();

        $amountBalance = 0;
        $balance = Balance::factory()
            ->for($user = $this->user)
            ->create([
                'amount' => $amountBalance,
            ]);

        $this->actingAs($user);

        $response = $this->postJson('/api/order', [
            'cust_phone_number' => '081234567890',
            'product_item_id' => $productItem->id,
            'qty' => 1,
            'payment_method' => $payementMethod->name,
        ]);
        $response->assertStatus(400);
        $this->assertSame($response->json("message"), trans('order.no_balance'));
    }

    public function test_order_success_using_saldo_for_topup()
    {
        try {
            config(['array.mail.notification' => 'test@test.com']);
            Mail::fake();
            $payementMethod = PaymentMethod::factory()->create(['vendor' => PaymentMethod::BALANCE]);
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

            /** @var ProductItem $productItem */
            $productItem = ProductItem::whereId($productItemId)->first();

            /** @var Order $order */
            $order = Order::whereProductItemId($productItemId)->first();
            $this->assertSame((float) $order->price, $productItem->margin_price);
            $this->assertSame($payementMethod->name, $order->payment_method, 'Payment method should be same with $payementMethod->name');
            $this->assertSame($this->user->id, $order->user->id, 'User should be same with $user');
            $this->assertSame($this->user->client->toArray(), $order->client->toArray(), 'Client should be same with $user->client');
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

    public function test_order_success_using_saldo_for_account()
    {
        try {
            config(['array.mail.notification' => 'test@test.com']);
            Mail::fake();
            $payementMethod = PaymentMethod::factory()->create(['vendor' => PaymentMethod::BALANCE]);
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
                    'type' => ProductItemTypeConstant::ACCOUNT,
                    'stock' => 1
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

            /** @var ProductItem $productItem */
            $productItem = ProductItem::whereId($productItemId)->first();
            $this->assertSame(0, $productItem->stock, 'Stock shouldbe 0');

            /** @var Order $order */
            $order = Order::whereProductItemId($productItemId)->first();
            $this->assertSame((float) $order->price, $productItem->margin_price);
            $this->assertSame($payementMethod->name, $order->payment_method, 'Payment method should be same with $payementMethod->name');
            $this->assertSame($this->user->id, $order->user->id, 'User should be same with $user');
            $this->assertSame($this->user->client->toArray(), $order->client->toArray(), 'Client should be same with $user->client');
            $this->assertSame(Order::SETTLEMENT, $order->payment_status, 'Payment status should be settlement');
            $this->assertSame(Order::DONE, $order->order_status, 'Order status should be done');
            $this->assertTrue($balance->fresh()->amount == $amountBalance - $order->total_price, 'Balance should be reduced');

            /** @var BalanceHistory $balanceHistory */
            $balanceHistory = BalanceHistory::whereBalanceId($balance->id)->first();
            $this->assertTrue($balanceHistory->latest_balance == $balance->fresh()->amount, 'Balance history should be created');
            $this->assertTrue($balanceHistory->amount == -$order->total_price, 'Balance history amount should be equal to order total price');

            $paymentHistory = $order->histories->firstWhere('type', 'payment');
            $orderHistory = $order->histories->firstWhere('type', 'order');
            $this->assertSame(Order::SETTLEMENT, $paymentHistory->status, 'History payment status should be settlement');
            $this->assertSame(Order::DONE, $orderHistory->status, 'History order status should be done');

            $response->assertStatus(200);
            Mail::assertQueued(SendSettlementNotif::class);
            Mail::assertQueued(OrderAccountSucceed::class);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function test_order_success_using_qris_xendit_for_topup()
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

            /** @var ProductItem $productItem */
            $productItem = ProductItem::whereId($productItemId)->first();

            /** @var Order $order */
            $order = Order::whereProductItemId($productItemId)->first();

            $this->assertSame((float) $order->price, $productItem->margin_price);
            $this->assertSame($payementMethod->name, $order->payment_method, 'Payment method should be same with $payementMethod->name');
            $this->assertSame($this->user->id, $order->user->id, 'User should be same with $user');
            $this->assertSame($this->user->client->toArray(), $order->client->toArray(), 'Client should be same with $user->client');
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

    public function test_order_success_using_va_xendit_for_topup()
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

            /** @var ProductItem $productItem */
            $productItem = ProductItem::whereId($productItemId)->first();

            /** @var Order $order */
            $order = Order::whereProductItemId($productItemId)->first();

            $this->assertSame((float) $order->price, $productItem->margin_price);
            $this->assertSame($payementMethod->name, $order->payment_method, 'Payment method should be same with $payementMethod->name');
            $this->assertSame($this->user->id, $order->user->id, 'User should be same with $user');
            $this->assertSame($this->user->client->toArray(), $order->client->toArray(), 'Client should be same with $user->client');
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

    public function test_xendit_callback_token_didnt_register_yet(): void
    {
        /** @var Order $order */
        $order = Order::factory()->va()->create();

        $response = $this->withHeaders([
            'x-callback-token' => "invalid-token"
        ])->postJson(route('callback.xendit'), [
            'id' => $order->payment_id,
            'external_id' => $order->code,
        ]);

        $response->assertStatus(400);
        $this->assertSame("callback token didn't register yet, or invalid token!!!!", $response->json("message"));
    }

    public function test_xendit_callback_didnt_bring_id_or_correct_code(): void
    {
        /** @var Order $order */
        $order = Order::factory()->va()->create();

        $response = $this->withHeaders([
            'x-callback-token' => $this->user->client->xendit_callback_token
        ])->postJson(route('callback.xendit'), [
            'external_id' => $order->code . "invalid-code",
        ]);

        $response->assertStatus(400);
        $this->assertSame("Order not found", $response->json("message"));
    }

    public function test_xendit_callback_qr_expired(): void
    {
        $this->markTestIncomplete();
        $order = Order::factory()->qris()->create();

        $this->postJson('/order/xendit', []);
    }

    public function test_xendit_callback_qr_success(): void
    {
        $this->markTestIncomplete();
    }

    public function test_xendit_callback_va_expired(): void
    {
        Mail::fake();
        /** @var Order $order */
        $order = Order::factory()->va()->create([
            'cust_email' => 'mail@mail.test',
        ]);

        $response = $this->withHeaders([
            'x-callback-token' => $this->user->client->xendit_callback_token
        ])->postJson(route('callback.xendit'), [
            'id' => $order->payment_id,
            'external_id' => $order->code,
            'status' => 'EXPIRED'
        ]);

        $response->assertOk();
        $order = $order->refresh();
        $this->assertSame(Order::EXPIRED, $order->order_status);
        Mail::assertSent(SendOrderNotif::class);
    }

    public function test_xendit_callback_va_expired_without_email(): void
    {
        /** @var Order $order */
        $order = Order::factory()->va()->create();

        $response = $this->withHeaders([
            'x-callback-token' => $this->user->client->xendit_callback_token
        ])->postJson(route('callback.xendit'), [
            'id' => $order->payment_id,
            'external_id' => $order->code,
            'status' => 'EXPIRED'
        ]);

        $response->assertOk();
        $order = $order->refresh();
        $this->assertSame(Order::EXPIRED, $order->order_status);
    }

    public function test_xendit_callback_va_complete_paid_for_topup(): void
    {
        config(['array.mail.notification' => 'test@test.com']);
        Mail::fake();
        $product = Product::factory()->create([
            'name' => 'Mobile Legends'
        ]);
        ProductClient::factory()
            ->for($product, 'product')
            ->for($this->user->client)
            ->create();

        $productItem = ProductItem::factory()
            ->for($product)
            ->create([
                'code' => 'ML14',
                'price' => 0,
                'capital' => 5000,
                'price_reseller' => 0,
                'type' => 'topup'
            ]);

        $productItem->productItemClients()
            ->save(ProductItemClient::factory()->make([
                'margin' => 5,
                'client_id' => $this->user->client->id
            ]));

        /** @var Order $order */
        $order = Order::factory()
            ->for($productItem)
            ->va()
            ->create([
                'cust_email' => 'mail@mail.test',
                'price' => $productItem->real_price,
                'discount_price' => 0,
            ]);

        $mitraGamersMockData = $this->mitraGamersMockSuccess();

        $response = $this->withHeaders([
            'x-callback-token' => $this->user->client->xendit_callback_token
        ])->postJson(route('callback.xendit'), [
            'id' => $order->payment_id,
            'external_id' => $order->code,
            'status' => 'COMPLETED'
        ]);

        $response->assertOk();

        $order = $order->refresh();
        $this->assertSame($order->mg_invoice, $mitraGamersMockData['payload']['id']);
        $this->assertSame(Order::INPROCESS, $order->order_status);
        $this->assertSame(Order::SETTLEMENT, $order->payment_status);

        Mail::assertSent(SendOrderNotif::class);
        Mail::assertQueued(SendSettlementNotif::class);
    }

    public function test_xendit_callback_va_complete_paid_for_topup_error_create_mitra_gamers(): void
    {
        config(['array.mail.notification' => 'test@test.com']);
        Mail::fake();
        $product = Product::factory()->create([
            'name' => 'Mobile Legends'
        ]);
        ProductClient::factory()
            ->for($product, 'product')
            ->for($this->user->client)
            ->create();

        $productItem = ProductItem::factory()
            ->for($product)
            ->create([
                'code' => 'ML14',
                'price' => 0,
                'capital' => 5000,
                'price_reseller' => 0,
                'type' => 'topup'
            ]);

        $productItem->productItemClients()
            ->save(ProductItemClient::factory()->make([
                'margin' => 5,
                'client_id' => $this->user->client->id
            ]));

        /** @var Order $order */
        $order = Order::factory()
            ->for($productItem)
            ->va()
            ->create([
                'cust_email' => 'mail@mail.test',
                'price' => $productItem->real_price,
                'discount_price' => 0,
            ]);

        $this->mitraGamersMockDataFail();

        $response = $this->withHeaders([
            'x-callback-token' => $this->user->client->xendit_callback_token
        ])->postJson(route('callback.xendit'), [
            'id' => $order->payment_id,
            'external_id' => $order->code,
            'status' => 'COMPLETED'
        ]);

        $response->assertOk();

        $order = $order->refresh();
        $this->assertSame($order->mg_invoice, null);
        $this->assertSame(Order::INPROCESS, $order->order_status);
        $this->assertSame(Order::SETTLEMENT, $order->payment_status);

        Mail::assertSent(SendOrderNotif::class);
        Mail::assertQueued(SendSettlementNotif::class);
    }

    public function test_xendit_callback_va_complete_paid_for_account(): void
    {
        config(['array.mail.notification' => 'test@test.com']);
        Mail::fake();
        $product = Product::factory()->create([
            'name' => 'Mobile Legends'
        ]);
        ProductClient::factory()
            ->for($product, 'product')
            ->for($this->user->client)
            ->create();

        $productItem = ProductItem::factory()
            ->for($product)
            ->create([
                'code' => 'ML14',
                'price' => 0,
                'capital' => 5000,
                'price_reseller' => 0,
                'type' => ProductItemTypeConstant::ACCOUNT,
                'stock' => 1
            ]);

        $productItem->productItemClients()
            ->save(ProductItemClient::factory()->make([
                'margin' => 5,
                'client_id' => $this->user->client->id
            ]));

        /** @var Order $order */
        $order = Order::factory()
            ->for($productItem)
            ->va()
            ->create([
                'cust_email' => 'mail@mail.test',
                'price' => $productItem->real_price,
                'discount_price' => 0,
            ]);

        $this->mitraGamersMockDataFail();

        $response = $this->withHeaders([
            'x-callback-token' => $this->user->client->xendit_callback_token
        ])->postJson(route('callback.xendit'), [
            'id' => $order->payment_id,
            'external_id' => $order->code,
            'status' => 'COMPLETED'
        ]);

        $response->assertOk();

        /** @var ProductItem $productItem */
        $productItem = $productItem->fresh();
        $this->assertSame(0, $productItem->stock, 'Stock shouldbe 0');

        /** @var Order $order */
        $order = $order->refresh();
        $this->assertSame($order->mg_invoice, null);
        $this->assertSame(Order::DONE, $order->order_status);
        $this->assertSame(Order::SETTLEMENT, $order->payment_status);

        Mail::assertSent(SendOrderNotif::class);
        Mail::assertQueued(SendSettlementNotif::class);
        Mail::assertQueued(OrderAccountSucceed::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->generateCustomerUser();
    }

    private function mitraGamersMockSuccess(): array
    {
        $jsonFilePath = base_path('tests/Data/mitra-gamers-success.json');
        $jsonContent = file_get_contents($jsonFilePath);
        $responseData = json_decode($jsonContent, true);

        $path = str(config('array.mitra-gamers.url'))->replaceEnd("/", "")->append('/api/v2/transaction')->value();
        Http::fake([
            $path => Http::response($responseData, 200),
        ]);

        return $responseData;
    }

    private function mitraGamersMockDataFail(): array
    {
        $path = str(config('array.mitra-gamers.url'))->replaceEnd("/", "")->append('/api/v2/transaction')->value();
        Http::fake([
            $path => Http::response([
                "message" => "error",
            ], 400),
        ]);
        return [];
    }
}
