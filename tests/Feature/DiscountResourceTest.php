<?php

namespace Tests\Feature;

use App\Models\Discount;
use Tests\TestCase;

class DiscountResourceTest extends TestCase
{
    public function testCanShowDataTablePage()
    {
        $response = $this->actingAs($this->user)->get(route('discount.index'));
        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::discounts.index');
    }

    public function testCanShowCreateDiscountForm()
    {
        $response = $this->actingAs($this->user)->get(route('discount.create'));
        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::discounts.create');
    }

    public function testCanStoreNewDiscount()
    {
        $discount = factory(Discount::class)->make();
        $response = $this->actingAs($this->user)->post(route('discount.store'), $discount->toArray());

        $response->assertSessionHasNoErrors();

        $response->assertStatus(302);

        $this->assertDatabaseHas('discounts', $discount->toArray());
        $response->assertRedirect(route('discount.index'));
    }

    public function testCanShowDiscountDetailPage()
    {
        $discount = factory(Discount::class)->create();
        $response = $this->actingAs($this->user)->get(route('discount.show', $discount));
        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::discounts.show');
    }

    public function testCanShowUpdateDiscountForm()
    {
        $discount = factory(Discount::class)->create();
        $response = $this->actingAs($this->user)->get(route('discount.edit', $discount));

        $response->assertSessionHasNoErrors();

        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::discounts.edit');
    }

    public function testCanUpdateDiscount()
    {
        $discount = factory(Discount::class)->create();
        $newData = factory(Discount::class)->make();
        $response = $this->actingAs($this->user)->put(route('discount.update', $discount), $newData->toArray());

        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);
        $this->assertDatabaseHas('discounts', $newData->toArray());
        $response->assertRedirect(route('discount.index'));
    }

    public function testCanDeleteDiscount()
    {
        $discount = factory(Discount::class)->create();
        $response = $this->actingAs($this->user)->delete(route('discount.destroy', $discount));
        $response->assertStatus(302);
        $this->assertDatabaseMissing('discounts', $discount->toArray());

        $response->assertRedirect(route('discount.index'));
    }
}
