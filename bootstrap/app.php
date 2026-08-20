<?php

use App\Http\Middleware\CheckBanned;
use App\Http\Middleware\CheckBannedIp;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->prepend(CheckBanned::class);
        $middleware->prepend(CheckBannedIp::class);
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'not_customer' => \App\Http\Middleware\EnsureNotCustomer::class,
            'only_verified' => \App\Http\Middleware\OnlyVerifiedUserCan::class,
            'basic_auth' => \App\Http\Middleware\BasicAuth::class,
            'ip.whitelist' => \App\Http\Middleware\IpWhitelist::class,
            'partner-api' => \App\Http\Middleware\PartnerApi::class,
            'enforce.2fa' => \App\Http\Middleware\Enforce2FA::class,
            'session.timeout' => \App\Http\Middleware\SessionTimeout::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Never redirect guests to the private admin access URL. Protected
        // browser requests instead render an ordinary 404 page.
        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            return response()->view('errors.404', [], 404);
        });
    })->create();
