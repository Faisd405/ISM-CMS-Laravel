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
                <img src="{{ $data['read']['bannerSrc'] }}" alt="" class="thumb" data-rellax data-rellax-speed="-4">
                <div class="bg-overlay"></div>
            </div>
        </div>
        <div class="page-header-content d-flex flex-column">
            <div class="container mt-auto">
                <div class="row g-0">
                    <div class="col-lg-8">
                        <div class="main-title">
                            <div class="subtitle mb-4 text-danger split-text">{!! $data['read']->fieldLang('name') !!}</div>
                            <h1 class="title fw-700 title-display-2 text-uppercase split-text line-height-sm text-white">{{ $data['read']['header_text'] && !empty($data['read']->fieldLang('header_text')) ? $data['read']->fieldLang('header_text') : __('text.header_title_2') }}</h1>
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
            <div class="list-post">
                @foreach ($data['posts'] as $post)
                    <div class="row g-0 list-post-item">
                        <div class="col-lg-9">
                            <a href="{{route('content.post.read.' . $post->section->slug, ['slugPost' => $post->slug])}}" class="d-flex single-post">
                                <div class="post-date">
                                    <div class="d-flex align-items-center">
                                        <div class="fs-38 fw-700 line-height-sm me-2 dd">{{ $post->created_at->format('d') }}</div>
                                        <div class="flex-grow-1 mm-yy">
                                            <div class="subtitle">{{ $post->created_at->format('M') }}</div>
                                            <div class="subtitle">{{ $post->created_at->format('Y') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <figure class="post-img ratio ratio-1x1 mb-0 overflow-hidden anim-scroll-img" data-aos>
                                    <img src="{{$post['coverSrc']}}" alt="" class="thumb">
                                </figure>
                                <div class="post-content d-flex flex-column flex-grow-1">
                                    <div class="post-header mb-5">
                                        <h4 class="title fw-700 post-title clamp-2">
                                            <span class="link">
                                                {{ $post->fieldLang('title') }}
                                            </span>
                                        </h4>
                                    </div>
                                    <div class="post-body mb-5">
                                        <div class="post-text clamp-3">
                                            {!! $post->fieldLang('intro') !!}
                                        </div>
                                    </div>
                                    <div class="post-footer mt-auto">
                                        <span class="d-inline-flex text-dark align-items-center">
                                            <div class="label-btn d-inline-flex subtitle span-2-red">@lang('text.read_more')</div>
                                            <i class="fa-light fa-arrow-right-long text-danger ms-3"></i>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            {{-- {{dd($data['read']['config']['paginate_post'])}} --}}
            @if ($data['read']['config']['paginate_post'])
                <nav class="pagination-nav d-flex justify-content-center anim-scroll-up" data-aos>
                    {{ $data['posts']->links('vendor.pagination.frontend') }}
                </nav>
            @endif
        </div>

    </section>
@endsection
