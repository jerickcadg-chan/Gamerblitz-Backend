<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    protected ?User $user;

    public function generateCustomerUser(): User
    {
        Artisan::call('db:seed', ['--class' => 'RolesTableSeeder']);

        $user = User::factory()
            ->create();
        $user->assignRole('Customer');


        $this->user = $user;

        return $user;
    }
}
