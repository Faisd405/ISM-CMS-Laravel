@extends('layouts.frontend.layout')

@section('content')
    {{-- DETAIL

    DATA :
    {!! $data['read']->fieldLang('name') !!} // name

    @if ($data['read']['config']['hide_description'] == false)
    {!! $data['read']->fieldLang('description') !!} //description
    @endif

    //image preview
    <img src="{{ $data['image_preview'] }}" title="{{ $data['read']['image_preview']['title'] }}" alt="{{ $data['read']['image_preview']['alt'] }}">

    @if ($data['read']['config']['hide_banner'] == false) //banner
    <img src="{{ $data['banner'] }}" title="{{ $data['read']['banner']['title'] }}" alt="{{ $data['read']['banner']['alt'] }}">
    @endif

    DATA LOOPING :
    $data['files']
    $data['read']['fields']

    {!! $data['creator'] !!}
--}}
    <div class="box-wrap banner-breadcrumb">
        <div class="container">
            <div class="banner-content">
                <div class="title-heading">
                    <h1>Making steel in the 2020s </h1>
                </div>
                <div class="list-breadcrumb">
                    <ul>
                        <li><a href="">Home</a></li>
                        <li><a href="{{ route('gallery.list') }}">Steel Galleries</a></li>
                        <li class="current">Making steel in the 2020s</a></li>
                    </ul>
                </div>
            </div>
            <div class="box-content list-photo">
                <div class="row">
                    <div class="col-xl-9">
                        <ul class="masonry-photo" id="masonry">
                            @foreach ($data['files'] as $file)
                            <li data-src="{{ $file['file'] }}" data-sub-html="<h4>{{ $file->fieldLang('title') }}</h4><span>{{ $file->fieldLang('description') }}</span>">
                                <span><img src="{{ $file['file'] }}"></span>
                            </li>
                            @endforeach

                        </ul>
                        <div class="box-btn mb-4 mb-lg-0">
                            <a href="{{ route('gallery.list') }}" class="btn btn-primary">Go Back</a>
                        </div>
                    </div>
                    <div class="col-xl-3">
                        <div class="item-sidenav">
                            <div class="title-heading">
                                <h5>
                                    {{ $data['read']->fieldLang('name') }}
                                </h5>
                            </div>
                            <article class="summary-content">
                                {!! $data['read']->fieldLang('description') !!}
                            </article>
                        </div>
                        <div class="item-sidenav">
                            <div class="title-heading">
                                <h5>Album Info</h5>
                            </div>
                            <ul class="info">
                                <li>
                                    <span class="title-info">Category</span>
                                    <span class="data-info">{{ $data['read']['category']->fieldLang('name') }}</span>
                                </li>
                                <li>
                                    <span class="title-info">Date</span>
                                    <span class="data-info">{{ $data['read']['created_at']->format('d M Y') }}</span>
                                </li>
                                <li>
                                    <span class="title-info">Amount of Photos</span>
                                    <span class="data-info">
                                        {{ count($data['files']) }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="thumbnail-img">
            <img src="{{ $data['read']['cover_src'] }}" alt="">
        </div>
    </div>
@endsection

@section('styles')
    <!-- Css Additional -->
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/lightgallery.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/swiper.min.css') }}">
@endsection

@section('jsbody')
    <script src="{{ asset('assets/frontend/js/lightgallery.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/lg/lg-zoom.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/lg/lg-thumbnail.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/masonry.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/imagesloaded.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/classiee.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/AnimOnScroll.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/modernizr-2.6.2.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/swiper.min.js') }}"></script>

    <script>
        //MASONRY
        new AnimOnScroll(document.getElementById('masonry'), {
            minDuration: 0,
            maxDuration: 0,
            viewportFactor: 0
        });

        $('.masonry-photo').lightGallery({});

        //SLIDER SPONSOR NEWS
        var swiper = new Swiper('.news-sponsor .swiper-container', {
            direction: 'vertical',
            slidesPerView: 5,
            spaceBetween: 20,
            autoplay: {
                delay: 6000,
                reverseDirection: 'true',
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
