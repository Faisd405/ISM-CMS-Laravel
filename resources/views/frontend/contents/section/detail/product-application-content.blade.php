@extends('layouts.frontend.layout')

@section('content')
{{-- DETAIL

    DATA :
    {!! $data['read']->fieldLang('title') !!} // title

    @if ($data['read']['config']['hide_intro'] == false)
    {!! $data['read']->fieldLang('intro') !!} //intro
    @endif

    {!! $data['read']->fieldLang('content') !!} //content

    @if ($data['read']['config']['hide_cover'] == false) //cover
    <img src="{{ $data['cover'] }}" title="{{ $data['read']['cover']['title'] }}" alt="{{ $data['read']['cover']['alt'] }}">
    @endif

    @if ($data['read']['config']['hide_banner'] == false) //banner
    <img src="{{ $data['banner'] }}" title="{{ $data['read']['banner']['title'] }}" alt="{{ $data['read']['banner']['alt'] }}">
    @endif

    DATA LOOPING :
    $data['read']['medias']
    $data['read']['latest_posts']
    $data['read']['addon_fields']
    $data['read']['fields']
    @if ($data['read']['config']['hide_tags'] == false)
        $data['read']['tags']
    @endif

    {!! $data['creator'] !!}

    LINK SHARE :
    $data['share_facebook']
    $data['share_twitter']
    $data['share_whatsapp']
    $data['share_linkedin']
    $data['share_pinterest']

--}}
<section class="page-header">
    <div class="page-header-bg thumb overflow-hidden">
        <div class="thumb overflow-hidden">
            <img src="{{$data['read']['bannerSrc']}}" alt="" class="thumb" data-rellax data-rellax-speed="-4">
            <div class="bg-overlay"></div>
        </div>
    </div>
    <div class="page-header-content d-flex flex-column">
        <div class="container mt-auto">
            <div class="row g-0">
                <div class="col-lg-8">
                    <div class="main-title">
                        <div class="subtitle mb-4 mb-xl-5 text-danger split-text">{{$data['read']->fieldLang('title')}}</div>
                        <h1 class="title fw-700 title-display-2 text-uppercase split-text line-height-sm text-white"
                        >{{ $data['read']['header_text'] && !empty($data['read']->fieldLang('header_text')) ? $data['read']->fieldLang('header_text') : __('text.header_title') }}</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ornament ornament-1">
        <img src="{{ asset('assets/frontend/img/ornament01.svg') }}" class="w-100 anim-load-left delay-400" alt="">
    </div>
</section>

<div class="app-logo-title" data-nav-color="dark">
    <div class="container">
        <div class="app-logo d-flex align-items-center">
            <img src="{{$data['read']['coverSrc']}}" alt="" class="logo anim-load-up delay-600">
        </div>
    </div>
</div>

<section class="content-wrap page-content" data-nav-color="dark">
    <div class="content-wrap">
        <div class="container">
            <div class="row g-0">
                <div class="col-lg-2">
                    {{-- <div class="subtitle mb-4 split-text" data-aos>@lang('text.about') {!! $data['parent']['parent'] ? $data['parent']->getParent()->fieldLang('title') : isset($data['parent']) ? $data['parent']->fieldLang('title') : $data['read']->fieldLang('title') !!}</div> --}}
                </div>
                <div class="col-lg-8">
                    <div class="main-title mb-6">
                        <h1 class="title fw-700 title-display-1 line-height-sm split-text" data-aos>{!! $data['read']->fieldLang('title') !!}</h1>
                    </div>
                    <div class="post-entry">
                        <p>{!! $data['read']->fieldLang('content') !!}</p>
                        @if (!$data['read']->fieldLang('content'))
                            <p class="text-center mt-6">@lang('text.page_content_empty')</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if ($data['medias']->count() > 0)
        <div class="content-wrap bg-gradient-muted">
            <div class="container">
                <div class="list-customer-logo">
                    <div class="row g-0 justify-content-center">
                        <div class="col-lg-6">
                            <div class="main-title text-center mb-5">
                                <div class="subtitle text-muted mb-5 split-text" data-aos>@lang('text.our_customers')</div>
                                <h1 class="title text-uppercase line-height-sm fw-700 split-text" data-aos>@lang('text.product_application_title_customer')</h1>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        @foreach ($data['medias'] as $media)
                            <div class="col-xl-2 col-lg-4 col-6">
                                <div class="ratio ratio-4x3 logo-item anim-scroll-up" data-aos>
                                    <img src="{{ $media['file_src'] }}" alt="" class="thumb">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>
@endsection
