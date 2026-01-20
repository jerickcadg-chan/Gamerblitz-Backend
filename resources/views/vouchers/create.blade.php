@extends('layouts.app', [
    'activePage' => 'voucher',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Page {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('voucher.index', ['product_item_id' => request('product_item_id')]) }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create Data</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ $storeLink }}">
                    @csrf
                    <div class="form-group">
                        <label for="input_serial_number">Serial Number</label>
                        <input type="text" name="serial_number" class="form-control {{ $errors->has('serial_number') ? ' is-invalid' : '' }}" id="input_serial_number" placeholder="Enter serial number" value="{{ old('serial_number') }}" required>
                        @include('alerts.feedback', ['field' => 'serial_number'])
                    </div>
                    <div class="form-group">
                        <label for="input_password">Password</label>
                        <input type="text" name="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" id="input_password" placeholder="Enter password" value="{{ old('password') }}" required>
                        @include('alerts.feedback', ['field' => 'password'])
                    </div>
                    <div class="form-group">
                        <label for="input_capital">Modal</label>
                        <input type="number" name="capital" class="form-control {{ $errors->has('capital') ? ' is-invalid' : '' }}" id="input_capital" placeholder="Enter capital" value="{{ old('capital') }}" required>
                        @include('alerts.feedback', ['field' => 'capital'])
                    </div>
                    <div class="form-group">
                        <label for="input_vendor">Vendor</label>
                        <input type="text" name="vendor" class="form-control {{ $errors->has('vendor') ? ' is-invalid' : '' }}" id="input_vendor" placeholder="Enter name" value="{{ old('vendor') }}" required>
                        @include('alerts.feedback', ['field' => 'vendor'])
                    </div>
                    <input type="hidden" name="product_item_id" value="{{ request('product_item_id') }}">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ $indexLink }}" class="btn btn-light">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection
