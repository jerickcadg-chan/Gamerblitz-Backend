@extends('layouts.app', [
    'activePage' => 'exchange_rate',
])

@php
  $page = isset($exchangeRate) ? 'Create' : 'Edit';
@endphp

@section('content')
  <div class="page-header">
    <h3 class="page-title"> {{ $title }} Page </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('exchange_rate.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $page }} Data</li>
      </ol>
    </nav>
  </div>

  <div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <form method="POST" action="{{ $actionLink }}">
            @csrf
            @isset($exchangeRate)
              @method('PUT')
            @endisset
            <div class="form-group">
              <label for="input_currency_code" class="required">Currency Code</label>
              @isset($exchangeRate)
                <input type="text" name="currency_code"
                  class="form-control {{ $errors->has('currency_code') ? ' is-invalid' : '' }}" id="input_currency_code"
                  placeholder="Enter currency code" value="{{ old('currency_code', @$exchangeRate->currency_code) }}"
                  disabled />
              @else
                <select id="input_currency_code" class="form-control" name="settings[base_currency]">
                  @php $v = old('settings.base_currency', $settings['base_currency'] ?? 'USD'); @endphp
                  @foreach ($currencies as $currency)
                    @php
                      $symbol = $currency['symbol'] ?? null;
                      $code = $currency['code'] ?? '';
                    @endphp
                    <option value="{{ $code }}" {{ $v === $code ? 'selected' : '' }}>
                      {{ $symbol ? "($symbol) " : '' }}{{ $code }} / {{ $currency['name'] ?? '' }}
                    </option>
                  @endforeach
                </select>
              @endisset

              @include('alerts.feedback', ['field' => 'currency_code'])
            </div>
            <div class="form-group">
              <label for="input_rate" class="required">Rate</label>
              <input type="text" name="rate" class="form-control {{ $errors->has('rate') ? ' is-invalid' : '' }}"
                id="input_rate" placeholder="Enter rate" value="{{ old('rate', @$exchangeRate->rate) }}" required />
              @include('alerts.feedback', ['field' => 'rate'])
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ $indexLink }}" class="btn btn-light">Cancel</a>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
