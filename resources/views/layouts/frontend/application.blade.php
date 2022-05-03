<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
<head>

    <meta charset="utf-8">
    <meta name="theme-color" content="#0084ff"/>
    <meta name="title" content="{!! isset($data['meta_title']) ? strip_tags($data['meta_title']) : strip_tags($config['meta_title']) !!}">
    <meta name="description" content="{!! isset($data['meta_description']) ? strip_tags($data['meta_description']) : strip_tags($config['meta_description']) !!}">
    <meta name="keywords" content="{!! isset($data['meta_keywords']) ? strip_tags($data['meta_keywords']) : strip_tags($config['meta_keywords']) !!}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title.' | ' : '' }} @yield('title') {{ strip_tags($config['meta_title']) }}</title>

    <meta name="robots" content="index,follow" />
    <meta name="googlebot" content="index,follow" />
    <meta name="revisit-after" content="2 days" />
    <meta name="author" content="4 Vision Media">
    <meta name="expires" content="never" />

    <meta name="google-site-verification" content="{!! $config['google_verification'] !!}" />
    <meta name="p:domain_verify" content="{!! $config['domain_verification'] !!}"/>

    <meta property="og:locale" content="{{ App::getlocale().'_'.strtoupper(App::getlocale()) }}" />
    <meta property="og:site_name" content="{{ route('home') }}">
    <meta property="og:title" content="{!! isset($data['meta_title']) ? strip_tags($data['meta_title']) : strip_tags($config['meta_title']) !!}"/>
    <meta property="og:url" name="url" content="{{ url()->full() }}">
    <meta property="og:description" content="{!! isset($data['meta_description']) ? $data['meta_description'] : $config['meta_description'] !!}"/>
    <meta property="og:image" content="{!! isset($data['cover']) ? asset($data['cover']) : $config['open_graph'] !!}"/>
    <meta property="og:type" content="website" />

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{!! isset($data['meta_title']) ? strip_tags($data['meta_title']) : strip_tags($config['meta_title']) !!}">
    <meta name="twitter:site" content="{{ url()->full() }}">
    <meta name="twitter:creator" content="{!! isset($data['creator']) ? $data['creator'] : 'Administrator Web' !!}">
    <meta name="twitter:description" content="{!! isset($data['meta_description']) ? strip_tags($data['meta_description']) : strip_tags($config['meta_description']) !!}">
    <meta name="twitter:image" content="{!! isset($data['cover']) ? asset($data['cover']) : $config['open_graph'] !!}">

    <link rel="canonical" href="{{ url()->full() }}" />

    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('assets/favicon/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('assets/favicon/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('assets/favicon/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/favicon/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('assets/favicon/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('assets/favicon/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('assets/favicon/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('assets/favicon/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/favicon/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192"  href="{{ asset('assets/favicon/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('assets/favicon/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/favicon/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#0084ff">
    <meta name="msapplication-TileImage" content="{{ asset('assets/favicon/ms-icon-144x144.png') }}">

    <!-- Fonts -->

    <!-- Css Global -->
    
    <!-- Css Additional -->
    @yield('styles')
    
    <!-- jQuery header -->
    @yield('jshead')


    {!! $config['google_analytics'] !!}
</head>
    <body @yield('body-attr')>

        @yield('layout-content')

        <!-- jQuery.min.js -->

        <!-- jQuery Global-->

        <!-- jQuery addtional-->
        @yield('scripts')

        <!-- jsbody-->
        @yield('jsbody')

    </body>
</html>
