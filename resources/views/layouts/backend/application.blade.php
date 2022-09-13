<!DOCTYPE html>

<html lang="{{ App::getLocale() }}" class="light-style{{ config('cms.setting.layout_fixed') == 1 ? ' layout-navbar-fixed layout-fixed' : '' }}">

<head>

    <!-- Meta default -->
    <meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="IE=edge,chrome=1">
	<meta name="title" content="{!! isset($title) ? $title.' | ' : '' !!} @yield('title') {!! strip_tags(config('cmsConfig.seo.meta_title')) !!}">
    <meta name="description" content="Backend Panel Content Management System">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{!! isset($title) ? $title.' | ' : '' !!} @yield('title') {!! strip_tags(config('cmsConfig.seo.meta_title')) !!}</title>

    <!-- Open graph -->
    <meta property="og:locale" content="{{ App::getLocale().'_'.Str::upper(App::getLocale()) }}" />
    <meta property="og:url" name="url" content="{{ url()->full() }}">
    <meta property="og:site_name" content="{{ route('login') }}">
    <meta property="og:title" content="{!! isset($title) ? $title.' | ' : '' !!} @yield('title') {!! strip_tags(config('cmsConfig.seo.meta_title')) !!}"/>
    <meta property="og:description" content="Backend Panel Content Management System"/>
    <meta property="og:image" content="{{ config('cmsConfig.file.open_graph') }}"/>
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,700;0,900;1,300;1,400;1,500;1,700&display=swap" 
		rel="stylesheet">

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
	<script>
		document['documentMode'] === 10 && document.write(
			'<script src="https://polyfill.io/v3/polyfill.min.js?features=Intl.~locale.en"><\/script>')
	</script>

	<script src="{{ asset('assets/backend/vendor/js/material-ripple.js') }}"></script>
	<script src="{{ asset('assets/backend/vendor/js/layout-helpers.js') }}"></script>

	@if (config('setting.theme_setting') == true)
	<!-- Theme settings -->
	<!-- This file MUST be included after core stylesheets and layout-helpers.js in the <head> section -->
	<script src="{{ asset('assets/backend/vendor/js/theme-settings.js') }}"></script>
	<script>
		window.themeSettings = new ThemeSettings({
			cssPath: '../assets/backend/vendor/css/rtl/',
			themesPath: '../assets/backend/vendor/css/rtl/'
		});
	</script>
	@endif

	<!-- Core scripts -->
	<script src="{{ asset('assets/backend/vendor/js/pace.js') }}"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    @yield('jshead')

	<!-- Libs -->
	<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">

	<!-- Custome Css -->
	<link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/custom/custom.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/backend/vendor/fonts/uicons-all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/toastr/toastr.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/spinkit/spinkit.css') }}">
    @yield('styles')

</head>

<body @yield('body-attribute')>

	<div class="page-loader">
        <div class="bg-{{ Request::segment(1) == 'admin' ? 'main' : 'primary' }}"></div>
    </div>

    @yield('layout-content')
    
    <!-- Core scripts -->
	<script src="{{ asset('assets/backend/vendor/libs/popper/popper.js') }}"></script>
	<script src="{{ asset('assets/backend/vendor/js/bootstrap.js') }}"></script>
	<script src="{{ asset('assets/backend/vendor/js/sidenav.js') }}"></script>

	<!-- Libs -->
	<script src="{{ asset('assets/backend/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/backend/vendor/libs/toastr/toastr.js') }}"></script>
    @yield('scripts')

	<!-- Demo -->
	<script src="{{ asset('assets/backend/js/demo.js') }}"></script>
	<script src="{{ asset('assets/backend/js/forms_validation.js') }}"></script>
	<script src="{{ asset('assets/backend/vendor/libs/validate/validate.js') }}"></script>
	<script src="{{ asset('assets/backend/vendor/libs/select2/select2.js') }}"></script>
	<script src="{{ asset('assets/backend/vendor/libs/bootstrap-tagsinput/bootstrap-tagsinput.js') }}"></script>

	<!-- switcher -->
	<script src="{{ asset('assets/backend/vendor/js/custom/jquery.style.switcher.js') }}"></script>
	<script src="{{ asset('assets/backend/vendor/js/custom/js.cookie.js') }}"></script>

    <script>
        // CSRF
        $.ajaxSetup({
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           }
        });

		@if (Request::segment(1) == 'admin')
		// total inquiry unread
		$(document).ready(function() {
			$.ajax({
				url : "/admin/inquiry/total-unread",
				type : "GET",
				dataType : "json",
				data : {},
				success:function(data) {
					const totalUnread = data.data;
					$('#total-inquiry-unread').text(totalUnread);
					if (totalUnread > 0) {
						$('#inquiry-form').show();
					} else {
						$('#inquiry-form').hide();
					}
				}
			});
		});
		@endif
    </script>
	
    @include('components.toastr')
	@include('includes.notif-backend')
    @yield('jsbody')

</body>

</html>