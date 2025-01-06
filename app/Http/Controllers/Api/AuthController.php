<?php

namespace App\Http\Controllers\Api;

use App\Mail\SentVerificationLink;
use App\Models\Balance;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function login(Request $request)
    {
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
            if (! Auth::attempt($credentials)) {
                $credentials = [
                    'phone_number' => convert_to_62($request->username),
                    'password' => $request->password,
                ];

                if (! Auth::attempt($credentials)) {
                    return api_status_warning(trans('auth.failed'));
                }
            }

            /** @var User $user */
            $user = $request->user();

            if ($user?->client_id == null || $user?->client_id != client()?->id) {
                return api_status_warning('User not found', 404);
            }

            if ($user->email_verified_at == null) {
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
        $request['phone_number'] = convert_to_62($request->phone_number);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'numeric', 'unique:users'],
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
            $user->client()->associate(client());
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
        return api_status_ok(transformer(auth()->user(), UserTransformer::class));
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return api_status_ok(transformer(auth()->user(), UserTransformer::class), trans('auth.loged_out'));
    }

    public function myBalance()
    {
        $balance = Balance::where('user_id', auth()->user()->id)->first();

        return api_status_ok(rp_format($balance?->amount ?? 0));
    }
}
