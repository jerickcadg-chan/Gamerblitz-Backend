<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class ForgotPassword extends Controller
{
    public function forgot(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        /** @var User $user */
        $user = User::where('email', $request->email)->where('client_id', client()->id)->first();
        if (!$user) {
            return api_status_warning('Email not found!', 404);
        }
        $token = $user->createToken(
            name: 'password-reset',
            expiresAt: now()->addMinutes(10)
        )->plainTextToken;

        $user->sendPasswordResetNotification(
            $token
        );

        return api_status_ok([], 'Password reset link has been sent to your email!');
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|confirmed',
        ]);

        [$id, $token] = explode('|', $request->token, 2);

        $user = User::where('email', $request->email)->where('client_id', client()->id)->first();
        $tokenModel = PersonalAccessToken::find($id);

        if ($tokenModel->expires_at && $tokenModel->expires_at->isPast()) {
            return api_status_warning('Token has expired!', 400);
        }

        if (!hash_equals($tokenModel->token, hash('sha256', $token))) {
            return api_status_warning('Invalid token!', 400);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $user->tokens()->delete();

        return api_status_ok([], 'Password has been reset successfully!');
    }
}
