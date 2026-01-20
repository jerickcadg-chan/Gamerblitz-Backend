<?php

namespace Tests\Feature;

use App\Models\ProductCategory;
use Tests\TestCase;

class ProductCategoryResourceTest extends TestCase
{
    public function testCanShowDataTablePage()
    {
        $response = $this->actingAs($this->user)->get(route('product_category.index'));
        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::product_categories.index');
    }

    public function testCanShowCreateProductCategoryForm()
    {
        $response = $this->actingAs($this->user)->get(route('product_category.create'));
        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::product_categories.create');
    }

    public function testCanStoreNewProductCategory()
    {
        $product_category = factory(ProductCategory::class)->make();
        $response = $this->actingAs($this->user)->post(route('product_category.store'), $product_category->toArray());

        $response->assertSessionHasNoErrors();

        $response->assertStatus(302);

        $this->assertDatabaseHas('product_categories', $product_category->toArray());
        $response->assertRedirect(route('product_category.index'));
    }

    public function testCanShowProductCategoryDetailPage()
    {
        $product_category = factory(ProductCategory::class)->create();
        $response = $this->actingAs($this->user)->get(route('product_category.show', $product_category));
        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::product_categories.show');
    }

    public function testCanShowUpdateProductCategoryForm()
    {
        $product_category = factory(ProductCategory::class)->create();
        $response = $this->actingAs($this->user)->get(route('product_category.edit', $product_category));

        $response->assertSessionHasNoErrors();

        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::product_categories.edit');
    }

    public function testCanUpdateProductCategory()
    {
        $product_category = factory(ProductCategory::class)->create();
        $newData = factory(ProductCategory::class)->make();
        $response = $this->actingAs($this->user)->put(route('product_category.update', $product_category), $newData->toArray());

        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);
        $this->assertDatabaseHas('product_categories', $newData->toArray());
        $response->assertRedirect(route('product_category.index'));
    }

    public function testCanDeleteProductCategory()
    {
        $product_category = factory(ProductCategory::class)->create();
        $response = $this->actingAs($this->user)->delete(route('product_category.destroy', $product_category));
        $response->assertStatus(302);
        $this->assertDatabaseMissing('product_categories', $product_category->toArray());

        $response->assertRedirect(route('product_category.index'));
    }
}
