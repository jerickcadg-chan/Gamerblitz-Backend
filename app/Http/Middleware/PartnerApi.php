<?php

namespace App\Http\Middleware;

use App\Models\UserApi;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PartnerApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $rawToken = $request->header('Authorization');

        if (!$rawToken) {
            return api_status_warning('Missing token!');
        }

        $api = UserApi::where('token', $rawToken)->first();

        if (!$api) {
            return api_status_warning('Invalid credentials!');
        }

        $eloquentUser = $api->user;

        if (!$eloquentUser) {
            return api_status_warning('User not found!');
        }

        Auth::onceUsingId($eloquentUser->id);

        $request->attributes->add([
            'partner' => $api,
            'user' => $eloquentUser,
        ]);

        return $next($request);
    }
}
