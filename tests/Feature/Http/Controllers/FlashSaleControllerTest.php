<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\FlashSale;
use App\Models\ProductItem;
use App\Models\ProductItemClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashSaleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_superadmin_can_access()
    {
        $user = $this->generateCustomerUser();

        $response = $this
            ->actingAs($user)
            ->get(route('flash_sale.index'));

        $response->assertSessionHas('alert');
        $this->assertToast($response, [
            'title' => trans('auth.no_permission'),
            'icon' => 'error',
        ]);

        $response->assertStatus(302);
    }

    public function test_index()
    {
        $user = $this->generateSuperAdminUser();
        $response = $this
            ->actingAs($user)
            ->get(route('flash_sale.index'));

        $response
            ->assertViewIs('flash_sales.index')
            ->assertStatus(200);
    }

    public function test_failed_because_start_date_required()
    {
        $user = $this->generateSuperAdminUser();
        $response = $this
            ->actingAs($user)
            ->post(route('flash_sale.store'), [
                'end_date' => now()->addDays(1),
            ]);

        $response->assertSessionHasErrors('start_date');
    }

    public function test_failed_because_end_date_required()
    {
        $user = $this->generateSuperAdminUser();
        $response = $this
            ->actingAs($user)
            ->post(route('flash_sale.store'), [
                'start_date' => now(),
            ]);

        $response->assertSessionHasErrors('end_date');
    }

    public function test_failed_because_end_date_after_start_date()
    {
        $user = $this->generateSuperAdminUser();
        $response = $this
            ->actingAs($user)
            ->post(route('flash_sale.store'), [
                'start_date' => now()->addDays(1),
                'end_date' => now(),
            ]);

        $response->assertSessionHasErrors('end_date');
    }

    public function test_failed_because_start_date_after_end_date(): void
    {
        $user = $this->generateSuperAdminUser();
        $response = $this
            ->actingAs($user)
            ->post(route('flash_sale.store'), [
                'start_date' => now()->addDays(1),
                'end_date' => now(),
            ]);

        $response->assertSessionHasErrors('end_date');
    }

    public function test_failed_because_product_item_ids_required()
    {
        $user = $this->generateSuperAdminUser();
        $response = $this
            ->actingAs($user)
            ->post(route('flash_sale.store'), [
                'start_date' => now(),
                'end_date' => now()->addDays(1),
            ]);

        $response->assertSessionHasErrors('product_item_ids');
    }

    public function test_failed_because_product_item_ids_is_not_array(): void
    {
        $user = $this->generateSuperAdminUser();
        $response = $this
            ->actingAs($user)
            ->post(route('flash_sale.store'), [
                'start_date' => now(),
                'end_date' => now()->addDays(1),
                'product_item_ids' => 'not_array',
            ]);

        $response->assertSessionHasErrors('product_item_ids');
    }

    public function test_failed_because_product_item_ids_product_id_required(): void
    {
        $user = $this->generateSuperAdminUser();
        $response = $this
            ->actingAs($user)
            ->post(route('flash_sale.store'), [
                'start_date' => now(),
                'end_date' => now()->addDays(1),
                'product_item_ids' => [
                    [
                        'price' => 1000,
                        'stock' => 10,
                    ],
                ],
            ]);
        $response->assertSessionHasErrors('product_item_ids.0.product_item_id');
    }

    public function test_create()
    {
        $user = $this->generateSuperAdminUser();
        $response = $this
            ->actingAs($user)
            ->get(route('flash_sale.create'));

        $response
            ->assertViewIs('flash_sales.create')
            ->assertStatus(200);
    }

    public function test_only_superadmin_can_create_flash_sale()
    {
        $user = $this->generateCustomerUser();

        $response = $this
            ->actingAs($user)
            ->post(route('flash_sale.store'));

        $response->assertSessionHas('alert');
        $this->assertToast($response, [
            'title' => trans('auth.no_permission'),
            'icon' => 'error',
        ]);

        $response->assertStatus(302);
    }

    public function test_failed_because_the_flash_sale_date_is_already_exist(): void
    {
        $admin = $this->generateSuperAdminUser();
        $productItems = ProductItem::factory(10)
            ->create()
            ->each(function ($productItem) use ($admin) {
                $productItem->productItemClients()->save(ProductItemClient::factory()->make([
                    'client_id' => $admin->client->id,
                ]));
            });
        $flashSale = FlashSale::factory()->create([
            'client_id' => $admin->client->id,
        ]);
        $data = [
            'name' => 'Flash Sale 1',
            'start_date' => $flashSale->start_date,
            'end_date' => $flashSale->end_date,
        ];
        $response = $this
            ->actingAs($admin)
            ->post(route('flash_sale.store'), array_merge($data, [
                'product_item_ids' => $productItems->map(fn ($productItem) => [
                    'product_item_id' => $productItem->product_id,
                    'price' => 1000,
                    'stock' => 10,
                ])->toArray(),
            ]));

        $response->assertSessionHasErrors('start_date');
    }

    public function test_store_flash_sale_success()
    {
        $admin = $this->generateSuperAdminUser();

        FlashSale::factory()->create([
            'client_id' => $admin->client->id,
            'start_date' => now()->subDay(2)->subMinute(),
            'end_date' => now()->subMinute(),
        ]);

        $productItems = ProductItem::factory(10)
            ->create()
            ->each(function ($productItem) use ($admin) {
                $productItem->productItemClients()->save(ProductItemClient::factory()->make([
                    'client_id' => $admin->client->id,
                ]));
            });

        $data = [
            'name' => 'Flash sale sukess',
            'start_date' => now()->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(1)->format('Y-m-d H:i:s'),
        ];

        $response = $this
            ->actingAs($admin)
            ->post(route('flash_sale.store'), array_merge($data, [
                'product_item_ids' => $productItems->map(fn ($productItem) => [
                    'product_item_id' => $productItem->product_id,
                    'price' => 1000,
                    'stock' => 10,
                ])->toArray(),
            ]));

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        $this->assertToast($response, [
            'title' => alert_created_text('Flash Sale'),
            'icon' => 'success',
        ]);

        $this->assertDatabaseHas('flash_sales', $data);

        $flashSale = FlashSale::where('name', $data['name'])->first();

        foreach ($flashSale->items as $productItem) {
            $this->assertSame([
                'flash_sale_id' => $productItem->flash_sale_id,
                'product_item_id' => $productItem->product_item_id,
                'price' => "1000.00",
                'stock' => 10.0,
            ], $productItem->only(['flash_sale_id', 'product_item_id', 'price', 'stock']));
        }
    }

    public function test_store_flash_sale_failed_because_product_item_id_not_exists()
    {
        $admin = $this->generateSuperAdminUser();
        $productItems = ProductItem::factory(10)
            ->create()
            ->each(function ($productItem) use ($admin) {
                $productItem->productItemClients()->save(ProductItemClient::factory()->make([
                    'client_id' => $admin->client->id,
                ]));
            });
        $data = [
            'name' => 'Flash sale sukess',
            'start_date' => now()->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(1)->format('Y-m-d H:i:s'),
        ];
        $response = $this
            ->actingAs($admin)
            ->post(route('flash_sale.store'), array_merge($data, [
                'product_item_ids' => $productItems->map(fn ($productItem) => [
                    'product_item_id' => 1000,
                    'price' => 1000,
                    'stock' => 10,
                ])->toArray(),
            ]));
        $response->assertSessionHasErrors('product_item_ids.0.product_item_id');
    }

    public function test_store_flash_sale_failed_because_price_required()
    {
        $admin = $this->generateSuperAdminUser();
        $productItems = ProductItem::factory(10)
            ->create()
            ->each(function ($productItem) use ($admin) {
                $productItem->productItemClients()->save(ProductItemClient::factory()->make([
                    'client_id' => $admin->client->id,
                ]));
            });
        $data = [
            'name' => 'Flash sale sukess',
            'start_date' => now()->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(1)->format('Y-m-d H:i:s'),
        ];
        $response = $this
            ->actingAs($admin)
            ->post(route('flash_sale.store'), array_merge($data, [
                'product_item_ids' => $productItems->map(fn ($productItem) => [
                    'product_item_id' => $productItem->product_id,
                    'stock' => 10,
                ])->toArray(),
            ]));

        $response->assertSessionHasErrors('product_item_ids.0.price');
    }

    public function test_store_flash_sale_failed_because_stock_required()
    {
        $admin = $this->generateSuperAdminUser();
        $productItems = ProductItem::factory(10)
            ->create()
            ->each(function ($productItem) use ($admin) {
                $productItem->productItemClients()->save(ProductItemClient::factory()->make([
                    'client_id' => $admin->client->id,
                ]));
            });
        $data = [
            'name' => 'Flash sale sukess',
            'start_date' => now()->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(1)->format('Y-m-d H:i:s'),
        ];
        $response = $this
            ->actingAs($admin)
            ->post(route('flash_sale.store'), array_merge($data, [
                'product_item_ids' => $productItems->map(fn ($productItem) => [
                    'product_item_id' => $productItem->product_id,
                    'price' => 1000,
                ])->toArray(),
            ]));
        $response->assertSessionHasErrors('product_item_ids.0.stock');
    }

    public function test_update_success()
    {
        $admin = $this->generateSuperAdminUser();
        $flashSale = FlashSale::factory()->create([
            'client_id' => $admin->client->id,
        ]);
        $productItems = ProductItem::factory(10)
            ->create()
            ->each(function ($productItem) use ($admin) {
                $productItem->productItemClients()->save(ProductItemClient::factory()->make([
                    'client_id' => $admin->client->id,
                ]));
            });
        $data = [
            'name' => 'Flash sale sukess',
            'start_date' => now()->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(1)->format('Y-m-d H:i:s'),
        ];
        $response = $this
            ->actingAs($admin)
            ->put(route('flash_sale.update', $flashSale), array_merge($data, [
                'product_item_ids' => $productItems->map(fn ($productItem) => [
                    'product_item_id' => $productItem->product_id,
                    'price' => 100,
                    'stock' => 10,
                ])->toArray(),
            ]));
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $this->assertToast($response, [
            'title' => alert_updated_text('Flash Sale'),
            'icon' => 'success',
        ]);
        $this->assertDatabaseHas('flash_sales', $data);
        $flashSale = FlashSale::where('name', $data['name'])->first();
        foreach ($flashSale->items as $productItem) {
            $this->assertSame([
                'flash_sale_id' => $productItem->flash_sale_id,
                'product_item_id' => $productItem->product_item_id,
                'price' => "100.00",
                'stock' => 10.0,
            ], $productItem->only(['flash_sale_id', 'product_item_id', 'price', 'stock']));
        }
    }
}
