<?php

namespace Tests\Unit\Helpers;

use App\Helpers\UniversalHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Transformers\ProductTransformer;

class UniversalHelperTest extends TestCase
{
    use RefreshDatabase;

    public function test_paginate_transformer()
    {
        User::factory()->create();
        Product::factory()->count(20)->create();

        $query = Product::query();

        $result = paginateTransformer($query, new ProductTransformer(), [], 5);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);
        $this->assertArrayHasKey('pagination', $result['meta']);
        $this->assertArrayHasKey('total', $result['meta']['pagination']);
        $this->assertArrayHasKey('count', $result['meta']['pagination']);
        $this->assertArrayHasKey('per_page', $result['meta']['pagination']);
        $this->assertArrayHasKey('current_page', $result['meta']['pagination']);
        $this->assertArrayHasKey('last_page', $result['meta']['pagination']);
        $this->assertArrayHasKey('has_more_pages', $result['meta']['pagination']);
        $this->assertArrayHasKey('from', $result['meta']['pagination']);
        $this->assertArrayHasKey('to', $result['meta']['pagination']);
        $this->assertArrayHasKey('links', $result['meta']['pagination']);
        $this->assertArrayHasKey('previous', $result['meta']['pagination']['links']);
        $this->assertArrayHasKey('next', $result['meta']['pagination']['links']);
        $this->assertArrayHasKey('current', $result['meta']['pagination']['links']);
        $this->assertArrayHasKey('first', $result['meta']['pagination']['links']);
        $this->assertArrayHasKey('last', $result['meta']['pagination']['links']);

        $this->assertCount(5, $result['data']);
    }
}

