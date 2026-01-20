<?php

namespace Tests\Unit\Models;

use App\Models\Account;
use App\Models\ProductItem;
use App\Models\ProductItemClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductItemTest extends TestCase
{
    use RefreshDatabase;

    public function testFactory()
    {
        $productItem = ProductItem::factory()->create();
        foreach (array_keys($productItem->getAttributes()) as $fillable) {
            $this->assertNotNull($productItem->{$fillable});
        }
    }

    public function testFillable()
    {
        $productItem = new ProductItem();
        $this->assertEquals([
            'product_id',
            'name',
            'code',
            'stock',
            'price',
            'price_reseller',
            'capital',
            'type'
        ], $productItem->getFillable());
    }

    public function testDates()
    {
        $productItem = new ProductItem();
        $this->assertEquals(['created_at', 'updated_at'], $productItem->getDates());
    }

    public function testToArray()
    {
        $productItem = ProductItem::factory()->create();

        $array = $productItem->toArray();

        $this->assertSame(array_keys($array), [
            "product_id",
            "name",
            "code",
            "stock",
            "price",
            "price_reseller",
            "capital",
            "type",
            "updated_at",
            "created_at",
            "id",
            "real_price",
            "total_price",
            "product_item_clients",
        ]);
    }

    public function test_product_item_can_have_account()
    {
        $user = $this->generateSuperAdminUser();
        $this->actingAs($user);
        $productItem = ProductItem::factory()->create();
        $pic = ProductItemClient::factory()
            ->create([
                'product_item_id' => $productItem->id,
                'client_id' => $user->client_id,
            ]);
        Account::factory()
            ->create([
                'product_item_id' => $productItem->id,
                'client_id' => $pic->client_id,
            ]);
        $this->assertNotEmpty($productItem->accounts);
    }

    public function test_product_item_can_have_product_clients()
    {
        $productItem = ProductItem::factory()->create();

        ProductItemClient::factory()
            ->create([
                'product_item_id' => $productItem->id
            ]);
        $this->assertNotEmpty($productItem->productItemClients);
    }
}
