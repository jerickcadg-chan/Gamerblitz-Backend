<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>MitraGamers - Error Notif</title>
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
            <img src="{{ config('array.store.url') }}/img/kayestore-logo.png" width="200px" alt="kayestore-logo">
            <div class="card">
                <h1>Ada Transaksi Error 🙈</h1>
                <h3>{{ $errMessage }}</h3>
                <table>
                    <tr>
                        <td>Kode Transaksi</td>
                        <td><a href="{{ route('order.show', $order->id) }}" target="_blank">{{ $order->code }}</a></td>
                    </tr>
                    <tr>
                        <td>Item Order</td>
                        <td>{{ $order->productItem->name }} {{ $order->productItem->product->name }}</td>
                    </tr>
                    <tr>
                        <td>Nominal Transaksi</td>
                        <td>{{ rp_format($order->total_price) }}</td>
                    </tr>
                    <tr>
                        <th>Informasi Akun</th>
                        <td>{!! $order->cust_account_format !!}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>{{ $order->payment_status_translated }}</td>
                    </tr>
                </table>
            </div>
            <p>Email dibuat secara otomatis</p>
            <a href="https://kayestore.com" target="_blank">MitraGamers.com</a>
        </center>
    </body>
</html>
