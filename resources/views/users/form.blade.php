@extends('layouts.app', [
    'activePage' => 'user',
])

@php
    $isEdit = isset($user);
@endphp

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('user.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $isEdit ? "Edit" : "Add" }} Data</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $actionLink }}">
          @csrf
          @isset($user) @method('PUT')@endisset
          <div class="form-group">
            <label for="input_name" class="required">Name</label>
            <input type="text" name="name" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" id="input_name" placeholder="Enter name" value="{{ old('name', @$user->name) }}" required>
            @include('alerts.feedback', ['field' => 'name'])
          </div>
          <div class="form-group">
            <label for="input_email" class="required">Email</label>
            <input type="email" name="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" id="input_email" placeholder="Enter email" value="{{ old('email', @$user->email) }}" required>
            @include('alerts.feedback', ['field' => 'email'])
          </div>
          <div class="form-group">
            <label for="input_phone_number">Phone Number</label>
            <input type="text" name="phone_number" class="form-control {{ $errors->has('phone_number') ? ' is-invalid' : '' }}" id="input_phone_number" placeholder="Enter phone number" value="{{ old('phone_number', @$user->phone_number) }}">
            @include('alerts.feedback', ['field' => 'phone_number'])
          </div>
          <div class="form-group">
            <label for="input_role_id" class="required">Role {{ $user?->role ?? null }}</label>
            <select class="form-control select2 {{ $errors->has('role_id') ? ' is-invalid' : '' }}" name="role_id" id="input_role_id" required>
              <option value="">Select role</option>
              @foreach($roles as $role)
                <option value="{{ $role->name }}" @if(old('role_name', $role->name) == ($user?->role ?? null)) selected @endif>{{ $role->name }}</option>
              @endforeach
            </select>
            @include('alerts.feedback', ['field' => 'role_id'])
          </div>
          <div class="form-group">
            <label for="input_password">Password</label>
            <input type="password" name="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" id="input_password" placeholder="Empty if don't want to change password">
            @include('alerts.feedback', ['field' => 'password'])
          </div>
          <div class="form-group">
            <label for="input_password_confirmation">Repeat Password</label>
            <input type="password" name="password_confirmation" class="form-control {{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" id="input_password_confirmation" placeholder="Repeat password entered">
            @include('alerts.feedback', ['field' => 'password_confirmation'])
          </div>
          <div class="form-group form-check">
            <label for="affiliate_on">Enable Affiliate</label>
            <input type="checkbox" class="form-check mt-1" id="affiliate_on" name="affiliate_on" value="1" {{ old('affiliate_on', @$user->affiliate->status) ? 'checked' : ''
}}>
          </div>
          <button type="submit" class="btn btn-primary">Submit</button>
          <a href="{{ $indexLink }}" class="btn btn-light">Cancel</a>
        </form>
      </div>
    </div>
  </div>
@endsection
