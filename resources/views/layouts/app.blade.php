<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Panel {{ brand_name() }} {{ isset($title) ? ' - ' . $title : null }}</title>

    {{--  Script--}}
    <script src="{{ asset('js/vendor.bundle.base.js') }}"></script>

    <!-- Styles -->
    @stack('assets')
    <link href="{{ asset('vendors/mdi/css/materialdesignicons.min.css') }}" rel="stylesheet">

    <!-- Select2 -->
    <link href="{{ asset('vendors/select2/select2.min.css') }}" rel="stylesheet">
    <script src="{{ asset('vendors/select2/select2.min.js') }}"></script>

    <!-- Shortcut icon -->
    <link rel="shortcut icon" href="{{ get_favicon() }}" />

    @php
      $primary = \App\Models\Setting::getByKey('primary_color') ?? '#445264';
    @endphp

    <style>
      :root {
        --color-primary: {{ $primary }};
      }
    </style>

    @vite(['resources/css/style.css'])
    
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-TGVWKS9P');
    </script>
    <!-- End Google Tag Manager -->
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TGVWKS9P"
        height="0" width="0" style="display:none;visibility:hidden">
    </iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
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
    <script src="{{ asset('js/alphine.min.js') }}" defer></script>
    <script>
    $('select.form-control').select2({
      tags: true
    });
    $('form').submit(function(){
      $(this).find(':submit').attr('disabled','disabled');
    });
    document.addEventListener("DOMContentLoaded", () => {
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
      tooltipTriggerList.forEach((el) => {
        new bootstrap.Tooltip(el);
      });
    });
    </script>
    @stack('js')
    
</body>
</html>
