<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Mail\SentVerificationLink;
use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $user = $this->generateCustomerUser();
        $response = $this->postJson('api/login', [
            'username' => $user->email,
            'password' => 'wrong-password',
        ])->assertStatus(400);

        $this->assertSame(trans('auth.failed'), $response['message']);
    }

    public function test_user_cannot_login_with_wrong_username(): void
    {
        $this->generateCustomerUser();
        $response = $this->postJson('api/login', [
            'username' => 'wrong-username',
            'password' => 'password',
        ])->assertStatus(400);

        $this->assertSame(trans('auth.failed'), $response['message']);
    }

    public function test_user_cannot_login_with_wrong_phone_number(): void
    {
        $this->generateCustomerUser();
        $response = $this->postJson('api/login', [
            'username' => '081234567891',
            'password' => 'password',
        ])->assertStatus(400);

        $this->assertSame(trans('auth.failed'), $response['message']);
    }

    public function test_user_cannot_login_with_wrong_email_format(): void
    {
        $this->generateCustomerUser();
        $response = $this->postJson('api/login', [
            'username' => 'wrong-email-format',
            'password' => 'password',
        ])->assertStatus(400);

        $this->assertSame(trans('auth.failed'), $response['message']);
    }

    public function test_user_doesnt_have_client_id(): void
    {
        $user = $this->generateCustomerUser();
        $user->client_id = null;
        $user->save();
        $response = $this->postJson('api/login', [
            'username' => $user->email,
            'password' => 'password',
        ])->assertStatus(404);

        $this->assertSame('User not found', $response['message']);
    }

    public function test_user_not_verified_yet(): void
    {
        $user = $this->generateCustomerUser();
        $user->email_verified_at = null;
        $user->save();

        $response = $this->postJson('api/login', [
            'username' => $user->email,
            'password' => 'password',
        ])->assertStatus(400);

        $this->assertSame(trans('auth.unverified'), $response['message']);
    }

    public function test_user_can_login(): void
    {
        $user = $this->generateCustomerUser();

        $this->postJson('api/login', [
            'username' => $user->email,
            'password' => 'password',
        ])->assertStatus(200);
    }

    public function test_user_can_login_with_phone_number(): void
    {
        $user = $this->generateCustomerUser();
        $this->postJson('api/login', [
            'username' => $user->phone_number,
            'password' => 'password',
        ])->assertStatus(200);
    }

    public function test_user_cannot_see_profile_without_login(): void
    {
        $this->getJson('api/me')->assertStatus(401);
    }

    public function test_user_cannot_see_profile_with_email_not_verified(): void
    {
        $user = $this->generateCustomerUser();
        $user->email_verified_at = null;
        $user->save();
        $this->actingAs($user);
        $this->getJson('api/me')->assertStatus(401);
    }

    public function test_user_can_see_profile(): void
    {
        $user = $this->generateCustomerUser();
        $this->actingAs($user);
        $this->getJson('api/me')->assertStatus(200);
    }

    public function test_user_can_register(): void
    {
        Notification::fake();
        $this->generateSuperAdminUser();
        $data = [
            'name' => 'John Doe',
            'email' => 'john@mail.com',
            'phone_number' => '081234567890',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $this->postJson('api/register', $data)->assertStatus(200);

        /** @var User $user */
        $user = User::whereEmail($data['email'])->first();

        $client = $this->user->client;

        $this->assertNull($user->email_verified_at, 'Email should not be verified');
        $this->assertTrue('Customer' == $user->roles->first()->name, 'Role is not match');
        $this->assertTrue($client->id == $user->client_id, 'Client is not match');
        Notification::assertSentTo(
            $user,
            VerifyEmail::class,
            function ($notification, $channels, User $notifiable) use ($user) {
                return $notifiable->email == $user->email;
            }
        );
    }
}
