@extends('layouts.app', ['title' => 'Login'])

@section('content')
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth">
        <div class="row flex-grow">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left p-5">
              <div class="text-center">
                <img src="{{ get_logo() }}" alt="header image" class="w-75 text-center">
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
                <button type="submit" class="btn w-100 btn-primary btn-lg font-weight-medium auth-form-btn">SIGN IN</button>
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

     <!-- 2FA Modal -->
     @if(session('show_2fa_modal'))
     <div class="modal fade show" id="twofaModal" tabindex="-1" role="dialog" aria-labelledby="twofaModalLabel" aria-hidden="false" style="display: block;">
       <div class="modal-dialog" role="document">
         <div class="modal-content">
           <div class="modal-header">
             <h5 class="modal-title" id="twofaModalLabel">Enter 2FA Code</h5>
           </div>
           <form action="{{ route('login.verify2fa') }}" method="POST">
             @csrf
             <div class="modal-body">
               <div class="form-group">
                 <input id="one_time_password" type="text" class="form-control @error('one_time_password') is-invalid @enderror" name="one_time_password" placeholder="Google Authenticator Code" autocomplete="one-time-code" required autofocus>
                 @error('one_time_password')
                 <span class="text-danger">{{ $message }}</span>
                 @enderror
               </div>
             </div>
             <div class="modal-footer">
               <button type="submit" class="btn btn-primary">Verify</button>
             </div>
           </form>
         </div>
       </div>
     </div>
      <div class="modal-backdrop fade show"></div>
      @endif
      @if(session('show_2fa_modal'))
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          document.getElementById('one_time_password').focus();
        });
      </script>
      @endif
  @endsection
