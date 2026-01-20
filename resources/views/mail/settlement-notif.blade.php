@php use App\Constants\StatusConst; @endphp
@extends('mail.layout')

@section('content')
  <h3>There's paid manual orders 🔥</h3>

  @include('mail.layout-order')
@endsection
