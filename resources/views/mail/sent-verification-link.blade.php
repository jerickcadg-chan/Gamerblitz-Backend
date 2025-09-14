@extends('mail.layout')

@section('content')
  <h1>Verification Link</h1>
  <p>Click this link to verify your account</p>
  <a href="{{ $url }}"
    target="_blank"
    class="btn">Click Here
  </a>
@endsection
