<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
<head>

    <meta charset="utf-8">
    <!-- Chrome for Android theme color -->
    <meta name="theme-color" content="{{ config('cms.setting.theme_color') }}"/>
    <meta name="title" content="{!! isset($data['meta_title']) ? strip_tags($data['meta_title']) : strip_tags(config('cmsConfig.seo.meta_title')) !!}">
    <meta name="description" content="{!! isset($data['meta_description']) ? strip_tags($data['meta_description']) : strip_tags(config('cmsConfig.seo.meta_description')) !!}">
    <meta name="keywords" content="{!! isset($data['meta_keywords']) ? strip_tags($data['meta_keywords']) : strip_tags(config('cmsConfig.seo.meta_keywords')) !!}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title.' | ' : '' }} @yield('title') {{ strip_tags(config('cmsConfig.seo.meta_title')) }}</title>

    <meta name="robots" content="index,follow" />
    <meta name="googlebot" content="index,follow" />
    <meta name="revisit-after" content="2 days" />
    <meta name="author" content="4 Vision Media">
    <meta name="expires" content="never" />

    <meta name="google-site-verification" content="{!! config('cmsConfig.seo.google_verification') !!}" />
    <meta name="p:domain_verify" content="{!! config('cmsConfig.seo.domain_verification') !!}"/>

    <!-- Open Graph -->
    <meta property="og:locale" content="{{ config('app.faker_locale') }}" />
    <meta property="og:site_name" content="{{ route('home') }}">
    <meta property="og:title" content="{!! isset($data['meta_title']) ? strip_tags($data['meta_title']) : strip_tags(config('cmsConfig.seo.meta_title')) !!}"/>
    <meta property="og:url" name="url" content="{{ url()->full() }}">
    <meta property="og:description" content="{!! isset($data['meta_description']) ? $data['meta_description'] : config('cmsConfig.seo.meta_description') !!}"/>
    <meta property="og:image" content="{!! isset($data['cover']) ? asset($data['cover']) : config('cmsConfig.file.open_graph') !!}"/>
    <meta property="og:type" content="website" />

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{!! isset($data['meta_title']) ? strip_tags($data['meta_title']) : strip_tags(config('cmsConfig.seo.meta_title')) !!}">
    <meta name="twitter:site" content="{{ url()->full() }}">
    <meta name="twitter:creator" content="{!! isset($data['creator']) ? $data['creator'] : 'Administrator Web' !!}">
    <meta name="twitter:description" content="{!! isset($data['meta_description']) ? strip_tags($data['meta_description']) : strip_tags(config('cmsConfig.seo.meta_description')) !!}">
    <meta name="twitter:image" content="{!! isset($data['cover']) ? asset($data['cover']) : config('cmsConfig.file.open_graph') !!}">

    <link rel="canonical" href="{{ url()->full() }}" />
    
    <!-- Icon -->
    @include('layouts.frontend.assets.icon')

    <!-- Css -->
    @include('layouts.frontend.assets.css')
    
    <!-- jsHead -->
    @yield('jshead')

    @if (config('cmsConfig.dev.pwa') == true)
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

    {!! config('cmsConfig.seo.google_analytics') !!}
</head>
    <body @yield('body-attr')>

        @yield('layout-content')

        <!-- jS -->
        @include('layouts.frontend.assets.js')

        <!-- Fonts -->
        @include('layouts.frontend.assets.font')

        <!-- jsbody-->
        @yield('jsbody')

    </body>
</html>
