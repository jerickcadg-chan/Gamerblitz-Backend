<?php

namespace Tests\Unit\Models;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    public function testFactory()
    {
        $client = Client::factory()->create();
        $this->assertNotEmpty($client->name);
        $this->assertNotEmpty($client->logo);
        $this->assertNotEmpty($client->description);
        $this->assertNotEmpty($client->user_token);
    }

    public function testFillable()
    {
        $client = new Client();
        $this->assertEquals(['name', 'logo', 'description', 'user_token'], $client->getFillable());
    }

    public function testDates()
    {
        $client = new Client();
        $this->assertEquals(['created_at', 'updated_at'], $client->getDates());
    }

    public function testToArray()
    {
        $client = Client::factory()->create()->fresh();

        $array = $client->toArray();

        $this->assertSame(array_keys($array), [
            'id',
            'name',
            'logo',
            'description',
            'user_token',
            'created_at',
            'updated_at',
        ]);
    }
}
