<?php

namespace Tests\Unit\Models;

use App\Models\Discount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscountTest extends TestCase
{
    use RefreshDatabase;

    public function testFactory()
    {
        $discount = Discount::factory()->create();
        foreach (array_keys($discount->getAttributes()) as $fillable) {
            $this->assertNotNull($discount->{$fillable});
        }
    }

    public function testFillable()
    {
        $discount = new Discount();
        $this->assertEquals([
            'name',
            'code',
            'description',
            'nominal',
            'disc_type',
            'product_type',
            'start_date',
            'end_date',
            'is_active',
            'maximum',
            'used'
        ], $discount->getFillable());
    }

    public function testDates()
    {
        $discount = new Discount();
        $this->assertEquals(['created_at', 'updated_at'], $discount->getDates());
    }

    public function testToArray()
    {
        $discount = Discount::factory()->create();
        $array = $discount->toArray();

        $this->assertSame(array_keys($array), [
            "client_id",
            "name",
            "code",
            "description",
            "nominal",
            "disc_type",
            "product_type",
            "start_date",
            "end_date",
            "is_active",
            "maximum",
            "used",
            "updated_at",
            "created_at",
            "id",
        ]);
    }

    public function testClient()
    {
        $discount = Discount::factory()->create();

        $this->assertNotNull($discount->client);
    }
}
