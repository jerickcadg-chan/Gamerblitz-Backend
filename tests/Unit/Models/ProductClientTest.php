<?php

namespace Tests\Unit\Models;

use App\Models\Picture;
use App\Models\ProductClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductClientTest extends TestCase
{
    use RefreshDatabase;

    public function testFactory()
    {
        $productClient = ProductClient::factory()->create();
        $this->assertNotEmpty($productClient->product_id);
        $this->assertNotEmpty($productClient->client_id);
    }

    public function testFillable()
    {
        $productClient = new ProductClient();
        $this->assertEquals([
            'product_id',
            'client_id',
            'is_active',
        ], $productClient->getFillable());
    }

    public function testDates()
    {
        $productClient = new ProductClient();
        $this->assertEquals(['created_at', 'updated_at'], $productClient->getDates());
    }

    public function testToArray()
    {
        $productClient = ProductClient::factory()->create();
        $array = $productClient->toArray();
        $this->assertSame(array_keys($array), [
            'product_id',
            'client_id',
            'is_active',
            'updated_at',
            'created_at',
            'id',
        ]);
    }

    public function testProductCanHavePicture()
    {
        $productClient = ProductClient::factory()->create();
        $productClient->picture()->create([
            'path' => 'path/to/picture.jpg',
            'file_name' => 'picture.jpg',
            'caption' => 'Picture caption',
        ]);

        $this->assertNotEmpty($productClient->picture);
        $this->assertEquals('path/to/picture.jpg', $productClient->picture->path);
        $this->assertEquals('picture.jpg', $productClient->picture->file_name);
        $this->assertEquals('Picture caption', $productClient->picture->caption);
    }

}
