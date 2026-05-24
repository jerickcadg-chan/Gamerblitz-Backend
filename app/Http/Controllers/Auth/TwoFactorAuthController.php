<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        $user = Auth::user();
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $activePage = '2fa';
        $title = 'Two-Factor Authentication';

        return view('auth.2fa', compact('user', 'secret', 'qrCodeUrl', 'activePage', 'title'));
    }

    public function enable(Request $request)
    {
        $request->validate([
            'secret' => 'required|string',
            'one_time_password' => 'required|string',
        ]);

        $user = Auth::user();
        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey($request->secret, $request->one_time_password);

        if ($valid) {
            $user->update(['google2fa_secret' => $request->secret]);
            toast('2FA enabled successfully', 'success');
            return redirect()->route('2fa.show');
        } else {
            toast('Invalid 2FA code', 'error');
            return redirect()->back();
        }
    }

    public function disable(Request $request)
    {
        $user = Auth::user();
        $user->update(['google2fa_secret' => null]);
        toast('2FA disabled successfully', 'success');
        return redirect()->route('2fa.show');
    }
}