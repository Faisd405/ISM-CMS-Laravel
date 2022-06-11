<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
<head>

    <meta charset="utf-8">
    <!-- Chrome for Android theme color -->
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

    <!-- Open Graph -->
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
    
    <!-- Web Application Manifest -->
    <link rel="manifest" href="{{ asset('assets/favicon/manifest.json') }}">

    @if ($config['pwa'] == true)
    <!-- Add to homescreen for Chrome on Android -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="{!! $config['website_name'] !!}">
    <link rel="icon" sizes="512x512" href="{{ asset('assets/favicon/pwa/icon-512x512.png') }}">

    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="#0084ff">
    <meta name="apple-mobile-web-app-title" content="{!! $config['website_name'] !!}">
    <link rel="apple-touch-icon" href="{{ asset('assets/favicon/pwa/icon-512x512.png') }}">

    <link href="{{ asset('assets/favicon/pwa/splash-640x1136.png') }}" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
    <link href="{{ asset('assets/favicon/pwa/splash-750x1334.png') }}" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
    <link href="{{ asset('assets/favicon/pwa/splash-1242x2208.png') }}" media="(device-width: 621px) and (device-height: 1104px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
    <link href="{{ asset('assets/favicon/pwa/splash-1125x2436.png') }}" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
    <link href="{{ asset('assets/favicon/pwa/splash-828x1792.png') }}" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
    <link href="{{ asset('assets/favicon/pwa/splash-1242x2688.png') }}" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
    <link href="{{ asset('assets/favicon/pwa/splash-1536x2048.png') }}" media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
    <link href="{{ asset('assets/favicon/pwa/splash-1668x2224.png') }}" media="(device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
    <link href="{{ asset('assets/favicon/pwa/splash-1668x2388.png') }}" media="(device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
    <link href="{{ asset('assets/favicon/pwa/splash-2048x2732.png') }}" media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
    @endif

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

    <!-- Tile for Win8 -->
    <meta name="msapplication-TileImage" content="{{ asset('assets/favicon/ms-icon-144x144.png') }}">
    <meta name="msapplication-TileColor" content="#0084ff">

    <!-- Fonts -->

    <!-- Css Global -->
    
    <!-- Css Additional -->
    @yield('styles')
    
    <!-- jQuery header -->
    @yield('jshead')

    @if ($config['pwa'] == true)
    <!-- PWA -->
    <script type="text/javascript">
        // Initialize the service worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/serviceworker.js', {
                scope: '.'
            }).then(function (registration) {
                // Registration was successful
                // console.log('Laravel PWA: ServiceWorker registration successful with scope: ', registration.scope);
            }, function (err) {
                // registration failed :(
                // console.log('Laravel PWA: ServiceWorker registration failed: ', err);
            });
        }
    </script>
    @endif

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
