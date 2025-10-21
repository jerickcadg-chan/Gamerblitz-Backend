<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use PragmaRX\Google2FA\Google2FA;

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

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if ($user->google2fa_secret) {
                Session::put('2fa_user_id', $user->id);
                Session::put('2fa_remember', $request->boolean('remember'));
                Session::put('2fa_email', $request->email);
                return redirect()->back()->withInput($request->only('email'))->with('show_2fa_modal', true);
            }

            $this->guard()->login($user, $request->boolean('remember'));

            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }

            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function verify2fa(Request $request)
    {
        $userId = Session::get('2fa_user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        if (!$user || !$user->google2fa_secret) {
            Session::forget('2fa_user_id');
            return redirect()->route('login');
        }

        $request->validate([
            'one_time_password' => 'required|string',
        ]);

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->one_time_password);

        if ($valid) {
            $this->guard()->login($user, Session::get('2fa_remember', false));
            Session::forget(['2fa_user_id', '2fa_remember', '2fa_email']);
            return redirect()->intended($this->redirectPath());
        } else {
            toast('Invalid 2FA code', 'error');
            return redirect()->back()->withInput(['email' => Session::get('2fa_email')]);
        }
    }
}
