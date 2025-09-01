<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>{{ get_setting('brand_name')['value'] }} - Verification Link</title>
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
            .btn {
              padding: 10px 12px;
              background-color: #000000;
              color: white;
              margin-top: 10px;
              border-radius: 5px;
              text-decoration: none;
            }
        </style>
    </head>
    <body>
        <center>
            <img src="{{ asset('/storage/'.get_setting('logo')['value']) }}" width="200px" alt="{{ get_setting('brand_name')['value'] }}">
            <div class="card">
                <h1>Verification Link</h1>
                <p>Click this link to verify your account</p>
                <a href="{{ $url }}"
                   target="_blank"
                   class="btn">Click Here</a>
            </div>
            <p>Automatic email</p>
            <a href="{{ config('app.fe_url') }}" target="_blank">{{ config('app.fe_url') }}</a>
        </center>
    </body>
</html>

