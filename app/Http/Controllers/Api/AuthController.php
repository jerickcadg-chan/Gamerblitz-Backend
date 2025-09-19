<?php

namespace App\Http\Controllers\Api;

use App\Mail\SentVerificationLink;
use App\Models\Balance;
use App\Models\Setting;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Number;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

/** @package App\Http\Controllers\Api */
class AuthController extends Controller
{
    public function login(Request $request)
    {
        Auth::logout();
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return api_status_warning($validator->messages()->first(), 422);
        }

        $credentials = [
            'email' => $request->username,
            'password' => $request->password,
        ];

        try {
            /** @var User $user */
            $user = User::whereEmail($credentials['email'])->first();
            if (!$user) {
                $credentials = [
                   'phone_number' => $request->username,
                   'password' => $request->password,
                ];

                $user = User::wherePhoneNumber($credentials['phone_number'])->first();

                if (!$user) {
                    return api_status_warning(trans('auth.failed'));
                }
            }

            if (!Hash::check($credentials['password'], $user->password)) {
                return api_status_warning(trans('auth.failed'));
            }

            if ($user->email_verified_at == null) {
                $user->sendEmailVerificationNotification();
                return api_status_warning(trans('auth.unverified'), 400);
            }

            return api_status_ok([
                'token' => $user->createToken('access_token')->plainTextToken,
                'user' => transformer($user, UserTransformer::class),
            ]);
        } catch (\Exception $exception) {
            return api_status_error($exception);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'phone_number' => ['required', 'numeric', Rule::unique('users')],
            'password' => ['required', 'string', 'min:4', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return api_status_warning($validator->messages()->first(), 422);
        }

        try {
            DB::beginTransaction();
            $request->merge([
                'password' => bcrypt($request->password),
            ]);

            /** @var User $user */
            $user = User::create($request->all());

            $role = Role::where('name', 'Customer')->first();
            $user->assignRole($role);
            $user->save();

            $user->balance()->create([
                'amount' => 0,
            ]);

            $user->sendEmailVerificationNotification();

            DB::commit();

            return api_status_ok([
                'token' => $user->createToken('access_token')->plainTextToken,
                'user' => transformer($user, UserTransformer::class),
            ]);
        } catch (\Exception $exception) {
            return api_status_error($exception);
        }
    }

    public function me()
    {
        $baseCurrency = Setting::getBaseCurrency();
        $userCurrencyCode = request('currency_code', $baseCurrency);
        $exchangeRate = get_exchange_rate($baseCurrency, $userCurrencyCode);
        return api_status_ok(transformer(auth()->user(), new UserTransformer($exchangeRate)));
    }

    public function updateMe(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['string', 'email', 'max:255', 'unique:users,email,' . auth()->id()],
            'whatsapp_number' => ['required', 'numeric'],
            'current_password' => ['nullable', 'string'],
            'password' => ['required_with:current_password', 'nullable', 'string', 'min:4', 'confirmed'],
        ]);

        try {
            /** @var User $user */
            $user = Auth::user();
            $update = [
                'email' => $validatedData['email'] ?? $user->email,
            ];
            if ($request->filled('current_password')) {
                if (!Hash::check($request->get('current_password'), $user->password)) {
                    return api_status_warning('Current password is incorrect', 422);
                }

                $update['password'] = bcrypt($request->get('password'));
            }
            $user->update($update);

            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $validatedData
            );

            return api_status_ok(transformer($user, UserTransformer::class), 'Profile updated');
        } catch (\Exception $exception) {
            return api_status_error($exception);
        }
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return api_status_ok(transformer(auth()->user(), UserTransformer::class), trans('auth.loged_out'));
    }

    public function myBalance(Request $request)
    {
        $request->validate([
            'currency_code' => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
        ]);

        $userCurrency = $request->currency_code;

        $exchangeRate = get_exchange_rate(Setting::getBaseCurrency(), $userCurrency);
        $balance = Balance::where('user_id', auth()->user()->id)->first();

        $amount = $balance ? $balance->amount * $exchangeRate : 0;

        return api_status_ok($amount);
    }
}
