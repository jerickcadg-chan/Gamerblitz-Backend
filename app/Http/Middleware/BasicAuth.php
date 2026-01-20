<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class BasicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authToken = $request->header('Authorization');
        if (!$authToken) {
            return api_status_warning('Unauthorized', 401);
        }
        $decoded = base64_decode(str_replace('Basic ', '', $authToken));
        if ($decoded !== Arr::join(config('app.basic_auth'),':')) {
            return api_status_warning('Unauthorized', 401);
        }

        return $next($request);
    }
}
