<!-- resources/views/emails/layout.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>{{ $title ?? brand_name() }}</title>
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
    @yield('content')
  </div>
  @yield('cta')
  <p>This email was generated automatically</p>
  <a href="{{ config('app.fe_url') }}" target="_blank">{{ config('app.fe_url') }}</a>
</center>
</body>
</html>
