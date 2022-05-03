<!DOCTYPE html>

<html lang="{{ app()->getLocale() }}" class="layout-fixed default-style layout-collapsed">

<head>

    <!-- Meta default -->
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge,chrome=1">
    <meta name="title" content="{{ isset($title) ? $title : env('APP_NAME') }}">
    <meta name="description" content="website error">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/backend/images/favicon.ico') }}" sizes="32x32">
    <!-- Main font -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900" rel="stylesheet">

    <!-- Icon fonts -->
    <link rel="stylesheet" href="{{ asset('assets/backend/vendor/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/vendor/fonts/ionicons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/vendor/fonts/linearicons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/vendor/fonts/open-iconic.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/vendor/fonts/pe-icon-7-stroke.css') }}">

    <!-- Core stylesheets -->
    <link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css">
    <link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/rtl/appwork.css') }}" class="theme-settings-appwork-css">
    <link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/rtl/theme-corporate.css') }}" class="theme-settings-theme-css">
    <link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/rtl/colors.css') }}" class="theme-settings-colors-css">
    <link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/rtl/uikit.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/css/demo.css') }}">

    <!-- Load polyfills -->
    <script src="{{ asset('assets/backend/vendor/js/polyfills.js') }}"></script>
    <script>document['documentMode']===10&&document.write('<script src="https://polyfill.io/v3/polyfill.min.js?features=Intl.~locale.en"><\/script>')</script>

    <!-- Layout helpers -->
    <script src="{{ asset('assets/backend/vendor/js/layout-helpers.js') }}"></script>

    <!-- Core scripts -->
    <script src="{{ asset('assets/backend/vendor/js/pace.js') }}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <!-- `perfect-scrollbar` library required by SideNav plugin -->
    <link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">
    @yield('styles')

</head>

<body class="bg-primary">

    @yield('layout-content')

    <!-- Core scripts -->
    <script src="{{ asset('assets/backend/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/backend/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/backend/vendor/js/sidenav.js') }}"></script>

    <!-- Libs -->

    <!-- `perfect-scrollbar` library required by SideNav plugin -->
    <script src="{{ asset('assets/backend/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    @yield('scripts')
    <script src="{{ asset('assets/backend/js/demo.js') }}"></script>
    @yield('jsbody')

</body>