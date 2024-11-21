<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} {{ isset($title) ? ' - ' . $title : null }}</title>

    <!-- Scripts -->
    <script src="{{ asset('build/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('build/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('build/js/misc.js') }}"></script>

    <!-- Styles -->
    <link href="{{ asset('build/vendors/mdi/css/materialdesignicons.min.css') }}" rel="stylesheet">
    @vite(['resources/js/app.js', 'resources/css/style.css'])

    <!-- Select2 -->
    <link href="{{ asset('build/vendors/select2/select2.min.css') }}" rel="stylesheet">
    <script src="{{ asset('build/vendors/select2/select2.min.js') }}"></script>

    <!-- Shortcut icon -->
    <link rel="shortcut icon" href="{{ asset('build/img/favicon.ico') }}" />
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

    @stack('js')
    @include('sweetalert::alert')
    <script>
        $('.select2').select2();
        $('form').submit(function(){
            $(this).find(':submit').attr('disabled','disabled');
        });
    </script>
</body>
</html>
