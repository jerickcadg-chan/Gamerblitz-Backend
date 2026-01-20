<?php

namespace Tests\Unit\Models;

use App\Models\Slider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SliderTest extends TestCase
{
    use RefreshDatabase;

    public function testFactory()
    {
        $slider = Slider::factory()->create();
        $this->assertNotEmpty($slider->name);
        $this->assertNotEmpty($slider->url);
        $this->assertNotEmpty($slider->start_date);
        $this->assertNotEmpty($slider->end_date);
        $this->assertNotEmpty($slider->client);
    }

    public function testFillable()
    {
        $slider = new Slider();
        $this->assertEquals(['name', 'url', 'start_date', 'end_date'], $slider->getFillable());
    }

    public function testDates()
    {
        $slider = new Slider();
        $this->assertEquals(['created_at', 'updated_at'], $slider->getDates());
    }

    public function testToArray()
    {
        $slider = Slider::factory()->create();
        $array = $slider->toArray();

        $this->assertSame(array_keys($array), [
            'name',
            'url',
            'start_date',
            'end_date',
            'client_id',
            'updated_at',
            'created_at',
            'id',
        ]);
    }
}
