<?php

namespace Tests\Feature;

use App\Models\ProductItem;
use Tests\TestCase;

class ProductItemResourceTest extends TestCase
{
    public function testCanShowDataTablePage()
    {
        $response = $this->actingAs($this->user)->get(route('product_item.index'));
        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::product_items.index');
    }

    public function testCanShowCreateProductItemForm()
    {
        $response = $this->actingAs($this->user)->get(route('product_item.create'));
        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::product_items.create');
    }

    public function testCanStoreNewProductItem()
    {
        $product_item = factory(ProductItem::class)->make();
        $response = $this->actingAs($this->user)->post(route('product_item.store'), $product_item->toArray());

        $response->assertSessionHasNoErrors();

        $response->assertStatus(302);

        $this->assertDatabaseHas('product_items', $product_item->toArray());
        $response->assertRedirect(route('product_item.index'));
    }

    public function testCanShowProductItemDetailPage()
    {
        $product_item = factory(ProductItem::class)->create();
        $response = $this->actingAs($this->user)->get(route('product_item.show', $product_item));
        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::product_items.show');
    }

    public function testCanShowUpdateProductItemForm()
    {
        $product_item = factory(ProductItem::class)->create();
        $response = $this->actingAs($this->user)->get(route('product_item.edit', $product_item));

        $response->assertSessionHasNoErrors();

        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::product_items.edit');
    }

    public function testCanUpdateProductItem()
    {
        $product_item = factory(ProductItem::class)->create();
        $newData = factory(ProductItem::class)->make();
        $response = $this->actingAs($this->user)->put(route('product_item.update', $product_item), $newData->toArray());

        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);
        $this->assertDatabaseHas('product_items', $newData->toArray());
        $response->assertRedirect(route('product_item.index'));
    }

    public function testCanDeleteProductItem()
    {
        $product_item = factory(ProductItem::class)->create();
        $response = $this->actingAs($this->user)->delete(route('product_item.destroy', $product_item));
        $response->assertStatus(302);
        $this->assertDatabaseMissing('product_items', $product_item->toArray());

        $response->assertRedirect(route('product_item.index'));
    }
}
