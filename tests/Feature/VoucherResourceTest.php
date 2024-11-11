<?php

namespace Tests\Feature;

use App\Models\Voucher;
use Tests\TestCase;

class VoucherResourceTest extends TestCase
{
    public function testCanShowDataTablePage()
    {
        $response = $this->actingAs($this->user)->get(route('voucher.index'));
        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::vouchers.index');
    }

    public function testCanShowCreateVoucherForm()
    {
        $response = $this->actingAs($this->user)->get(route('voucher.create'));
        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::vouchers.create');
    }

    public function testCanStoreNewVoucher()
    {
        $voucher = factory(Voucher::class)->make();
        $response = $this->actingAs($this->user)->post(route('voucher.store'), $voucher->toArray());

        $response->assertSessionHasNoErrors();

        $response->assertStatus(302);

        $this->assertDatabaseHas('vouchers', $voucher->toArray());
        $response->assertRedirect(route('voucher.index'));
    }

    public function testCanShowVoucherDetailPage()
    {
        $voucher = factory(Voucher::class)->create();
        $response = $this->actingAs($this->user)->get(route('voucher.show', $voucher));
        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::vouchers.show');
    }

    public function testCanShowUpdateVoucherForm()
    {
        $voucher = factory(Voucher::class)->create();
        $response = $this->actingAs($this->user)->get(route('voucher.edit', $voucher));

        $response->assertSessionHasNoErrors();

        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::vouchers.edit');
    }

    public function testCanUpdateVoucher()
    {
        $voucher = factory(Voucher::class)->create();
        $newData = factory(Voucher::class)->make();
        $response = $this->actingAs($this->user)->put(route('voucher.update', $voucher), $newData->toArray());

        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);
        $this->assertDatabaseHas('vouchers', $newData->toArray());
        $response->assertRedirect(route('voucher.index'));
    }

    public function testCanDeleteVoucher()
    {
        $voucher = factory(Voucher::class)->create();
        $response = $this->actingAs($this->user)->delete(route('voucher.destroy', $voucher));
        $response->assertStatus(302);
        $this->assertDatabaseMissing('vouchers', $voucher->toArray());

        $response->assertRedirect(route('voucher.index'));
    }
}
