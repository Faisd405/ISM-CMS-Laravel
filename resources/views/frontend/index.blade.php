@extends('layouts.frontend.layout')

@section('body-attr')
{{-- attribute body jika tiap halaman memiliki attribute tag body yang berbeda, jika sama tidak dibutuhkan --}}
data-bs-spy="scroll" data-bs-offset="98" data-bs-target=".nav-dot"
@endsection

@section('styles')
{{-- css tambahan per halaman --}}
<!--libs-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/css/swiper-bundle.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/css/swiper-custom.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/css/slick.min.css') }}">
@endsection

@section('content')
@foreach ($data['widgets'] as $widget)
    @include('frontend.widget.'.$widget['template'], ['widget' => $widget])
@endforeach


@endsection

@section('scripts')
{{-- scripts tambahan per halaman --}}
@endsection

@section('html-after-main')
<div class="nav-dot">
    <ul class="list-unstyled d-flex flex-column m-0">
        <li><a class="nav-link d-flex align-items-center justify-content-center" href="#main"><div class="subtitle text-nowrap dot-label">Home</div></a></li>
        <li><a class="nav-link d-flex align-items-center justify-content-center" href="#home-about"><div class="subtitle text-nowrap dot-label">About Us</div></a></li>
        <li><a class="nav-link d-flex align-items-center justify-content-center" href="#home-services"><div class="subtitle text-nowrap dot-label">Our Service</div></a></li>
        <li><a class="nav-link d-flex align-items-center justify-content-center" href="#home-products"><div class="subtitle text-nowrap dot-label">Our Product</div></a></li>
        <li><a class="nav-link d-flex align-items-center justify-content-center" href="#get-in-touch"><div class="subtitle text-nowrap dot-label">Get In Touch</div></a></li>
    </ul>
</div>
@endsection

@section('jsbody')
{{-- js tambahan per halaman --}}

@endsection
