@extends('layouts.frontend.layout')

@section('body-attr')
    {{-- attribute body jika tiap halaman memiliki attribute tag body yang berbeda, jika sama tidak dibutuhkan --}}
@endsection

@section('styles')
    {{-- css tambahan per halaman --}}
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/swiper.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/lightgallery.css') }}">
@endsection

@section('content')
    @foreach ($data['widgets'] as $key => $widget)
        @include('frontend.widget.' . $widget['template'], ['widget' => $widget])
    @endforeach
@endsection

@section('scripts')
    {{-- scripts tambahan per halaman --}}
@endsection

@section('jsbody')
    {{-- js tambahan per halaman --}}

    <script src="{{ asset('assets/frontend/js/swiper.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/lightgallery.js') }}"></script>
    <script>
        //SLIDER INTRO
        var galleryTop = new Swiper('.intro-content', {
            spaceBetween: 20,
            slidesPerView: 1,
            touchRatio: 0.2,
            autoplay: {
                delay: 6000,
            },
            speed: 1500,
        });



        var galleryThumbs = new Swiper('.intro-img', {
            centeredSlides: true,
            slidesPerView: 1,
            touchRatio: 0.2,
            slideToClickedSlide: true,
            speed: 1500,
            effect: 'fade',
            pagination: {
                el: '.swiper-pagination',
                clickable: 'true',
            },
        });
        galleryTop.controller.control = galleryThumbs;
        galleryThumbs.controller.control = galleryTop;


        //SLIDER SPONSOR NEWS
        var swiper = new Swiper('.news-sponsor .swiper-container', {
            direction: 'vertical',
            slidesPerView: 5,
            // slidesPerGroup: 5,
            loop: 'true',
            spaceBetween: 20,
            autoplay: {
                delay: 1500,
            },
            speed: 1500,
            navigation: {
                nextEl: '.sbn-2',
                prevEl: '.sbp-2',
            },
            breakpoints: {
                // when window width is <= 991.98px
                767.98: {
                    direction: 'horizontal',
                    slidesPerView: 1,
                },
                // when window width is <= 991.98px
                1198.98: {
                    direction: 'horizontal',
                    slidesPerView: 2,
                }
            }
        });
    </script>
@endsection
