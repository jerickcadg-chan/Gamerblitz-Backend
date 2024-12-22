<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\FlashSale;
use App\Models\FlashSaleProductItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FlashSaleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_return_flash_sales()
    {
        $this->generateSuperAdminUser();
        $fs = FlashSale::factory()->create([
            'client_id' => $this->user->client_id,
            'start_date' => now()->subHour(),
            'end_date' => now()->addDay()->subHour(),
        ]);
        FlashSaleProductItem::factory(5)->create([
            'flash_sale_id' => $fs->id,
        ]);
        $data = $fs->fresh()->with('items.productItem')->first();

        $response = $this->getJson('/api/flash-sale');

        $response->assertStatus(200)
            ->assertJson($this->response_status_ok([
                'id' => $data->id,
                'name' => $data->name,
                'start_date' => $data->start_date->format('Y-m-d H:i:s'),
                'end_date' => $data->end_date->format('Y-m-d H:i:s'),
                'items' => $data->items->map(function (FlashSaleProductItem $item) {
                    return [
                        'id' => $item->id,
                        'flash_price' => $item->price,
                        'stock' => $item->stock,
                        'real_price' => $item->productItem->real_price,
                        'product_item' => [
                            'id' => $item->productItem->id,
                            'name' => $item->productItem->name,
                            'code' => $item->productItem->code,
                            'cover' => $item->productItem->product->product_cover,
                        ],
                    ];
                })->toArray(),
            ]));
    }

    public function test_it_should_return_empty_flash_sales()
    {
        if (FlashSale::active()->exists()) {
            FlashSale::active()->update([
                'start_date' => now()->subDays(2),
                'end_date' => now()->subDays(1),
            ]);
        }
        $this->generateSuperAdminUser();
        $fs = FlashSale::factory()->create([
            'client_id' => $this->user->client_id,
            'start_date' => now()->subDays(2),
            'end_date' => now()->subDays(1),
        ]);
        FlashSaleProductItem::factory(5)->create([
            'flash_sale_id' => $fs->id,
        ]);

        $response = $this->getJson('/api/flash-sale');

        $response->assertStatus(200)
            ->assertJsonPath('payload', null);
    }
}
