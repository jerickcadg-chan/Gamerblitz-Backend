<?php

namespace Tests\Unit\Models;

use App\Models\FlashSale;
use App\Models\FlashSaleProductItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashSaleProductItemTest extends TestCase
{
    use RefreshDatabase;

    private function generateFLashSale(): FlashSale
    {
        return FlashSale::factory()->create();
    }

    public function testFactory()
    {
        $flashSaleProductItem = FlashSaleProductItem::factory()->create([
            'flash_sale_id' => $this->generateFLashSale()->id,
        ]);
        $this->assertNotEmpty($flashSaleProductItem->flash_sale_id);
        $this->assertNotEmpty($flashSaleProductItem->product_item_id);
        $this->assertNotEmpty($flashSaleProductItem->price);
        $this->assertNotEmpty($flashSaleProductItem->stock);
    }

    public function testFillable()
    {
        $flashSaleProductItem = new FlashSaleProductItem();
        $this->assertEquals([
            'flash_sale_id',
            'product_item_id',
            'price',
            'stock',
        ], $flashSaleProductItem->getFillable());
    }

    public function testFlashSaleRelationship()
    {
        $flashSaleProductItem = FlashSaleProductItem::factory()->create([
            'flash_sale_id' => $this->generateFLashSale()->id,
        ]);
        $this->assertNotNull($flashSaleProductItem->flashSale);
    }

    public function testProductItemRelationship()
    {
        $flashSaleProductItem = FlashSaleProductItem::factory()->create([
            'flash_sale_id' => $this->generateFLashSale()->id,
        ]);
        $this->assertNotNull($flashSaleProductItem->productItem);
    }

    public function testDates()
    {
        $flashSaleProductItem = new FlashSaleProductItem();
        $this->assertEquals(['created_at', 'updated_at'], $flashSaleProductItem->getDates());
    }

    public function testToArray()
    {
        $flashSaleProductItem = FlashSaleProductItem::factory()->create([
            'flash_sale_id' => $this->generateFLashSale()->id,
        ]);
        $array = $flashSaleProductItem->toArray();

        $this->assertSame(array_keys($array), [
            'price',
            'stock',
            'product_item_id',
            'flash_sale_id',
            'updated_at',
            'created_at',
            'id',
        ]);
    }
}
