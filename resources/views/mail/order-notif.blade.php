<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <title>{{ brand_name() }} - Order Invoice</title>
  <style>
    body {
      background: #000;
      font-family: 'Arial', sans-serif;
      color: #000;
    }
    .card {
      background: #fff;
      width: 85%;
      padding: 20px;
    }
    table tr td {
      padding: 15px;
    }
    .button {
      background-color: #FF6363;
      border: none;
      color: #fff !important;
      padding: 20px 15px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 16px;
      margin: 4px 2px;
      cursor: pointer;
      border-radius: 15px;
    }
  </style>
</head>
<body>
<center>
  <img src="{{ get_logo() }}" width="200px" alt="logo">
  <div class="card">
    @if ($order->payment_status == \App\Constants\StatusConst::PENDING)
      <h3>Hi, please complete your payment before {{ parse_date_full($order->expired_at) }}</h3>
    @endif
    @if ($order->payment_status == \App\Constants\StatusConst::ON_PROCESS)
      <h3>Thank you for your purchase 😊</h3>
    @endif
    @if ($order->payment_status == \App\Constants\StatusConst::SUCCESS)
      <h3>Your order has been successfully processed 🥳</h3>
    @endif
    @if ($order->order_status == \App\Constants\StatusConst::EXPIRED)
      <h3>Oops, your payment has expired :(</h3>
    @endif
    @if ($order->order_status == \App\Constants\StatusConst::FAILED)
      <h3>Oops, your payment has failed :(</h3>
    @endif
    <table>
      <tr>
        <td>Transaction Code</td>
        <td>{{ $order->code }}</td>
      </tr>
      <tr>
        <td>Account Information</td>
        <td>{!! $order->cust_account_format !!}</td>
      </tr>
      <tr>
        <td>Product</td>
        <td>{{ $order->productItem->name }} {{ $order->productItem->product->name }}</td>
      </tr>
      <tr>
        <td>Transaction Amount</td>
        <td>{{ currency_format($order->total_price) }}</td>
      </tr>
      <tr>
        <td>Order Status</td>
        <td>{{ $order->status }}</td>
      </tr>
      @if($order->note)
        <tr>
          <td>Note</td>
          <td>{{ $order->note }}</td>
        </tr>
      @endif
    </table>
  </div>
  @if ($order->payment_status == \App\Constants\StatusConst::PENDING)
    <p><a href="{{ $order->payment_url_full }}" class="button" target="_blank">Pay Now</a></p>
  @endif
  <p>This email was generated automatically</p>
  <a href="{{ config('app.fe_url') }}" target="_blank">{{ config('app.fe_url') }}</a>
</center>
</body>
</html>
