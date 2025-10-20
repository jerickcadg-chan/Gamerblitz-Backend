<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentMethodRequest;
use App\Models\ExchangeRate;
use App\Models\PaymentMethod;
use App\Services\PictureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentMethodController extends Controller
{
    private string $title;

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
        $paymentMethods = PaymentMethod::latest()
            ->paginate();

        $createLink = route('payment_method.create');

        $title = $this->title;

        return view('payment_methods.index', compact('paymentMethods', 'createLink', 'title'));
    }

    public function create()
    {
        $formAction = route('payment_method.store');

        $title = $this->title;

        return view('payment_methods.form', compact('formAction', 'title'));
    }

    public function store(PaymentMethodRequest $request)
    {
        $data = array_merge(
            $request->all(),
            [
                'additional_input' => json_decode($request?->additional_input ?? '', true)
            ]
        );

        $pictureService = new PictureService();

        if ($request->hasFile('default_picture')) {
            $request['picture'] = $pictureService->insert($request->default_picture);
        }

        $paymentMethod = PaymentMethod::create($data);

        toast(alert_created_text($this->title), 'success');
        return redirect()->route('payment_method.index');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        $formAction = route('payment_method.update', $paymentMethod);

        $title = $this->title;

        return view('payment_methods.form', compact('title', 'formAction', 'paymentMethod'));
    }

    public function update(PaymentMethodRequest $request, PaymentMethod $paymentMethod)
    {
        $data = array_merge(
            $request->all(),
            [
                'additional_input' => json_decode($request?->additional_input ?? '', true)
            ]
        );

        $pictureService = new PictureService();

        if ($request->hasFile('default_picture')) {
            $request['picture'] = $pictureService->insert($request->default_picture);
        }

        $paymentMethod->update($data);

        toast(alert_updated_text($this->title), 'success');

        return redirect()->route('payment_method.index');
    }

    public function show(PaymentMethod $paymentMethod)
    {
        $editLink = route('payment_method.edit', $paymentMethod);
        $deleteLink = route('payment_method.destroy', $paymentMethod);
        $indexLink = route('payment_method.index');
        $title = $this->title;

        return view('payment_methods.show', compact('paymentMethod', 'editLink', 'indexLink', 'deleteLink', 'title'));
    }

    public function destroy(PaymentMethod $payment_method)
    {
        $payment_method->delete();
        toast(alert_deleted_text($this->title), 'success');

        return redirect()->route('payment_method.index');
    }
}
