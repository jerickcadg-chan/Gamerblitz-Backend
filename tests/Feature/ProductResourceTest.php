<?php

namespace Tests\Feature;

use App\Models\Product;
use Tests\TestCase;

class ProductResourceTest extends TestCase
{
    public function testCanShowDataTablePage()
    {
        $response = $this->actingAs($this->user)->get(route('product.index'));
        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::products.index');
    }

    public function testCanShowCreateProductForm()
    {
        $response = $this->actingAs($this->user)->get(route('product.create'));
        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::products.create');
    }

    public function testCanStoreNewProduct()
    {
        $product = factory(Product::class)->make();
        $response = $this->actingAs($this->user)->post(route('product.store'), $product->toArray());

        $response->assertSessionHasNoErrors();

        $response->assertStatus(302);

        $this->assertDatabaseHas('products', $product->toArray());
        $response->assertRedirect(route('product.index'));
    }

    public function testCanShowProductDetailPage()
    {
        $product = factory(Product::class)->create();
        $response = $this->actingAs($this->user)->get(route('product.show', $product));
        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::products.show');
    }

    public function testCanShowUpdateProductForm()
    {
        $product = factory(Product::class)->create();
        $response = $this->actingAs($this->user)->get(route('product.edit', $product));

        $response->assertSessionHasNoErrors();

        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::products.edit');
    }

    public function testCanUpdateProduct()
    {
        $product = factory(Product::class)->create();
        $newData = factory(Product::class)->make();
        $response = $this->actingAs($this->user)->put(route('product.update', $product), $newData->toArray());

        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);
        $this->assertDatabaseHas('products', $newData->toArray());
        $response->assertRedirect(route('product.index'));
    }

    public function testCanDeleteProduct()
    {
        $product = factory(Product::class)->create();
        $response = $this->actingAs($this->user)->delete(route('product.destroy', $product));
        $response->assertStatus(302);
        $this->assertDatabaseMissing('products', $product->toArray());

        $response->assertRedirect(route('product.index'));
    }
}
