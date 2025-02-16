@extends('layouts.app', [
    'activePage' => 'payment_method',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Halaman {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('payment_method.index') }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Data</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ $storeLink }}" enctype="multipart/form-data">
                    @csrf
                    {{-- {{ dd($errors) }} --}}
                    <div class="form-group">
                        <label for="name_input">Nama Pemilik</label>
                        <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" id="name_input" placeholder="Masukkan Nama" value="{{ old('name') }}">
                        @include('alerts.feedback', ['field' => 'name'])
                    </div>
                    <div class="form-group">
                        <label for="bank_account_input">Nama Bank</label>
                        <input type="text" class="form-control {{ $errors->has('bank_account') ? ' is-invalid' : '' }}" name="bank_account" id="bank_account_input" placeholder="BCA, BNI, BRI" value="{{ old('bank_account') }}">
                        @include('alerts.feedback', ['field' => 'bank_account'])
                    </div>
                    <div class="form-group">
                        <label for="account_number_input">Nomor rekening</label>
                        <input type="text" class="form-control {{ $errors->has('account_number') ? ' is-invalid' : '' }}" name="account_number" id="account_number_input" placeholder="Masukkan Nama" value="{{ old('account_number') }}">
                        @include('alerts.feedback', ['field' => 'account_number'])
                    </div>
                    <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                    <a href="{{ $indexLink }}" class="btn btn-light">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection

