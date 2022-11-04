@extends('layouts.frontend.application')

@section('layout-content')
    @include('layouts.frontend.includes.preloader')
    @include('layouts.frontend.includes.header')
    <main id="main">
        @yield('content')
        @include('layouts.frontend.includes.footer')
    </main>
    @yield('html-after-main')
    @include('layouts.frontend.includes.chat-widget')
@endsection
