<?php

namespace App\Http\Controllers;

use App\Constants\StatusConst;
use App\Http\Requests\UserRequest;
use App\Models\Affiliate;
use App\Models\Deposit;
use App\Models\Setting;
use App\Models\User;
use App\Services\DepositService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private string $title;

    public function __construct()
    {
        $this->title = 'User';

        $this->middleware(['permission:View User'])->only('index', 'show');
        $this->middleware(['permission:Create User'])->only(['create', 'store']);
        $this->middleware(['permission:Edit User'])->only('edit', 'update');
        $this->middleware(['permission:Delete User'])->only('destroy');
    }

    public function index()
    {
        $users = User::nonCustomer()->latest()
            ->when(request('name'), function ($query) {
                return $query->where('name', 'like', '%'. request('name') .'%');
            })
            ->paginate();

        $createLink = route('user.create');

        $title = $this->title;

        return view('users.index', compact('users', 'createLink', 'title'));
    }

    public function getCustomer()
    {
        $users = User::customer()->latest()
            ->when(request('name'), function ($query) {
                return $query->where('name', 'like', '%'. request('name') .'%');
            })
            ->paginate();

        $title = $this->title;

        return view('users.customer', compact('users', 'title'));
    }

    public function create()
    {
        $actionLink = route('user.store');
        $indexLink = route('user.index');

        $title = $this->title;

        $roles = Role::all();

        return view('users.form', compact('roles', 'actionLink', 'indexLink', 'title'));
    }

    public function show(User $user)
    {
        $editLink = route('user.edit', $user);
        $deleteLink = route('user.destroy', $user);
        $indexLink = route('user.index');

        $deposits = Deposit::with('user')
            ->where('user_id', $user->id)
            ->latest()
            ->when(\request('code'), function (Builder $query) {
                $query->where('code', 'like', '%' . \request('code') . '%');
            })
            ->paginate();

        $title = $this->title;

        return view('users.show', compact('user', 'deposits','editLink', 'indexLink', 'deleteLink', 'title'));
    }

    public function store(UserRequest $request)
    {
        $user = User::create($request->all());

        $user->assignRole($request->role_id);

        if ($request->affiliate_on == 1) {
            if (!$user->affiliate) {
                $code = $this->generateUniqueAffiliateCode();

                if (Affiliate::where('code', $code)->exists()) {
                    if ($request->filled('affiliate_code')) {
                        $code = $this->makeUniqueFromBase($code);
                    } else {
                        $code = $this->generateUniqueAffiliateCode();
                    }
                }

                Affiliate::create([
                    'user_id' => $user->id,
                    'code'    => $code,
                    'status'  => 'active',
                    'balance' => 0,
                ]);
            }
        }

        toast(alert_created_text($this->title),'success');
        return redirect()->route('user.show', $user);
    }

    public function edit(User $user)
    {
        $actionLink = route('user.update', $user);
        $indexLink = route('user.index');

        $title = $this->title;

        $roles = Role::all();

        return view('users.form', compact('roles', 'actionLink', 'indexLink', 'user', 'title'));
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->all();
        if (empty($request->password)) {
            unset($data['password']);
        }
        $user->update($data);

        $user->syncRoles($request->role_id);

        if ($request->affiliate_on == 1) {
            if ($user->affiliate) {
                $user->affiliate->update(['status' => 'active']);
            } else {
                $code = $request->filled('affiliate_code')
                    ? strtoupper(trim($request->affiliate_code))
                    : $this->generateUniqueAffiliateCode();

                if (Affiliate::where('code', $code)->exists()) {
                    $code = $this->makeUniqueFromBase($code);
                }

                Affiliate::create([
                    'user_id' => $user->id,
                    'code'    => $code,
                    'status'  => 'active',
                    'balance' => 0,
                ]);
            }
        } else {
            if ($user->affiliate) {
                $user->affiliate->update(['status' => 'inactive']);
            }
        }

        toast(alert_updated_text($this->title), 'success');
        return redirect()->route('user.show', $user);
    }

    public function destroy(User $user)
    {
        $user->delete();

        toast(alert_deleted_text($this->title),'success');
        return redirect()->route('user.index');
    }

    public function topUpManual(User $user)
    {
        $actionLink = route('user.top-up-manual.store', $user);
        $indexLink = route('user.index');

        $title = 'Top up Manual ' . $this->title;

        return view('users.topup-manual', compact('actionLink', 'indexLink', 'title', 'user'));
    }

    public function topUpManualStore(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required'
        ]);

        $baseCurrency = Setting::getBaseCurrency();
        $exchangeRateToBase = get_exchange_rate($baseCurrency, $baseCurrency);

        $amount = (int) $request->amount;
        $adminFee = 0;

        $totalAmount = $amount + $adminFee;

        $baseAmount = $amount * $exchangeRateToBase;
        $baseAdminFee = $adminFee * $exchangeRateToBase;
        $baseTotalAmount = $totalAmount * $exchangeRateToBase;

        try {
            $deposit = Deposit::create([
                'code' => "DP".date('ymd').rand(1000, 999999),
                'user_id' => $user->id,
                'payment_method_id' => 1,
                'currency_code' => $baseCurrency,
                'converted_currency_code' => $baseCurrency,
                'exchange_rate' => $exchangeRateToBase,
                'amount' => $baseAmount,
                'admin_fee' => $baseAdminFee,
                'total_amount' => $baseTotalAmount,
                'converted_amount' => $amount,
                'converted_admin_fee' => $adminFee,
                'converted_total_amount' => $totalAmount,
                'expired_at' => now()->addHours(3),
                'status' => StatusConst::PENDING,
            ]);
    
            DepositService::updateStatus($deposit, StatusConst::PAID, $baseTotalAmount);

            toast("Deposit status updated", 'success');

            return redirect()->route('user.show', $user);
        } catch (\Exception $e) {
            toast("Top Up Manual Failed " . $e->getMessage(), 'error');

            return redirect()->back();
        }
    }

    private function generateUniqueAffiliateCode(): string
    {
        do {
            $code = Str::upper(Str::random(8)); // A-Z0-9
        } while (Affiliate::where('code', $code)->exists());

        return $code;
    }

    private function makeUniqueFromBase(string $base): string
    {
        $base = Str::upper(preg_replace('/\s+/', '', $base));
        $candidate = $base;
        $i = 1;
        while (Affiliate::where('code', $candidate)->exists()) {
            $candidate = $base . $i;
            $i++;
            if ($i > 9999) { // fallback hard-stop
                $candidate = $this->generateUniqueAffiliateCode();
                break;
            }
        }
        return $candidate;
    }
}
