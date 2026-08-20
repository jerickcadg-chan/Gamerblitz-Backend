<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Do not disclose the private login route to unauthenticated visitors.
     * Browser requests for protected admin pages receive a normal 404 instead.
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            abort(404);
        }
    }
}
