@php use App\Constants\StatusConst; @endphp
@extends('mail.layout')

@section('content')
  @if ($order->status == StatusConst::PENDING)
    <h3>Hi, please complete your payment before {{ parse_date_full($order->expired_at) }}</h3>
  @elseif ($order->status == StatusConst::ON_PROCESS)
    <h3>Thank you for your purchase 😊</h3>
  @elseif ($order->status == StatusConst::SUCCESS)
    <h3>Your order has been successfully processed 🥳</h3>
  @elseif ($order->status == StatusConst::EXPIRED)
    <h3>Oops, your payment has expired :(</h3>
  @elseif ($order->status == StatusConst::FAILED)
    <h3>Oops, your payment has failed :(</h3>
  @endif

  @include('mail.layout-order')
@endsection

@section('cta')
  @if ($order->status == StatusConst::PENDING)
    <p><a href="{{ $order->payment_url_full }}" class="button" target="_blank">Pay Now</a></p>
  @endif
@endsection
