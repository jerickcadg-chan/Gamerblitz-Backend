<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>{{ $user->client->name }} - Link Verifikasi</title>
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
            <img src="{{$user->client->logo}}" width="200px" alt="mitragamers-logo">
            <div class="card">
                <h1>Email verifikasi</h1>
                <p style="margin: 0px">Klik link dibawah ini untuk verifikasi email anda</p>
                <a href="{{ $url }}" target="_blank">Link verifikasi</a>
            </div>
            <p>Email dibuat secara otomatis</p>
            <a href="{{$user->client->host}}" target="_blank">{{$user->client->host}}</a>
        </center>
    </body>
</html>

