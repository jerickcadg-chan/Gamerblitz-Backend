@extends('layouts.app', ['title' => $title ?? '2FA'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Two-Factor Authentication</h4>
                </div>
                <div class="card-body">
                    @if(Auth::user()->google2fa_secret)
                        <p>2FA is enabled for your account.</p>
                        <form action="{{ route('2fa.disable') }}" method="POST">
                            @csrf
                            @method('POST')
                            <button type="submit" class="btn btn-danger">Disable 2FA</button>
                        </form>
                    @else
                        <p>2FA is not enabled. Scan the QR code with your authenticator app and enter the code below to enable.</p>
                        <p>Secret Key: <code>{{ $secret }}</code></p>
                        <p>Scan this QR code with your authenticator app:</p>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}" alt="QR Code">
                        <form action="{{ route('2fa.enable') }}" method="POST" class="mt-4">
                            @csrf
                            <input type="hidden" name="secret" value="{{ $secret }}">
                            <div class="form-group">
                                <label for="one_time_password">Enter Code from App</label>
                                <input type="text" class="form-control" name="one_time_password" required>
                                @error('one_time_password')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Enable 2FA</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
