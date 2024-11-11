@extends('layouts.app', ['title' => 'Login'])

@section('content')
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth">
                <div class="row flex-grow">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left p-5">
                            <div class="text-center">
                                <img src="{{ asset('img/header.png') }}" alt="header image kayestore" class="w-75 text-center">
                            </div>
                            <form class="pt-3" action="{{ route('login') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Email" required autocomplete="email" autofocus>
                                </div>
                                <div class="form-group">
                                    <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" placeholder="Password" required autocomplete="current-password">
                                </div>
                                @error('email')
                                    <span class="text-danger" role="alert">
                                        <p>{{ $message }}</p>
                                    </span>
                                @enderror
                                <div class="mt-3">
                                    <button type="submit" class="btn w-100 btn-danger btn-lg font-weight-medium auth-form-btn" href="../../index.html">SIGN IN</button>
                                </div>
                                <div class="my-4">
                                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
@endsection
