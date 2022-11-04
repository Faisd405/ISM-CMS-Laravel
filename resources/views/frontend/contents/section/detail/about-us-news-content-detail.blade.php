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

    <section class="page-header page-header-post">
        <div class="page-header-content">
            <div class="container">
                <div class="row g-0">
                    <div class="col-lg-5">
                        <div class="post-img-header h-100">
                            <div class="ratio ratio-1x1 h-100 anim-scroll-img delay-300">
                                <img src="{{ $data['read']['coverSrc'] }}" alt="" class="thumb">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="d-flex flex-column justify-content-center h-100 post-header-wrapper">
                            <div class="post-title-header">
                                <div class="subtitle text-danger mb-4 split-text">{!! $data['section']->fieldLang('name') !!}</div>
                                <h1 class="title fw-700 text-white split-text">{!! $data['read']->fieldLang('title') !!}</h1>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="post-date anim-load-up delay-400">
                                    <div class="d-flex align-items-center">
                                        <div class="fs-38 fw-700 line-height-sm me-2 dd">
                                            {{ $data['read']->created_at->format('d') }}</div>
                                        <div class="flex-grow-1 mm-yy">
                                            <div class="subtitle">{{ $data['read']->created_at->format('M') }}</div>
                                            <div class="subtitle">{{ $data['read']->created_at->format('Y') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="social-share d-flex align-items-center anim-load-up delay-400">
                                    <span class="subtitle text-muted">@lang('text.share')</span>
                                    <a href="#!" class="social-link text-dark">
                                        <div class="label-btn span-2-red"><i class="fa-brands fa-facebook fs-20"></i></div>
                                    </a>
                                    <a href="#!" class="social-link text-dark">
                                        <div class="label-btn span-2-red"><i class="fa-brands fa-twitter fs-20"></i></div>
                                    </a>
                                    <a href="#!" class="social-link text-dark">
                                        <div class="label-btn span-2-red"><i class="fa-brands fa-whatsapp fs-20"></i></div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content-wrap page-content" data-nav-color="dark">
        <div class="container">
            <div class="row g-0 justify-content-center">
                <div class="col-lg-8">
                    <div class="post-entry">
                        {!! $data['read']->fieldLang('content') !!}
                    </div>
                </div>
            </div>
            @if ($data['next']->count() > 0)
                <div class="row g-0 justify-content-center nav-post">
                    <div class="col-lg-8">
                        <a href="{{route('content.post.read.' . $data['next']->first()->section->slug, ['slugPost' => $data['next']->first()->slug])}}" class="post-link d-flex align-items-center anim-scroll-up" data-aos>
                            <div class="post-content d-flex flex-grow-1">
                                <div class="post-header flex-grow-1">
                                    <div class="post-date d-flex align-items-center mb-2">
                                        <div class="fs-38 fw-700 line-height-sm me-2 dd">
                                            {{ $data['next']->first()->created_at->format('d') }}</div>
                                        <div class="mm-yy">
                                            <div class="subtitle">{{ $data['next']->first()->created_at->format('M') }}</div>
                                            <div class="subtitle">{{ $data['next']->first()->created_at->format('Y') }}</div>
                                        </div>
                                    </div>
                                    <h4 class="title fw-700 post-title clamp-1">{!! $data['next']->first()->fieldLang('title') !!}</h4>
                                </div>
                            </div>
                            <div class="d-flex align-items-center flex-nowrap ms-5">
                                <span class="subtitle me-3">@lang('pagination.next')</span>
                                <div
                                    class="post-icon d-flex align-items-center justify-content-center rounded-circle text-dark">
                                    <div class="label-btn label-btn-right text-center">
                                        <i class="fa-light fa-arrow-right-long"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
