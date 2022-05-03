<!DOCTYPE html>

<html lang="{{ App::getLocale() }}" class="layout-fixed default-style layout-collapsed">

<head>

    <!-- Meta default -->
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge,chrome=1">
    <meta name="title" content="{{ isset($title) ? $title.' | ' : '' }} @yield('title') {{ strip_tags($config['meta_title']) }}">
    <meta name="description" content="Backend Panel Content Management System">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title.' | ' : '' }} @yield('title') {{ strip_tags($config['meta_title']) }}</title>

    <!-- Open graph -->
    <meta property="og:locale" content="{{ App::getLocale().'_'.Str::upper(App::getLocale()) }}" />
    <meta property="og:url" name="url" content="{{ url()->full() }}">
    <meta property="og:site_name" content="{{ route('login') }}">
    <meta property="og:title" content="{{ isset($title) ? $title.' | ' : '' }} @yield('title') {{ strip_tags($config['meta_title']) }}"/>
    <meta property="og:description" content="Backend Panel Content Management System"/>
    <meta property="og:image" content="{{ $config['open_graph'] }}"/>
    <meta property="og:image:width" content="650" />
    <meta property="og:image:height" content="366" />
    <meta property="og:type" content="website" />

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
    <meta name="msapplication-TileColor" content="#0084FF">
    <meta name="msapplication-TileImage" content="{{ asset('assets/favicon/ms-icon-144x144.png') }}">

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

    <!-- Additional CSS -->
    <link rel="stylesheet" href="{{ asset('assets/backend/css/custom-alsen.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/css/line-awesome.css') }}">

    <!-- Load polyfills -->
    <script src="{{ asset('assets/backend/vendor/js/polyfills.js') }}"></script>
    <script>document['documentMode']===10&&document.write('<script src="https://polyfill.io/v3/polyfill.min.js?features=Intl.~locale.en"><\/script>')</script>

    <!-- Layout helpers -->
    <script src="{{ asset('assets/backend/vendor/js/layout-helpers.js') }}"></script>

    <!-- Libs -->
    <style type="text/css">
		.preloader {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			z-index: 9999;
			background-color: rgba(255,255,255,0.5);
		}
		.preloader .loading {
			position: absolute;
			left: 50%;
			top: 50%;
			transform: translate(-50%,-50%);
			font: 14px arial;
        }
        @media screen and (max-width: 1199.98px) {
            #notif-bar, .bread-right {
                visibility: hidden;
                display: none;
            }
        }
    </style>

    <!-- Core scripts -->
    <script src="{{ asset('assets/backend/vendor/js/pace.js') }}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    @yield('jshead')

    <!-- `perfect-scrollbar` library required by SideNav plugin -->
    <link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/toastr/toastr.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/spinkit/spinkit.css') }}">
    @yield('styles')

</head>

<body @yield('body-attr')>

    <div class="page-loader">
        <div class="bg-danger"></div>
    </div>

    <div class="preloader">
        <div class="loading">
            <div class="col-xs-12">
                <div class="sk-double-bounce sk-primary">
                    <div class="sk-child sk-double-bounce1"></div>
                    <div class="sk-child sk-double-bounce2"></div>
                </div>
            </div>
        </div>
    </div>

    @yield('layout-content')

    <!-- Core scripts -->
    <script src="{{ asset('assets/backend/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/backend/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/backend/vendor/js/sidenav.js') }}"></script>

    <!-- Libs -->

    <!-- `perfect-scrollbar` library required by SideNav plugin -->
    <script src="{{ asset('assets/backend/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/backend/vendor/libs/toastr/toastr.js') }}"></script>
    @yield('scripts')
    <script src="{{ asset('assets/backend/js/demo.js') }}"></script>
    <script>
        // CSRF
        $.ajaxSetup({
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           }
        });

        // FILE BROWSE
        function callfileBrowser() {
            $(".custom-file-input").on("change", function() {
                const fileName = Array.from(this.files).map((value, index) => {
                    if (this.files.length == index + 1) {
                        return value.name
                    } else {
                        return value.name + ', '
                    }
                });
                $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
            });
        }
        callfileBrowser();

        // FOCUSED-FORM
        FormControl = function() {
            var e = $(".form-control, label");
            e.length && e.on("focus blur", function(e) {
                var $this = $(this);
                // console.log($this);
                if ($this.val() !== '') {
                    $this.parents('.form-group').addClass('completed');
                } else {
                    $this.parents('.form-group').removeClass('completed');
                }
                $(this).parents(".form-group").toggleClass("focused", "focus" === e.type)
            }).trigger("blur")
        }();

        //PRE-LOAD
        $(document).ready(function(){
            $('.preloader').delay(1000).fadeOut();
            $('#main').delay(1000).fadeIn();
        });

        //filter
        $("#filter-form").hide();
        $("#filter-btn").click(function() {
            $("#filter-form").toggle();
        });
    </script>
    @include('includes.notif-backend')
    @include('components.toastr')
    @yield('jsbody')
</body>