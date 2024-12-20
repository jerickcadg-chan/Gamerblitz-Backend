<?php

namespace Tests\Unit\Models;

use App\Models\ProductItemClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductItemClientTest extends TestCase
{
    use RefreshDatabase;

    public function testFactory()
    {
        $productItemClient = ProductItemClient::factory()->create();
        $this->assertNotEmpty($productItemClient->product_item_id);
        $this->assertNotEmpty($productItemClient->client_id);
        $this->assertNotEmpty($productItemClient->margin);
    }

    public function testFillable()
    {
        $productItemClient = new ProductItemClient();
        $this->assertEquals([
            'product_item_id',
            'client_id',
            'margin',
            'is_active',
        ], $productItemClient->getFillable());
    }

    public function testDates()
    {
        $productItemClient = new ProductItemClient();
        $this->assertEquals(['created_at', 'updated_at'], $productItemClient->getDates());
    }

    public function testToArray()
    {
        $productItemClient = ProductItemClient::factory()->create();

        $array = $productItemClient->toArray();

        $this->assertSame(array_keys($array), [
            'product_item_id',
            'client_id',
            'margin',
            'is_active',
            'updated_at',
            'created_at',
        ]);
    }
}
