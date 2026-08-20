<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * Force logout admins after 3 hours of inactivity.
 * This replaces the old hourly 2FA re-prompt which caused redirect loops.
 */
class SessionTimeout
{
    const TIMEOUT_MINUTES = 180; // 3 hours

    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $lastActivity = Session::get('last_activity_at');

        if ($lastActivity) {
            $minutesInactive = (time() - $lastActivity) / 60;
            if ($minutesInactive >= self::TIMEOUT_MINUTES) {
                Auth::logout();
                Session::flush();

                // Do not reveal the private admin access URL after a forced logout.
                abort(404);
            }
        }

        // Update last activity timestamp on every request
        Session::put('last_activity_at', time());

        return $next($request);
    }
}
