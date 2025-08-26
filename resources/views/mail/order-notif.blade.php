<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>{{$order->client->name}} - Order Invoice</title>
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
            <img src="{{ $order->client->logo }}" width="200px" alt="{{ $order->client->name }}">
            <div class="card">
                @if ($order->payment_status == \App\Models\Order::PENDING && $order->order_status != \App\Models\Order::EXPIRED)
                    <h3>Hi, Silahkan selesaikan pembayaran anda sebelum {{ parse_date_full($order->expired_at) }}</h3>
                @endif
                @if ($order->payment_status == \App\Models\Order::SETTLEMENT && $order->order_status == \App\Models\Order::INPROCESS)
                    <h3>Terimakasih Atas Pembelian Anda 😊</h3>
                @endif
                @if ($order->payment_status == \App\Models\Order::SETTLEMENT && $order->order_status == \App\Models\Order::DONE)
                    <h3>Order Anda Telah Selesai Kami Proses 🥳</h3>
                @endif
                @if ($order->order_status == \App\Models\Order::EXPIRED)
                    <h3>Yaahhh, pembayaran anda telah kadaluarsa :(</h3>
                @endif
                @if ($order->order_status == \App\Models\Order::CANCELED)
                    <h3>Yaahhh, pembayaran anda telah gagal :(</h3>
                @endif
                <table>
                    <tr>
                        <td>Kode Transaksi</td>
                        <td>{{ $order->code }}</td>
                    </tr>
                    <tr>
                        <td>Informasi Akun</td>
                        <td>{!! $order->cust_account_format !!}</td>
                    </tr>
                    <tr>
                        <td>Product</td>
                        <td>{{ $order->productItem->name }} {{ $order->productItem->product->name }}</td>
                    </tr>
                    <tr>
                        <td>Nominal Transaksi</td>
                        <td>{{ rp_format($order->total_price) }}</td>
                    </tr>
                    <tr>
                        <td>Status Pembayaran</td>
                        <td>{{ $order->payment_status_translated }}</td>
                    </tr>
                    <tr>
                        <td>Status Transaksi</td>
                        <td>{{ $order->order_status_translated }}</td>
                    </tr>
                    @if($order->note)
                        <tr>
                            <td>Catatan</td>
                            <td>{{ $order->note }}</td>
                        </tr>
                    @endif
                </table>
            </div>
            @if ($order->payment_status == \App\Models\Order::PENDING && $order->order_status != \App\Models\Order::EXPIRED)
                <p><a href="{{ $order->payment_url_full }}" class="button" target="_blank">Bayar Sekarang</a></p>
            @endif
            <p>Email dibuat secara otomatis</p>
            <a href="{{$order->client->frontend_host}}" target="_blank">{{$order->client->frontend_host}}</a>
        </center>
    </body>
</html>
