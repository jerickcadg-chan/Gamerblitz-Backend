<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>MitraGamers - Voucher Receipt</title>
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
            <img src="{{ config('array.store.url') }}/img/mitragamers-logo.png" width="200px" alt="mitragamers-logo">
            <div class="card">
                <h1>Terimakasih Atas Pembelian Anda 😊</h1>
                <table>
                    <tr>
                        <td>Kode Transaksi</td>
                        <td>{{ $order->code }}</td>
                    </tr>
                    <tr>
                        <td>Produk</td>
                        <td>{{ $order->productItem->name }} {{ $order->productItem->product->name }}</td>
                    </tr>
                    <tr>
                        <td>Nominal Transaksi</td>
                        <td>{{ rp_format($order->total_price) }}</td>
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
            <p>Apabila kamu membutuhkan bantuan silahkan <a href="https://mitragamers.com/contact" target="_blank">Hubungi Kami</a> </p>
            <a href="https://mitragamers.com" target="_blank">MitraGamers.com</a>
        </center>
    </body>
</html>
