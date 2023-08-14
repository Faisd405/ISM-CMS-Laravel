@extends('layouts.frontend.application')

@section('layout-content')
    @include('layouts.frontend.includes.header')
        @yield('content')
    @include('layouts.frontend.includes.footer')
@endsection
