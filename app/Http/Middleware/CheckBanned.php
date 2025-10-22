<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->banned_at) {
            auth()->logout();
            return redirect('/login')->with('error', 'Your account has been banned. Reason: ' . (auth()->user()->ban_reason ?? 'No reason provided'));
        }

        return $next($request);
    }
}
