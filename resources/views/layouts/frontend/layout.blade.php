@extends('layouts.frontend.application')

@section('layout-content')
    <div id="page">
        @include('layouts.frontend.includes.header')
        <main>
            @yield('content')
        </main>
        @include('layouts.frontend.includes.footer')
    </div>
@endsection
