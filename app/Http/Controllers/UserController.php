<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Affiliate;
use App\Models\AffiliateHistory;
use App\Models\Balance;
use App\Models\BalanceHistory;
use App\Models\User;
use App\Services\BalanceService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $this->middleware(['permission:Ban User'])->only('ban');
        $this->middleware(['permission:Unban User'])->only('unban');
    }

    public function index()
    {
        $users = User::nonCustomer()->latest()
            ->when(request('name'), function ($query) {
                return $query->where('name', 'like', '%'.request('name').'%');
            })
            ->paginate();

        $createLink = route('user.create');

        $title = $this->title;

        return view('users.index', compact('users', 'createLink', 'title'));
    }

    public function ban(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'ban_ip' => 'nullable|boolean',
        ]);

        $user->update([
            'banned_at' => now(),
            'ban_reason' => $request->reason,
        ]);

        if ($request->ban_ip) {
            // Get all unique IPs from user_ip_logs
            $ipLogs = DB::table('user_ip_logs')
                ->where('user_id', $user->id)
                ->select('ip_address')
                ->distinct()
                ->get();

            foreach ($ipLogs as $ipLog) {
                \App\Models\BannedIp::firstOrCreate(
                    ['ip_address' => $ipLog->ip_address],
                    [
                        'banned_at' => now(),
                        'ban_reason' => $this->getUserIpBanReason($user, $request->reason),
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'User banned successfully.');
    }

    public function unban(Request $request, User $user)
    {
        $user->update([
            'banned_at' => null,
            'ban_reason' => null,
        ]);

        // Unban IPs that were banned along with this user
        $userIps = DB::table('user_ip_logs')
            ->where('user_id', $user->id)
            ->select('ip_address')
            ->distinct()
            ->pluck('ip_address');

        \App\Models\BannedIp::whereIn('ip_address', $userIps)
            ->where('ban_reason', 'like', $this->getUserIpBanReason($user, '%'))
            ->delete();

        return redirect()->back()->with('success', 'User unbanned successfully.');
    }

    public function getCustomer()
    {
        $users = User::customer()->latest()
            ->when(request('name'), function ($query) {
                return $query->where('name', 'like', '%'.request('name').'%');
            })
            ->when(request('email'), function ($query) {
                return $query->where('email', 'like', '%'.request('email').'%');
            })
            ->when(request('phone'), function ($query) {
                return $query->where('phone_number', 'like', '%'.request('phone').'%');
            })
            ->when(request('banned'), function ($query) {
                return $query->whereNotNull('banned_at');
            })
            ->when(request('affiliate'), function ($query) {
                return $query->whereHas('affiliate', function ($q) {
                    $q->where('status', 'active');
                });
            })
            ->paginate();

        $createLink = route('user.create');

        $title = 'Customer';

        return view('users.customer', compact('users', 'createLink', 'title'));
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

        $balance = Balance::query()->firstOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'amount' => 0,
            ]
        );

        $balanceHistories = BalanceHistory::where('balance_id', $balance->id)
            ->latest()
            ->paginate();

        $affiliateHistories = [];

        if ($user->affiliate) {
            $affiliateHistories = AffiliateHistory::with('affiliateable')
                ->where('affiliate_id', $user->affiliate->id)
                ->latest()
                ->paginate();
        }

        $ipLogs = DB::table('user_ip_logs')->where('user_id', $user->id)->latest()->paginate();

        $title = $this->title;

        return view('users.show', compact('user', 'balanceHistories', 'affiliateHistories', 'ipLogs', 'editLink', 'indexLink', 'deleteLink', 'title'));
    }

    public function store(UserRequest $request)
    {
        $user = User::create($request->all());

        $user->assignRole($request->role_id);

        if ($request->affiliate_on == 1) {
            if (! $user->affiliate) {
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
                    'code' => $code,
                    'status' => 'active',
                    'balance' => 0,
                ]);
            }
        }

        toast(alert_created_text($this->title), 'success');

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
                    'code' => $code,
                    'status' => 'active',
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

        toast(alert_deleted_text($this->title), 'success');

        return redirect()->route('user.index');
    }

    public function topUpManual(User $user)
    {
        $actionLink = route('user.top-up-manual.store', $user);
        $indexLink = route('user.index');

        $title = 'Top up Manual '.$this->title;

        return view('users.topup-manual', compact('actionLink', 'indexLink', 'title', 'user'));
    }

    public function topUpManualStore(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required',
        ]);

        try {
            $balance = Balance::query()->lockForUpdate()->firstOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'amount' => 0,
                ]
            );

            BalanceService::update($balance, [
                'balanceable_type' => User::class,
                'balanceable_id' => Auth::id(),
                'amount' => $request->amount,
                'description' => 'Topup Balance by Admin',
            ]);

            toast('Top up manual success', 'success');

            return redirect()->route('user.show', $user);
        } catch (\Exception $e) {
            toast('Top Up Manual Failed '.$e->getMessage(), 'error');

            return redirect()->back();
        }
    }

    private function getUserIpBanReason(User $user, string $reason): string
    {
        return 'Banned along with user '.$user->email.': '.$reason;
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
            $candidate = $base.$i;
            $i++;
            if ($i > 9999) { // fallback hard-stop
                $candidate = $this->generateUniqueAffiliateCode();
                break;
            }
        }

        return $candidate;
    }
}
