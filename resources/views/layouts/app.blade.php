<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Panel {{ get_setting('brand_name')['value'] }} {{ isset($title) ? ' - ' . $title : null }}</title>

    {{--  Script--}}
    <script src="{{ asset('js/vendor.bundle.base.js') }}"></script>

    <!-- Styles -->
    @stack('assets')
    <link href="{{ asset('vendors/mdi/css/materialdesignicons.min.css') }}" rel="stylesheet">
    @vite(['resources/css/style.css'])

    <!-- Select2 -->
    <link href="{{ asset('vendors/select2/select2.min.css') }}" rel="stylesheet">
    <script src="{{ asset('vendors/select2/select2.min.js') }}"></script>

    <!-- Shortcut icon -->
    <link rel="shortcut icon" href="{{ asset('/storage/'.get_setting('favicon')['value']) }}" />
</head>
<body>
    @auth
        @include('layouts.navbar')
        <div class="container-fluid page-body-wrapper" id="app">
            @include('layouts.sidebar')
            <div class="main-panel">
                 <div class="content-wrapper">
                     @yield('content')
                 </div>
            </div>
        </div>
    @else
        @yield('content')
    @endauth

    @include('sweetalert::alert', ['cdn' => "https://cdn.jsdelivr.net/npm/sweetalert2@9"])
    <script src="{{ asset('js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('js/misc.js') }}"></script>
    <script>
    $('select.form-control').select2({
      tags: true
    });
    $('form').submit(function(){
      $(this).find(':submit').attr('disabled','disabled');
    });
    </script>
    @stack('js')
</body>
</html>
