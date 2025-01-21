<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_categories()
    {
        $response = $this->getJson('/api/category');
        $response->assertStatus(200);
    }
}
