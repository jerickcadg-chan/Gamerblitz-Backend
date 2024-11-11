<?php

namespace Tests\Feature;

use App\Models\Slider;
use Tests\TestCase;

class SliderResourceTest extends TestCase
{
    public function testCanShowDataTablePage()
    {
        $response = $this->actingAs($this->user)->get(route('slider.index'));
        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::sliders.index');
    }

    public function testCanShowCreateSliderForm()
    {
        $response = $this->actingAs($this->user)->get(route('slider.create'));
        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::sliders.create');
    }

    public function testCanStoreNewSlider()
    {
        $slider = factory(Slider::class)->make();
        $response = $this->actingAs($this->user)->post(route('slider.store'), $slider->toArray());

        $response->assertSessionHasNoErrors();

        $response->assertStatus(302);

        $this->assertDatabaseHas('sliders', $slider->toArray());
        $response->assertRedirect(route('slider.index'));
    }

    public function testCanShowSliderDetailPage()
    {
        $slider = factory(Slider::class)->create();
        $response = $this->actingAs($this->user)->get(route('slider.show', $slider));
        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::sliders.show');
    }

    public function testCanShowUpdateSliderForm()
    {
        $slider = factory(Slider::class)->create();
        $response = $this->actingAs($this->user)->get(route('slider.edit', $slider));

        $response->assertSessionHasNoErrors();

        $response->assertStatus(200);
        $response->assertViewIs('{{module}}::sliders.edit');
    }

    public function testCanUpdateSlider()
    {
        $slider = factory(Slider::class)->create();
        $newData = factory(Slider::class)->make();
        $response = $this->actingAs($this->user)->put(route('slider.update', $slider), $newData->toArray());

        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);
        $this->assertDatabaseHas('sliders', $newData->toArray());
        $response->assertRedirect(route('slider.index'));
    }

    public function testCanDeleteSlider()
    {
        $slider = factory(Slider::class)->create();
        $response = $this->actingAs($this->user)->delete(route('slider.destroy', $slider));
        $response->assertStatus(302);
        $this->assertDatabaseMissing('sliders', $slider->toArray());

        $response->assertRedirect(route('slider.index'));
    }
}
