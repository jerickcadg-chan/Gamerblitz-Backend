<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Picture;
use App\Models\Product;
use App\Models\ProductClient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs($this->generateSuperAdminUser());
    }

    public function test_index_displays_products()
    {
        Product::factory()->create();

        $response = $this->get(route('product.index'));

        $response->assertStatus(200);
        $response->assertViewIs('products.index');
        $response->assertViewHas('products');
    }

    public function test_create_displays_create_form()
    {
        $response = $this->get(route('product.create'));

        $response->assertStatus(200);
        $response->assertViewIs('products.create');
    }

    public function test_store_saves_new_product()
    {
        $data = Product::factory()->make()->toArray();

        $response = $this->post(route('product.store'), $data);

        $response->assertRedirect(route('product.index'));

        $this->assertToast($response, [
            'title' => alert_created_text('Product'),
            'icon' => 'success',
        ]);

        $this->assertDatabaseHas('products', $data);
    }

    public function test_store_saves_new_product_with_image()
    {
        $data = Product::factory()->make()->toArray();

        Storage::fake('public');

        $data['picture'] = UploadedFile::fake()->image('picture.jpg');

        $response = $this->post(route('product.store'), $data);

        $response->assertRedirect(route('product.index'));

        $this->assertToast($response, [
            'title' => alert_created_text('Product'),
            'icon' => 'success',
        ]);

        $product = Product::whereName($data['name'])->first();

        $this->assertNotNull($product->product_cover);

        unset($data['picture']);

        $this->assertDatabaseHas('products', $data);
    }

    public function test_show_displays_product_details()
    {
        $product = Product::factory()->create();

        $response = $this->get(route('product.show', $product));

        $response->assertStatus(200);
        $response->assertViewIs('products.show');
        $response->assertViewHas('product', $product);
    }

    public function test_edit_displays_edit_form()
    {
        $product = Product::factory()->create();

        $response = $this->get(route('product.edit', $product));

        $response->assertStatus(200);
        $response->assertViewIs('products.edit');
        $response->assertViewHas('product', $product);
    }

    public function test_update_modifies_existing_product()
    {
        $product = Product::factory()->create();
        $data = Product::factory()->make()->toArray();

        $response = $this->put(route('product.update', $product), $data);

        $response->assertRedirect(route('product.index'));

        $this->assertToast($response, [
            'title' => alert_updated_text('Product'),
            'icon' => 'success',
        ]);

        $this->assertDatabaseHas('products', $data);
    }

    public function test_update_modifies_existing_product_with_image()
    {
        $product = Product::factory()->create();

        $data = Product::factory()->make()->toArray();

        Storage::fake('public');

        $data['picture'] = UploadedFile::fake()->image('picture.jpg');

        $response = $this->put(route('product.update', $product), $data);

        $response->assertRedirect(route('product.index'));

        $this->assertToast($response, [
            'title' => alert_updated_text('Product'),
            'icon' => 'success',
        ]);
        $product = Product::whereName($data['name'])->first();

        $this->assertNotNull($product->product_cover);

        unset($data['picture']);

        $this->assertDatabaseHas('products', $data);
    }

    public function test_update_modifies_existing_product_with_new_image()
    {
        $product = Product::factory()->create();
        $product->productClient()->create([
            'client_id' => $this->user->client_id,
        ]);
        $product->productClient->first()->picture()->create([
            'path' => 'products',
            'file_name' => 'picture.jpg',
        ]);
        $oldCover = $product->product_cover;

        $data = Product::factory()->make()->toArray();

        Storage::fake('public');

        $data['picture'] = UploadedFile::fake()->image('picture.jpg');

        $response = $this->put(route('product.update', $product), $data);

        $response->assertRedirect(route('product.index'));

        $this->assertToast($response, [
            'title' => alert_updated_text('Product'),
            'icon' => 'success',
        ]);

        $product = Product::whereName($data['name'])->first();

        $this->assertTrue($oldCover != $product->product_cover);
        $this->assertNotNull($product->product_cover);

        unset($data['picture']);

        $this->assertDatabaseHas('products', $data);
    }

    public function test_destroy_deletes_product()
    {
        $product = Product::factory()->create();

        $response = $this->delete(route('product.destroy', $product));

        $response->assertRedirect(route('product.index'));

        $this->assertToast($response, [
            'title' => alert_deleted_text('Product'),
            'icon' => 'success',
        ]);

        $this->assertNull(Product::whereId($product->id)->first());
    }

    public function test_destroy_deletes_product_with_image()
    {
        $product = Product::factory()->create();
        $product->productClient()->create([
            'client_id' => $this->user->client_id,
        ]);

        $product->productClient->first()->picture()->create([
            'path' => 'products',
            'file_name' => 'picture.jpg',
        ]);

        $oldProductClient = $product->productClient;

        $oldProductPicture = $product->productClient->first()->picture->file_name;

        $response = $this->delete(route('product.destroy', $product));

        $response->assertRedirect(route('product.index'));

        $this->assertToast($response, [
            'title' => alert_deleted_text('Product'),
            'icon' => 'success',
        ]);

        $this->assertNull(ProductClient::whereId($oldProductClient)->first());

        $this->assertNull(Picture::whereFileName($oldProductPicture)->first());

        $this->assertNull(Product::whereId($product->id)->first());
    }
}
