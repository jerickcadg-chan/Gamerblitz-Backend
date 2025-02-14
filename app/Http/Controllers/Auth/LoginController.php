<?php

namespace App\Http\Controllers\Auth;

use App\Constants\DefaultRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }
        $user = $this->attemptLogin($request);

        if ($user) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }

            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        $user = User::whereEmail($credentials['email'])->first();
        if ($user?->client?->id !== client()->id) {
            return false;
        }
        if ($user->first_login) {
            $tmpRole = DB::table('model_has_roles')->where('model_type', 'App\Models\Clients\User')->where('model_id', $user->id)->first();
            if ($tmpRole) {
                $user->syncRoles(DefaultRole::SUPER_ADMIN);
            }
            $user->password = bcrypt($user->password);
            $user->first_login = false;
            $user->save();
        }
        $user = $this->guard()->attempt(
            $credentials,
            $request->boolean('remember')
        );

        return $user;
    }
}
