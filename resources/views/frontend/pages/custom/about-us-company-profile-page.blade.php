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
                            <div class="subtitle mb-5 text-danger split-text">{!! $data['read']->fieldLang('title') !!}</div>
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
                <div class="row g-0">
                    <div class="col-lg-2">
                        <div class="subtitle mb-4 split-text" data-aos>@lang('text.about') {!! $data['read']->fieldLang('title') !!}</div>
                    </div>
                    <div class="col-lg-8">
                        {!! $data['read']->fieldLang('content') !!}
                        @if (!$data['read']->fieldLang('content'))
                            <div class="post-entry">
                                <p class="text-center mt-6">@lang('text.page_content_empty')</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="content-wrap bg-muted">
            <div class="container">
                <div class="row g-0 justify-content-center">
                    <div class="col-lg-6">
                        <div class="main-title text-center mb-4">
                            <div class="subtitle text-danger mb-5 split-text" data-aos>@lang('text.about_values')</div>
                            <h1 class="title text-uppercase line-height-sm fw-700 split-text" data-aos>@lang('text.core_values')</h1>
                        </div>
                    </div>
                </div>
                <div class="row gx-5 justify-content-center">
                    @foreach ($data['childs'] as $child)
                    <div class="col-lg-4">
                        <div class="text-center my-5 px-4 anim-scroll-up" data-aos>
                            <img class="my-4" src="{{$child['coverSrc']}}" style="width:4rem">
                            <h3 class="title line-height-sm fw-700 mb-4">{!! $child->fieldLang('title') !!}</h3>
                            {!! $child->fieldLang('content') !!}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection