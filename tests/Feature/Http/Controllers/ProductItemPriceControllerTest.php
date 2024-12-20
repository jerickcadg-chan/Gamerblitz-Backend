<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ProductItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductItemPriceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_superadmin_can_access()
    {
        $user = $this->generateCustomerUser();

        $response = $this
            ->actingAs($user)
            ->get(route('product_item_price.index'));

        $response->assertSessionHas('alert');
        $this->assertToast($response, [
            'title' => trans('auth.no_permission'),
            'icon' => 'error',
        ]);

        $response->assertStatus(302);
    }

    public function test_index()
    {
        $user = $this->generateSuperAdminUser();
        $response = $this
            ->actingAs($user)
            ->get(route('product_item_price.index'));

        $response
            ->assertViewIs('product_item_price.index')
            ->assertStatus(200);
    }

    public function test_store_failed_cause_the_product_item_ids_is_required()
    {
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('product_item_price.store'), [
                'update_all' => false,
                'margin' => 10,
            ]);

        $response
            ->assertJsonValidationErrorFor('product_item_ids');
    }

    public function test_store_failed_cause_the_product_item_ids_must_be_an_array()
    {
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('product_item_price.store'), [
                'update_all' => false,
                'product_item_ids' => 'not-an-array',
                'margin' => 10,
            ]);
        $response
            ->assertJsonValidationErrorFor('product_item_ids');
    }

    public function test_store_failed_cause_the_margin_is_required()
    {
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('product_item_price.store'), [
                'update_all' => false,
                'product_item_ids' => [1],
            ]);
        $response
            ->assertJsonValidationErrorFor('margin');
    }

    public function test_store_success_with_update_all(): void
    {
        $MARGIN = 12;
        $PRODUCT_ITEM_COUNT = 10;

        $product_items = ProductItem::factory($PRODUCT_ITEM_COUNT)->create();

        $user = $this->generateSuperAdminUser();
        $client = $user->client;

        $response = $this
            ->actingAs($user)
            ->post(route('product_item_price.store'), [
                'update_all' => true,
                'margin' => $MARGIN,
            ]);

        $response->assertJson([
            'message' => 'Harga produk berhasil diupdate',
            'success' => true,
        ]);;

        $this->assertTrue($client->productItemClients->isNotEmpty());

        foreach ($client->productItemClients as $productItemClient) {
            $this->assertSame($client->id, $productItemClient->client_id);
            $this->assertTrue(in_array($productItemClient->product_item_id, $product_items->pluck('id')->toArray()));
            $this->assertEquals($MARGIN, $productItemClient->margin);
        }
    }


    public function test_store_success_with_product_item_ids(): void
    {
        $MARGIN = 12;
        $PRODUCT_ITEM_COUNT = 10;
        $product_items = ProductItem::factory($PRODUCT_ITEM_COUNT)->create();
        $user = $this->generateSuperAdminUser();
        $client = $user->client;
        $response = $this
            ->actingAs($user)
            ->post(route('product_item_price.store'), [
                'update_all' => false,
                'product_item_ids' => $product_items->pluck('id')->toArray(),
                'margin' => $MARGIN,
            ]);

        $response->assertJson([
            'message' => 'Harga produk berhasil diupdate',
            'success' => true,
        ]);

        $this->assertTrue($client->productItemClients->isNotEmpty());
        foreach ($client->productItemClients as $productItemClient) {
            $this->assertSame($client->id, $productItemClient->client_id);
            $this->assertTrue(in_array($productItemClient->product_item_id, $product_items->pluck('id')->toArray()));
            $this->assertEquals($MARGIN, $productItemClient->margin);
        }
    }

    public function test_store_only_updates_sent_product_item_ids(): void
    {
        $MARGIN = 12;
        $PRODUCT_ITEM_COUNT = 10;
        $product_items = ProductItem::factory($PRODUCT_ITEM_COUNT)->create();
        $user = $this->generateSuperAdminUser();
        $client = $user->client;

        $selected_product_items = $product_items->take(5);

        $response = $this
            ->actingAs($user)
            ->post(route('product_item_price.store'), [
                'update_all' => false,
                'product_item_ids' => $selected_product_items->pluck('id')->toArray(),
                'margin' => $MARGIN,
            ]);

        $response->assertJson([
            'message' => 'Harga produk berhasil diupdate',
            'success' => true,
        ]);

        $this->assertTrue($client->productItemClients->isNotEmpty());

        foreach ($client->productItemClients as $productItemClient) {
            $this->assertSame($client->id, $productItemClient->client_id);
            $this->assertTrue(in_array($productItemClient->product_item_id, $selected_product_items->pluck('id')->toArray()));
            $this->assertEquals($MARGIN, $productItemClient->margin);
        }

        $non_selected_product_items = $product_items->diff($selected_product_items);

        foreach ($non_selected_product_items as $productItem) {
            $this->assertFalse($client->productItemClients->contains('product_item_id', $productItem->id));
        }
    }

    public function test_can_update_margin_when_the_product_item_already_has_a_margin(): void
    {
        $INITIAL_MARGIN = 10;
        $UPDATED_MARGIN = 15;
        $PRODUCT_ITEM_COUNT = 5;

        $product_items = ProductItem::factory($PRODUCT_ITEM_COUNT)->create();
        $user = $this->generateSuperAdminUser();
        $client = $user->client;

        foreach ($product_items as $product_item) {
            $client->productItemClients()->create([
                'product_item_id' => $product_item->id,
                'margin' => $INITIAL_MARGIN,
            ]);
        }

        $response = $this
            ->actingAs($user)
            ->post(route('product_item_price.store'), [
                'update_all' => false,
                'product_item_ids' => $product_items->pluck('id')->toArray(),
                'margin' => $UPDATED_MARGIN,
            ]);

        $response->assertJson([
            'message' => 'Harga produk berhasil diupdate',
            'success' => true,
        ]);

        $this->assertTrue($client->productItemClients->isNotEmpty());

        foreach ($client->productItemClients as $productItemClient) {
            $this->assertSame($client->id, $productItemClient->client_id);
            $this->assertTrue(in_array($productItemClient->product_item_id, $product_items->pluck('id')->toArray()));
            $this->assertEquals($UPDATED_MARGIN, $productItemClient->margin);
        }
    }
}
