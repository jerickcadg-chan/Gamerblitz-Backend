@extends('layouts.app', [
    'activePage' => 'payment_method',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Halaman {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('payment_method.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ isset($paymentMethod) ? 'Edit Data' : 'Tambah Data' }}</li>
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
                   name="name" id="name_input" placeholder="Masukkan Nama"
                   value="{{ old('name', $paymentMethod->name ?? '') }}" required>
            @include('alerts.feedback', ['field' => 'name'])
          </div>

          <div class="form-group">
            <label for="slug_input" class="required">Slug</label>
            <input type="text" class="form-control {{ $errors->has('slug') ? ' is-invalid' : '' }}"
                   name="slug" id="slug_input" placeholder="slug unik (contoh: bca-va, qris)"
                   value="{{ old('slug', $paymentMethod->slug ?? '') }}" required>
            @include('alerts.feedback', ['field' => 'slug'])
          </div>

          <div class="form-group">
            <label for="account_name_input">Account Name</label>
            <input type="text" class="form-control {{ $errors->has('account_name') ? ' is-invalid' : '' }}"
                   name="account_name" id="account_name_input" placeholder="Nama bank / e-wallet"
                   value="{{ old('account_name', $paymentMethod->account_name ?? '') }}">
            @include('alerts.feedback', ['field' => 'account_name'])
          </div>

          <div class="form-group">
            <label for="account_number_input">Account Number</label>
            <input type="text" class="form-control {{ $errors->has('account_number') ? ' is-invalid' : '' }}"
                   name="account_number" id="account_number_input" placeholder="Nomor rekening / no e-wallet"
                   value="{{ old('account_number', $paymentMethod->account_number ?? '') }}">
            @include('alerts.feedback', ['field' => 'account_number'])
          </div>

          <div class="form-group">
            <label for="account_holder_name_input">Account Holder Name</label>
            <input type="text" class="form-control {{ $errors->has('account_holder_name') ? ' is-invalid' : '' }}"
                   name="account_holder_name" id="account_holder_name_input" placeholder="Nama pemilik rekening"
                   value="{{ old('account_holder_name', $paymentMethod->account_holder_name ?? '') }}">
            @include('alerts.feedback', ['field' => 'account_holder_name'])
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
            <label for="vendor_input" class="required">Admin Type</label>
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
            <label for="category_input" class="required">Category</label>
            <input type="text" class="form-control {{ $errors->has('category') ? ' is-invalid' : '' }}"
                   name="category" id="category_input" placeholder="E-Wallet, VA, Retail"
                   value="{{ old('category', $paymentMethod->category ?? '') }}" required>
            @include('alerts.feedback', ['field' => 'category'])
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
                   name="ordering" id="ordering_input" placeholder="Urutan tampil"
                   value="{{ old('ordering', $paymentMethod->ordering ?? '') }}">
            @include('alerts.feedback', ['field' => 'ordering'])
          </div>

          <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
          <a href="{{ route('payment_method.index') }}" class="btn btn-light">Cancel</a>
        </form>
      </div>
    </div>
  </div>
@endsection
