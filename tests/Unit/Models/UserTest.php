<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function testFactory()
    {
        $user = User::factory()->create();
        $this->assertNotEmpty($user->name);
        $this->assertNotEmpty($user->email);
        $this->assertNotEmpty($user->password);
        $this->assertNotEmpty($user->client_id);
    }

    public function testFillable()
    {
        $user = new User();
        $this->assertEquals(['name', 'email', 'password', 'phone_number', 'client_id'], $user->getFillable());
    }

    public function testDates()
    {
        $user = new User();
        $this->assertEquals(['created_at', 'updated_at'], $user->getDates());
    }

    public function testToArray()
    {
        $user = User::factory()->create()->fresh();
        $array = $user->toArray();
        $this->assertSame(array_keys($array), [
            'id',
            'client_id',
            'name',
            'email',
            'email_verified_at',
            'phone_number',
            'address',
            'created_at',
            'updated_at',
        ]);
    }

    public function testClient()
    {
        $user = User::factory()->create();
        $this->assertNotEmpty($user->client);
    }
}
