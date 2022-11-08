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
    $data['medias']
    $data['read']['fields']

    {!! $data['creator'] !!}
--}}

<section class="page-header">
    <div class="page-header-bg thumb overflow-hidden">
        <div class="thumb overflow-hidden min-vh-100">
            <img src="{{$data['read']['bannerSrc']}}" alt="" class="thumb" data-rellax data-rellax-speed="-4">
            <div class="bg-overlay"></div>
        </div>
    </div>
    <div class="page-header-content d-flex flex-column">
        <div class="container mt-auto">
            <div class="row g-0">
                <div class="col-lg-8">
                    <div class="main-title">
                        <div class="subtitle mb-5 text-danger split-text">{!! $data['read']->fieldLang('name') !!}</div>
                        <h1 class="title fw-700 title-display-2 text-uppercase split-text line-height-sm text-white">@lang('text.header_title')</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ornament ornament-1">
        <img src="{{ asset('assets/frontend/img/ornament01.svg') }}" class="w-100 anim-load-left delay-400"
            alt="">
    </div>
</section>

<section class="content-wrap page-content" data-nav-color="dark">
    <div class="content-wrap">
        <div class="container">
            <div class="list-media">
                @foreach ($data['medias'] as $media)
                <div class="row g-0 media-item justify-content-between">
                    <div class="col-lg-4">
                        <div class="ratio ratio-1x1 media-img anim-scroll-img" data-aos>
                            <div class="thumb" data-rellax data-rellax-speed="-2" data-rellax-percentage="0.5">
                                <img src="{{ $media['bannerSrc'] }}" alt="" class="thumb">
                            </div>
                            <div class="bg-overlay"></div>
                            <div class="thumb d-flex align-items-center justify-content-center">
                                <div class="ratio ratio-1x1 media-icon">
                                    <img src="{{ $media['coverSrc'] }}" class="thumb object-fit-contain" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="media-info h-100 d-flex flex-column justify-content-center">
                            <div class="main-title mb-5">
                                <h1 class="title title-display-1 line-height-sm fw-700 split-text" data-aos>{!! $media->fieldLang('title') !!}</h1>
                            </div>
                            <div class="post-entry mb-5">
                                {!! $media->fieldLang('description') !!}
                            </div>
                            @if ($media['url'])
                                <div class="caption-btn anim-scroll-up" data-aos>
                                    <a href="{{ $media['url'] }}" target="_blank" class="btn btn-danger"><div class="label-btn subtitle">Go To Website</div><i class="fa-light fa-arrow-up-right ms-3"></i></a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection
