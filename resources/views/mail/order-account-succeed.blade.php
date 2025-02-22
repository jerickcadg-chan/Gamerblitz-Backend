<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Pembelian akun anda di {{$order->client->name}} berhasil #{{$order->code}}</title>
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
        <img src="{{ $order->client->logo }}" width="200px" alt="{{ $order->client->name }}">
        <div class="card">
          <h1>Ini detail akun yang sudah anda beli 🔥</h1>
          {!! decrypt($order->accounts->first()->information) !!}
        </div>
        <p>Email dibuat secara otomatis</p>
        <a href="{{$order->client->frontend_host}}" target="_blank">{{$order->client->frontend_host}}</a>
      </center>
    </body>
</html>

