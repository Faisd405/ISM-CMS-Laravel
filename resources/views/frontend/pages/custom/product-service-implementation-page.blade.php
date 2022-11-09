@extends('layouts.frontend.layout')

@section('content')
    {{-- DETAIL

    DATA :
    {!! $data['read']->fieldLang('title') !!} // title

    @if ($data['read']['config']['show_intro'] == true)
    {!! $data['read']->fieldLang('intro') !!} //intro
    @endif

    @if ($data['read']['config']['show_content'] == true)
    {!! $data['read']->fieldLang('content') !!} //intro
    @endif

    @if ($data['read']['config']['show_cover'] == true) //cover
    <img src="{{ $data['cover'] }}" title="{{ $data['read']['cover']['title'] }}" alt="{{ $data['read']['cover']['alt'] }}">
    @endif

    @if ($data['read']['config']['show_banner'] == true) //banner
    <img src="{{ $data['banner'] }}" title="{{ $data['read']['banner']['title'] }}" alt="{{ $data['read']['banner']['alt'] }}">
    @endif

    DATA LOOPING :
    $data['read']['childs']
    $data['read']['medias']
    $data['read']['fields']
    @if ($data['read']['config']['show_tags'] == true)
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
                <img src="{{ $data['read']['bannerSrc'] }}" alt="" class="thumb" data-rellax data-rellax-speed="-4">
                <div class="bg-overlay"></div>
            </div>
        </div>
        <div class="page-header-content d-flex flex-column">
            <div class="container mt-auto">
                <div class="row g-0">
                    <div class="col-lg-8">
                        <div class="main-title">
                            <div class="subtitle mb-4 mb-xl-5 text-danger split-text">{!! $data['read']->fieldLang('title') !!}</div>
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
        <div class="container">
            <div class="list-media">
                @foreach ($data['childs'] as $child)
                    <div class="row g-0 media-item justify-content-between">
                        <div class="col-lg-4">
                            <div class="ratio ratio-1x1 media-img anim-scroll-img" data-aos>
                                <div class="thumb" data-rellax data-rellax-speed="-2" data-rellax-percentage="0.5">
                                    <img src="{{ $child['bannerSrc'] }}" alt="" class="thumb">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="media-info h-100 d-flex flex-column justify-content-center">
                                <div class="main-title mb-5">
                                    <h1 class="title title-display-1 line-height-sm fw-700 split-text" data-aos>{!! $child->fieldLang('title') !!}</h1>
                                </div>
                                <div class="post-entry mb-5">
                                    {!! $child->fieldLang('intro') !!}
                                </div>
                                <div class="caption-btn anim-scroll-up" data-aos>
                                    <a href="{{ route('page.read.child.' . $child['slug']) }}" class="btn btn-danger">
                                        <div class="label-btn subtitle">
                                            @lang('text.see_details')
                                        </div>
                                        <i class="fa-light fa-arrow-right-long ms-3"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
