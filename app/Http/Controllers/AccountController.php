<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountStoreRequest;
use App\Http\Requests\AccountUpdateRequest;
use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AccountController extends Controller
{
    private $title;

    public function __construct()
    {
        $this->title = 'Akun';

        $this->middleware(['permission:View Acount'])->only('index', 'show', 'showTheInformation');
        $this->middleware(['permission:Create Acount'])->only(['create', 'store']);
        $this->middleware(['permission:Edit Acount'])->only('edit', 'update');
        $this->middleware(['permission:Delete Acount'])->only('destroy');
    }

    public function index()
    {
        $accounts = Account::whereByClient()
            ->latest()
            ->whereHas('productItem', function ($query) {
                $query->where('type', 'account');
            })
            ->when(request('name'), function ($query) {
                return $query->where('name', 'like', '%' . request('name') . '%');
            })
            ->paginate();

        $createLink = route('account.create');

        $title = $this->title;

        return view('accounts.index', compact('accounts', 'createLink', 'title'));
    }

    public function create()
    {
        $storeLink = route('account.store');
        $indexLink = route('account.index');

        $title = $this->title;

        return view('accounts.create', compact('storeLink', 'indexLink', 'title'));
    }

    public function store(AccountStoreRequest $request, AccountService $accountService)
    {
        $account = $accountService->store($request);
        if (!$account) {
            return back()->withInput();
        }

        toast(alert_created_text($this->title), 'success');

        return redirect()->route('account.show', $account);
    }

    public function edit(Account $account)
    {
        $account->load('productItem');
        $updateLink = route('account.update', $account);
        $indexLink = route('account.index');
        $title = $this->title;

        return view('accounts.edit', compact('updateLink', 'indexLink', 'account', 'title'));
    }

    public function update(AccountUpdateRequest $request, Account $account, AccountService $accountService)
    {
        $accountService->update($request, $account);

        toast(alert_updated_text($this->title), 'success');

        return redirect()->route('account.show', $account);
    }

    public function show(Account $account)
    {
        $account->load('productItem');
        $editLink = route('account.edit', $account);
        $deleteLink = route('account.destroy', $account);
        $indexLink = route('account.index');
        $title = $this->title;

        return view('accounts.show', compact('account', 'editLink', 'indexLink', 'deleteLink', 'title'));
    }

    public function destroy(Account $account, AccountService $accountService)
    {
        $accountService->delete($account);
        toast(alert_deleted_text($this->title), 'success');

        return redirect()->route('account.index');
    }

    public function showTheInformation(Account $account, Request $request)
    {
        $request->validate([
            'pin' => ['required', 'string', 'min:6', 'max:6']
        ]);

        $path = str(config('array.mitra-gamers.url'))->replaceEnd("/", "")->append('/api/v2/verify-pin')->value();

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Valid-Token' => 'YjcyZWM5NGMtODg4NC00ZjQ2LThiYTAtZmE4MzYzYTQ4ZGRm'
        ])->post($path, [
            'pin' => $request->pin
        ]);
        if ($response->ok()) {
            return response()->json([
                'data' => decrypt($account->information),
            ]);
        }

        return response([], 403);
    }
}
