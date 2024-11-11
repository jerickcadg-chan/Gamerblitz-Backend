@extends('layouts.app', [
    'activePage' => 'user',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Halaman {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('user.index') }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Semua Data</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ $storeLink }}">
                    @csrf
                    <div class="form-group">
                        <label for="input_name" class="required">Nama</label>
                        <input type="text" name="name" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" id="input_name" placeholder="Masukkan nama" value="{{ old('name') }}" required>
                        @include('alerts.feedback', ['field' => 'name'])
                    </div>
                    <div class="form-group">
                        <label for="input_email" class="required">Email</label>
                        <input type="email" name="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" id="input_email" placeholder="Masukkan email" value="{{ old('email') }}" required>
                        @include('alerts.feedback', ['field' => 'email'])
                    </div>
                    <div class="form-group">
                        <label for="input_phone_number">No Handphone</label>
                        <input type="text" name="phone_number" class="form-control {{ $errors->has('phone_number') ? ' is-invalid' : '' }}" id="input_phone_number" placeholder="Masukkan no handphone" value="{{ old('phone_number') }}">
                        @include('alerts.feedback', ['field' => 'phone_number'])
                    </div>
                    <div class="form-group">
                        <label for="input_role_id" class="required">Role</label>
                        <select class="form-control select2 {{ $errors->has('role_id') ? ' is-invalid' : '' }}" name="role_id" id="input_role_id" required>
                            <option value="">Pilih role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" @if(old('role_id') == $role->id) selected @endif>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @include('alerts.feedback', ['field' => 'role_id'])
                    </div>
                    <div class="form-group">
                        <label for="input_password" class="required">Password</label>
                        <input type="password" name="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" id="input_password" placeholder="Masukkan password" required>
                        @include('alerts.feedback', ['field' => 'password'])
                    </div>
                    <div class="form-group">
                        <label for="input_password_confirmation" class="required">Ulangi Password</label>
                        <input type="password" name="password_confirmation" class="form-control {{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" id="input_password_confirmation" placeholder="Ulangi password yang dimasukkan" required>
                        @include('alerts.feedback', ['field' => 'password_confirmation'])
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ $indexLink }}" class="btn btn-light">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection
