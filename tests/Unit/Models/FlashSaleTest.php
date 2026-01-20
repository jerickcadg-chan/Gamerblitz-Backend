<?php

namespace Tests\Unit\Models;

use App\Models\FlashSale;
use App\Models\FlashSaleProductItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashSaleTest extends TestCase
{
    use RefreshDatabase;

    public function testFactory()
    {
        $flashSale = FlashSale::factory()->create();

        foreach (array_keys($flashSale->getAttributes()) as $fillable) {
            $this->assertNotNull($flashSale->{$fillable});
        }
    }

    public function testFillable()
    {
        $flashSale = new FlashSale();
        $this->assertEquals([
            'client_id',
            'name',
            'start_date',
            'end_date',
        ], $flashSale->getFillable());
    }

    public function testDates()
    {
        $flashSale = new FlashSale();
        $this->assertEquals(['created_at', 'updated_at'], $flashSale->getDates());
    }

    public function testIsActive()
    {
        $flashSale = FlashSale::factory()->create([
            'start_date' => now()->subHour(),
            'end_date' => now()->addDay()->subHour(),
        ]);
        $this->assertTrue($flashSale->isActive);
    }

    public function testStatusViewActive()
    {
        $flashSale = FlashSale::factory()->create([
            'start_date' => now()->subHour(),
            'end_date' => now()->addDay()->subHour(),
        ]);
        $this->assertEquals('<label class="badge badge-success">Aktif</label>', $flashSale->statusView);
    }

    public function testStatusViewInactive()
    {
        $flashSale = FlashSale::factory()->create([
            'start_date' => now()->subDays(2),
            'end_date' => now()->subDays(1),
        ]);
        $this->assertEquals('<label class="badge badge-danger">Tidak aktif</label>', $flashSale->statusView);
    }

    public function testScopeActive()
    {
        FlashSale::factory()->create([
            'start_date' => now()->subHour(),
            'end_date' => now()->addDay()->subHour(),
        ]);
        $this->assertEquals(1, FlashSale::active()->count());
    }

    public function testRelationshipClient()
    {
        $flashSale = FlashSale::factory()->create();
        $this->assertNotNull($flashSale->client);
    }

    public function testToArray()
    {
        $flashSale = FlashSale::factory()->create();
        $array = $flashSale->toArray();
        $this->assertSame(array_keys($array), [
            'client_id',
            'name',
            'start_date',
            'end_date',
            'updated_at',
            'created_at',
            'id',
        ]);
    }

    public function testRelationshipItems()
    {
        $admin = $this->generateSuperAdminUser();
        $flashSale = FlashSale::factory()->create([
            'client_id' => $admin->client->id,
        ]);
        FlashSaleProductItem::factory()
            ->create([
                'flash_sale_id' => $flashSale->id,
            ]);

        $this->assertNotEmpty($flashSale->items);
    }
}
