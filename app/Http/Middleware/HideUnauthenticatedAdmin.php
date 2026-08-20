<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HideUnauthenticatedAdmin
{
    /**
     * Make protected back-office pages indistinguishable from non-existent pages
     * to guests. This middleware runs before Laravel's standard auth redirect.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            abort(404);
        }

        return $next($request);
    }
}
