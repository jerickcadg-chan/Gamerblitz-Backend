<?php

namespace App\Http\Controllers;

use App\Events\UserActivityLogged;
use App\Services\DepositService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Deposit;

class DepositController extends Controller
{
    private string $title;

    public function __construct()
    {
        $this->title = 'Deposit';

        $this->middleware(['permission:View Deposit'])->only('index', 'show');
        $this->middleware(['permission:Edit Deposit'])->only('updateStatus');
    }

    public function index()
    {
        $deposits = Deposit::with('user', 'updater')
            ->latest()
            ->whereHas('user', function (Builder $query) {
                $query
                    ->when(request('name'), function (Builder $query) {
                        $query->where('name', 'like', '%' . \request('name') . '%');
                    });
            })
            ->when(\request('code'), function (Builder $query) {
                $query->where('code', 'like', '%' . \request('code') . '%');
            })
            ->paginate();

        $title = $this->title;

        return view('deposits.index', compact('deposits', 'title'));
    }

    public function show(Deposit $deposit)
    {
        $title = $this->title;

        return view('deposits.show', compact('deposit', 'title'));
    }

    public function updateStatus(Deposit $deposit, Request $request)
    {
        try {
            $deposit->updated_by = auth()->user()->id;
            $deposit->save();

            // Log user action
            event(new UserActivityLogged(auth()->user()->id, request()->ip(), 'deposit_updated:' . $deposit->code));

            $action = DepositService::updateStatus($deposit, $request->status, $request->amount);

            if (!$action['status']) {
                toast($action['message'] ?? "Failed", 'error');
                return redirect()->back();
            }

            toast("Deposit status updated", 'success');
            return redirect()->route('deposit.index');
        } catch (\Exception $e) {
            toast("Deposit status failed", 'error');
            return redirect()->route('deposit.index');
        }
    }
}
