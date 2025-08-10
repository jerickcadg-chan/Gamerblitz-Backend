<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentMethodController extends Controller
{
    private $title;

    public function __construct()
    {
        $this->title = 'Payment Method';

        $this->middleware(['permission:View Payment Methods'])->only('index', 'show');
        $this->middleware(['permission:Create Payment Methods'])->only(['create', 'store']);
        $this->middleware(['permission:Edit Payment Methods'])->only('edit', 'update');
        $this->middleware(['permission:Delete Payment Methods'])->only('destroy');
    }

    public function index()
    {
        $payment_methods = PaymentMethod::latest()
            ->paginate();

        $createLink = route('payment_method.create');

        $title = $this->title;

        return view('payment_methods.index', compact('payment_methods', 'createLink', 'title'));
    }

    public function create()
    {
        $storeLink = route('payment_method.store');
        $indexLink = route('payment_method.index');

        $title = $this->title;

        return view('payment_methods.create', compact('storeLink', 'indexLink', 'title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'bank_account' => 'required',
            'account_number' => 'required|max:20',
        ]);
        $name = Str::of($request->bank_account)->append(' ', $request->account_number, ' a/n ', $request->name);

        /** @var \App\Models\PaymentMethod $payment_method */
        $payment_method = PaymentMethod::create([
            'name' => $name,
            'vendor' => 'manual',
            'category' => 'bank',
            'admin_fee' => 0,
            'admin_type' => 'no-admin',
            'slug' => Str::slug($name),
            'client_id' => Auth::user()->client->id,
        ]);

        toast(alert_created_text($this->title), 'success');

        return redirect()->route('payment_method.show', $payment_method);
    }

    public function edit(PaymentMethod $payment_method)
    {
        $updateLink = route('payment_method.update', $payment_method);
        $indexLink = route('payment_method.index');
        $title = $this->title;

        [$bank_account, $account_number, $an, $name] = Str::of($payment_method->name)->explode(' ', 4);

        return view('payment_methods.edit', [
            'updateLink' => $updateLink,
            'indexLink' => $indexLink,
            'payment_method' => $payment_method,
            'title' => $title,
            'bank_account' => $bank_account,
            'account_number' => $account_number,
            'name' => $name,
        ]);
    }

    public function update(Request $request, PaymentMethod $payment_method)
    {
        $request->validate([
            'name' => 'required',
            'bank_account' => 'required',
            'account_number' => 'required|max:20',
        ]);
        $name = Str::of($request->bank_account)->append(' ', $request->account_number, ' a/n ', $request->name);

        $payment_method->update([
            'name' => $name,
            'admin_fee' => 0,
            'vendor' => 'manual',
            'category' => 'bank',
            'admin_type' => 'no-admin',
            'slug' => Str::slug($name),
            'client_id' => Auth::user()->client->id,
        ]);

        toast(alert_updated_text($this->title), 'success');

        return redirect()->route('payment_method.show', $payment_method);
    }

    public function show(PaymentMethod $payment_method)
    {
        $editLink = route('payment_method.edit', $payment_method);
        $deleteLink = route('payment_method.destroy', $payment_method);
        $indexLink = route('payment_method.index');
        $title = $this->title;

        return view('payment_methods.show', compact('payment_method', 'editLink', 'indexLink', 'deleteLink', 'title'));
    }

    public function destroy(PaymentMethod $payment_method)
    {
        $payment_method->delete();
        toast(alert_deleted_text($this->title), 'success');

        return redirect()->route('payment_method.index');
    }
}
