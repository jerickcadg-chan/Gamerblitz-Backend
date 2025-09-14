<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <title>{{ $order->client->name }} - Voucher Receipt</title>
  <style>
    body {
      background: #000;
      font-family: 'Arial';
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
  <img src="{{ $order->client->logo }}" width="200px" alt="{{ $order->client->name }}">
  <div class="card">
    <h1>Terimakasih Atas Pembelian Anda 😊</h1>
    <table>
      <tr>
        <td>Transaction Code</td>
        <td>{{ $order->code }}</td>
      </tr>
      <tr>
        <td>Product</td>
        <td>{{ $order->productItem->name }} {{ $order->productItem->product->name }}</td>
      </tr>
      <tr>
        <td>Amount</td>
        <td>{{ currency_format($order->total_price) }}</td>
      </tr>
      <tr>
        <td><b>Voucher</b></td>
        <td>
          <p style="margin: 0px"><b>Serial</b> : {{ @$voucher->serial_number }}</p>
          <p><b>PIN</b> : {{ @\Illuminate\Support\Facades\Crypt::decryptString($voucher->password) }}</p>
        </td>
      </tr>
    </table>
  </div>
  <p>Email dibuat secara otomatis</p>
  <a href="{{ config('app.fe_url') }}" target="_blank">{{ config('app.fe_url') }}</a>
</center>
</body>
</html>
