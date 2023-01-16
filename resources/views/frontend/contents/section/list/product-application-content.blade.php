@extends('layouts.frontend.layout')

@section('content')
{{-- DETAIL

    DATA :
    {!! $data['read']->fieldLang('name') !!} // name

    @if ($data['read']['config']['hide_description'] == false)
    {!! $data['read']->fieldLang('description') !!} //description
    @endif

    @if ($data['read']['config']['hide_banner'] == false) //banner
    <img src="{{ $data['banner'] }}" title="{{ $data['read']['banner']['title'] }}" alt="{{ $data['read']['banner']['alt'] }}">
    @endif

    DATA LOOPING :
    $data['read']['categories']
    $data['read']['posts']
    $data['read']['fields']

    {!! $data['creator'] !!}
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
                        <div class="subtitle mb-4 mb-xl-5 text-danger split-text">Application</div>
                        <h1 class="title fw-700 title-display-2 text-uppercase split-text line-height-sm text-white"
                            >{{ $data['read']['header_text'] ? $data['read']->fieldLang('header_text') : __('text.header_title')}}
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ornament ornament-1">
        <img src="{{ asset('assets/frontend/img/ornament01.svg') }}" class="w-100 anim-load-left delay-400" alt="">
    </div>
</section>

<section class="content-wrap page-content" data-nav-color="dark">
    <div class="content-wrap pb-0">
        <div class="container">
            <div class="row g-0">
                <div class="col-lg-2">
                    {{-- <div class="subtitle mb-4 split-text" data-aos>@lang('text.about') {!! $data['read']->fieldLang('title') !!}</div> --}}
                </div>
                <div class="col-lg-8">
                    <div class="main-title mb-6">
                        <h1 class="title fw-700 title-display-1 line-height-sm split-text" data-aos>{!! $data['read']->fieldLang('name') !!}</h1>
                    </div>
                    <div class="post-entry">
                        <p>{!! $data['read']->fieldLang('description') !!}</p>
                        @if (!$data['read']->fieldLang('description'))
                            <p class="text-center mt-6">@lang('text.page_content_empty')</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-wrap">
        <div class="container-fluid">
            <div class="row list-products justify-content-center">
                @foreach ($data['posts'] as $post)
                    <div class="col-xl-4 col-xxl-3 anim-scroll-up" data-aos>
                        <div class="product-item d-flex mb-4 ratio ratio-1x1">
                            <img src="{{ $post['bannerSrc'] }}" alt="" class="thumb">
                            <a href="{{ route('content.post.read.' . $post->section->slug, ['slugPost' => $post->slug]) }}" class="product-content d-flex flex-column">
                                <div class="product-icon align-self-center">
                                    <img src="{{ $post['coverSrc'] }}" class="w-100" alt="">
                                </div>
                                <div class="product-info text-center mt-auto">
                                    <h5 class="title fw-700 text-uppercase mb-4">{!! $post->fieldLang('title') !!}</h5>
                                    <div class="product-excerpt clamp-3">
                                        {!! $post->fieldLang('intro') !!}
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            @if ($data['read']['config']['paginate_post'])
                <nav class="pagination-nav d-flex justify-content-center anim-scroll-up" data-aos>
                    {{ $data['posts']->links('vendor.pagination.frontend') }}
                </nav>
            @endif

        </div>
    </div>
</section>
@endsection
