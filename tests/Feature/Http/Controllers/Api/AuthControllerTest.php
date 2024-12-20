<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login(): void
    {
        $user = $this->generateCustomerUser();

        $this->postJson('api/login', [
            'username' => $user->email,
            'password' => 'password',
        ])->assertStatus(200);
    }
}
