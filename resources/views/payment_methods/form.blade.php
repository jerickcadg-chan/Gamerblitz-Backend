@extends('layouts.app', [
    'activePage' => 'payment_method',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('payment_method.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ isset($paymentMethod) ? 'Edit Data' : 'Create Data' }}</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data">
          @csrf
          @isset($paymentMethod) @method('PUT') @endif

          <div class="form-group">
            <label for="name_input" class="required">Name</label>
            <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}"
                   name="name" id="name_input" placeholder="Enter Name"
                   value="{{ old('name', $paymentMethod->name ?? '') }}" required>
            @include('alerts.feedback', ['field' => 'name'])
          </div>

          <div class="form-group">
            <label for="slug_input" class="required">Slug</label>
            <input type="text" class="form-control {{ $errors->has('slug') ? ' is-invalid' : '' }}"
                   name="slug" id="slug_input" placeholder="Unique slug (e.g.: bca-va, qris)"
                   value="{{ old('slug', $paymentMethod->slug ?? '') }}"
                   required
                   @if($paymentMethod->slug === \App\Models\PaymentMethod::BALANCE) readonly @endif>
            @include('alerts.feedback', ['field' => 'slug'])
          </div>

          <div class="form-group">
            <label for="admin_fee_input" class="required">Admin Fee</label>
            <input type="number" step="0.01" class="form-control {{ $errors->has('admin_fee') ? ' is-invalid' : '' }}"
                   name="admin_fee" id="admin_fee_input" placeholder="0"
                   value="{{ old('admin_fee', $paymentMethod->admin_fee ?? 0) }}" required>
            @include('alerts.feedback', ['field' => 'admin_fee'])
          </div>

          <div class="form-group">
            <label for="admin_type_input" class="required">Admin Type</label>
            <select class="form-control {{ $errors->has('admin_type') ? ' is-invalid' : '' }}" name="admin_type" id="admin_type_input" required>
              @foreach(['nominal' => 'Nominal', 'percentage' => 'Percentage', 'no-admin' => 'No Admin'] as $val => $label)
                <option value="{{ $val }}" {{ old('admin_type', $paymentMethod->admin_type ?? '') == $val ? 'selected' : '' }}>
                  {{ $label }}
                </option>
              @endforeach
            </select>
            @include('alerts.feedback', ['field' => 'admin_type'])
          </div>

          <div class="form-group">
            <label for="vendor_input" class="required">Vendor</label>
            <select class="form-control {{ $errors->has('vendor') ? ' is-invalid' : '' }}" name="vendor" id="vendor_input" required>
              @foreach(['xendit' => 'Xendit', 'paypal' => 'Paypal', 'manual' => 'Manual'] as $val => $label)
                <option value="{{ $val }}" {{ old('vendor', $paymentMethod->vendor ?? '') == $val ? 'selected' : '' }}>
                  {{ $label }}
                </option>
              @endforeach
            </select>
            @include('alerts.feedback', ['field' => 'vendor'])
          </div>

          <div class="form-group">
            <label for="account_number_input">Account Number (For Manual)</label>
            <input type="text" class="form-control {{ $errors->has('account_number') ? ' is-invalid' : '' }}"
                   name="account_number" id="account_number_input" placeholder="Account No. / E-Wallet No."
                   value="{{ old('account_number', $paymentMethod->account_number ?? '') }}">
            @include('alerts.feedback', ['field' => 'account_number'])
          </div>

          <div class="form-group">
            <label for="category_input" class="required">Category</label>
            <select name="category"  id="category_input" class="form-control {{ $errors->has('category') ? ' is-invalid' : '' }}" required>
              @foreach(\App\Constants\PaymentCategoryConstant::all() as $key => $label)
                <option value="{{ $label }}"
                  {{ old('category', $paymentMethod->category ?? '') == $label ? 'selected' : '' }}>
                  {{ $label }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="form-group">
            <label class="required">Status</label><br>
            <label class="me-3">
              <input type="radio" name="is_active" value="1" {{ old('is_active', $paymentMethod->is_active ?? 1) == 1 ? 'checked' : '' }}> Aktif
            </label>
            <label>
              <input type="radio" name="is_active" value="0" {{ old('is_active', $paymentMethod->is_active ?? 1) == 0 ? 'checked' : '' }}> Non Aktif
            </label>
            @include('alerts.feedback', ['field' => 'is_active'])
          </div>

          <div class="form-group">
            <label for="ordering_input">Ordering</label>
            <input type="number" class="form-control {{ $errors->has('ordering') ? ' is-invalid' : '' }}"
                   name="ordering" id="ordering_input"
                   value="{{ old('ordering', $paymentMethod->ordering ?? '') }}">
            @include('alerts.feedback', ['field' => 'ordering'])
          </div>

          <div class="form-group">
            <label class="required">Currency</label>
            <select name="currency_code" id="currency_code"
                    class="form-control {{ $errors->has('currency_code') ? 'is-invalid' : '' }}"
                    required>
              @php
                $v = old('currency_code', isset($paymentMethod) ? @$paymentMethod->currency_code : 'USD');
              @endphp
              @foreach(\App\Constants\CurrencyConstant::all() as $currency)
                @php
                  $symbol = $currency['symbol'] ?? null;
                  $code = $currency['code'] ?? '';
                @endphp
                <option value="{{ $code }}" {{ $v === $code ? 'selected' : '' }}>
                  {{ $symbol ? "($symbol) " : '' }}{{ $code }} / {{ $currency['name'] ?? '' }}
                </option>
              @endforeach
            </select>
            @include('alerts.feedback', ['field' => 'currency_codes'])
          </div>

          <div class="form-group">
            <label for="picture" class="required">Picture</label>
            <input type="file" name="default_picture"
                   class="form-control {{ $errors->has('default_picture') ? ' is-invalid' : '' }}" accept="image/*">
            @include('alerts.feedback', ['field' => 'default_picture'])
            @if (!empty($paymentMethod->picture))
              <p class="d-block text-small mt-2">Current</p>
              <a href="{{ asset($paymentMethod->picture) }}" target="_blank">
                <img src="{{ asset($paymentMethod->picture) }}" height="100" alt="image preview" />
              </a>
            @endif
            @include('alerts.feedback', ['field' => 'picture'])
          </div>

          <button type="submit" class="btn btn-primary">Submit</button>
          <a href="{{ route('payment_method.index') }}" class="btn btn-light">Cancel</a>
        </form>
      </div>
    </div>
  </div>
@endsection
