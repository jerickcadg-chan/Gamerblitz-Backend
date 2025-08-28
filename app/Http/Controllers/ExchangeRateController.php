<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExchangeRateRequest;
use App\Models\ExchangeRate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ExchangeRateController extends Controller
{
    private string $title;

    public function __construct()
    {
        $this->title = 'Exchange Rate';

        $this->middleware(['permission:View Exchange rate'])->only('index', 'show');
        $this->middleware(['permission:Create Exchange rate'])->only(['create', 'store']);
        $this->middleware(['permission:Edit Exchange rate'])->only('edit', 'update');
        $this->middleware(['permission:Delete Exchange rate'])->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exchangeRates = ExchangeRate::select('id', 'currency_code', 'rate', 'effective_at')
            ->orderByDesc('effective_at')
            ->when(request('currency_code'), fn ($q) => $q->where('currency_code', 'like', '%'.request('currency_code').'%'))
            ->get()
            ->unique('currency_code')
            ->sort(function ($a, $b) {
                if ($a->currency_code === 'USD') return -1;   // USD always first
                if ($b->currency_code === 'USD') return 1;
                return strcmp($a->currency_code, $b->currency_code); // alphabetical
            })
            ->values();

        $createLink = route('exchange_rate.create');

        $title = $this->title;

        return view('exchange_rates.index', compact('exchangeRates', 'createLink', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $actionLink = route('exchange_rate.store');
        $indexLink = route('exchange_rate.index');

        $title = $this->title;

        return view('exchange_rates.form', compact('actionLink', 'indexLink', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ExchangeRateRequest $request)
    {
        $exchangeRate = new ExchangeRate();
        $exchangeRate->currency_code = $request->input('currency_code');
        $exchangeRate->rate = $request->input('rate');
        $exchangeRate->effective_at = now();
        $exchangeRate->save();

        toast(alert_created_text($this->title), 'success');
        return redirect()->route('exchange_rate.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(ExchangeRate $exchangeRate)
    {
        $title = $this->title;
        $exchangeRates = ExchangeRate::where('currency_code', $exchangeRate->currency_code)->orderBy('effective_at', 'desc')->get();

        return view('exchange_rates.show', compact('title', 'exchangeRate', 'exchangeRates'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExchangeRate $exchangeRate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExchangeRate $exchangeRate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExchangeRate $exchangeRate)
    {
        ExchangeRate::where('currency_code', $exchangeRate->currency_code)->delete();

        toast(alert_deleted_text($this->title), 'success');
        return redirect()->route('exchange_rate.index');
    }
}
