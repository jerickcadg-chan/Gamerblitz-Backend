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

    public function generateSuperAdminUser($data = []): User
    {
        Artisan::call('db:seed', ['--class' => 'RolesTableSeeder']);
        $user = User::factory()
            ->create();
        $user->assignRole('Super Admin');
        $this->user = $user;

        return $user;
    }

    public function assertToast(\Illuminate\Testing\TestResponse $response, array $config = []): void
    {
        $config = array_merge([
            'title' => '',
            'text' => '',
            'timer' => config('sweetalert.timer'),
            'background' => config('sweetalert.background'),
            'width' => config('sweetalert.width'),
            'padding' => config('sweetalert.padding'),
            'showConfirmButton' => false,
            'showCloseButton' => true,
            'confirmButtonText' => __(config('sweetalert.button_text.confirm')),
            'cancelButtonText' => __(config('sweetalert.button_text.cancel')),
            'timerProgressBar' => config('sweetalert.timer_progress_bar'),
            'customClass' => [
                'container' => config('sweetalert.customClass.container'),
                'popup' => config('sweetalert.customClass.popup'),
                'header' => config('sweetalert.customClass.header'),
                'title' => config('sweetalert.customClass.title'),
                'closeButton' => config('sweetalert.customClass.closeButton'),
                'icon' => config('sweetalert.customClass.icon'),
                'image' => config('sweetalert.customClass.image'),
                'content' => config('sweetalert.customClass.content'),
                'input' => config('sweetalert.customClass.input'),
                'actions' => config('sweetalert.customClass.actions'),
                'confirmButton' => config('sweetalert.customClass.confirmButton'),
                'cancelButton' => config('sweetalert.customClass.cancelButton'),
                'footer' => config('sweetalert.customClass.footer')
            ],
            'toast' => true,
            'icon' => 'error',
            'position' => 'top-end'
        ], $config);

        $response->assertSessionHas('alert', [
            'config' => json_encode($config)
        ]);
    }

    public function response_status_ok($data, int $code = 200, $message = 'data successfully retrieved'): array
    {
        $response = [
            'url' => url()->full(),
            'method' => request()->getMethod(),
            'code' => $code,
            'message' => $message,
            'payload' => $data,
        ];

        return $response;
    }

    public function response_status_warning($message, int $code = 400, ?array $data = null): array
    {
        $response = [
            'url' => url()->full(),
            'method' => request()->getMethod(),
            'code' => $code,
            'message' => $message,
            'request' => request()->except(['password']),
        ];

        if ($data) {
            $response['payload'] = $data;
        }

        return $response;
    }

    // protected function tearDown(): void
    // {
    //     // parent::tearDown();
    // }
}
