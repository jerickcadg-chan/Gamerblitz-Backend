<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class IpWhitelist
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): \Symfony\Component\HttpFoundation\Response  $next
     */
    public function handle(Request $request, Closure $next, mixed ...$allowedIps): Response
    {
        if ($allowedIps && !in_array($request->ip(), $allowedIps)) {
            Log::warning("Unauthorized IP: {$request->ip()}");
            abort(403, 'Unauthorized IP');
        }
        return $next($request);
    }
}
