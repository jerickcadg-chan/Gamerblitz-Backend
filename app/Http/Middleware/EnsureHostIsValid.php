<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHostIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('api/clients/all')) {
            return $next($request);
        }
        $host = $request->getHost();
        $client = Client::whereHost($host)->first();
        if (!$client) {
            return api_status_warning('Host is invalid', 401);
        }

        $request->merge(['client' => $client]);

        return $next($request);
    }
}
