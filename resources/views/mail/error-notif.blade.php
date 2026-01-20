@extends('mail.layout')

@section('content')
  <h1>There's an error with your order</h1>
  <h3>{{ $errMessage }}</h3>

  @include('mail.layout-order')
@endsection
