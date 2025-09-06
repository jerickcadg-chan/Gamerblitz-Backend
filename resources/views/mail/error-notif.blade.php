@php
  $brandName = \App\Models\Setting::getByKey('brand_name') ;
  $brandLogo = \App\Models\Setting::getByKey('logo');
@endphp

  <!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <title>{{ $brandName }} - Settlement Notif</title>
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
  </style>
</head>
<body>
<center>
  <img src="{{ $brandLogo }}" width="200px" alt="{{ $brandName }}">
  <div class="card">
    <h1>There's errors order</h1>
    <h3>{{ $errMessage }}</h3>
    <table>
      <tr>
        <td>Invoice Code</td>
        <td><a href="{{ route('order.show', $order->id) }}" target="_blank">{{ $order->code }}</a></td>
      </tr>
      <tr>
        <td>Item Order</td>
        <td>{{ $order->productItem->name }} {{ $order->productItem->product->name }}</td>
      </tr>
      <tr>
        <td>Total Price</td>
        <td>{{ currency_format($order->total_price) }}</td>
      </tr>
      <tr>
        <th>Customer Account</th>
        <td>{!! $order->cust_account_format !!}</td>
      </tr>
      <tr>
        <td>Status</td>
        <td>{{ $order->payment_status_translated }}</td>
      </tr>
      <tr>
        <td>Paid Date</td>
        <td>{{ parse_date_time($order->settlement_date) }}</td>
      </tr>
    </table>
  </div>
  <p>Email created automatically</p>
  <a href="{{ config('app.frontend_url') }}" target="_blank">{{ config('app.frontend_url') }}</a>
</center>
</body>
</html>
