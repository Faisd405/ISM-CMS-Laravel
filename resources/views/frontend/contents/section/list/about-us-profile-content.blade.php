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
                            <div class="subtitle mb-4 mb-xl-5 text-danger split-text">{!! $data['read']->fieldLang('name') !!}</div>
                            <h1 class="title fw-700 title-display-2 text-uppercase split-text line-height-sm text-white">{{ $data['read']['header_text'] ? $data['read']->fieldLang('header_text') : __('text.header_title')}}</h1>
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
        <div class="content-wrap pb-0">
            <div class="container">
                <div class="row g-0 justify-content-center">
                    <div class="col-lg-8">
                        <div class="row g-0 justify-content-between">
                            <div class="col-lg-4">
                                <div class="main-title mb-5 mb-lg-0 anim-scroll-up" data-aos>
                                    <h1 class="title fw-700 line-height-sm">@lang('text.meet_our_team')</h1>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="caption-text anim-scroll-up" data-aos>
                                    {!! $data['read']->fieldLang('description') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-wrap">
            <div class="container">
                <div class="row list-teams">
                    @foreach ($data['posts'] as $post)
                        <div class="col-lg-6 team-item">
                            <div class="card card-team d-flex flex-column flex-xl-row overflow-xl-hidden anim-scroll-up" data-aos>
                                <div class="card-header position-relative flex-shrink-0 d-flex flex-column">
                                    <div class="team-avatar thumb">
                                        <img src="{{ $post['coverSrc'] }}" alt="" class="thumb">
                                        <div class="bg-overlay"></div>
                                    </div>
                                    <div class="team-social d-flex flex-xl-column align-items-end">
                                        <a href="{{ $post['addon_fields']['facebook'] }}"
                                            class="btn icon-btn btn-sm btn-light">
                                            <div class="label-btn span-center fs-16 span-2-red"><i class="fa-brands fa-facebook"></i>
                                            </div>
                                        </a>
                                        <a href="{{ $post['addon_fields']['twitter'] }}"
                                            class="btn icon-btn btn-sm btn-light">
                                            <div class="label-btn span-center fs-16 span-2-red"><i class="fa-brands fa-twitter"></i>
                                            </div>
                                        </a>
                                        <a href="{{ $post['addon_fields']['instagram'] }}"
                                            class="btn icon-btn btn-sm btn-light">
                                            <div class="label-btn span-center fs-16 span-2-red"><i class="fa-brands fa-instagram"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="team-name mt-auto">
                                        <h5 class="title title-name fw-700 mb-2">{{ $post->fieldLang('title') }}
                                        </h5>
                                        <div class="job-title subtitle">{!! $post->fieldLang('intro') !!}</div>
                                    </div>
                                </div>
                                <div class="flex-grow-1 d-flex flex-column">
                                    <div class="card-body team-info flex-grow-1 position-relative">
                                        <i class="fa-solid fa-quote-left text-danger fs-32 mb-3"></i>
                                        <div class="team-quotes clamp-4">
                                            {!! $post->fieldLang('content') !!}
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <a href="#modal-team-{{ $post['id'] }}" data-bs-toggle="modal"
                                            class="text-dark d-flex align-items-center">
                                            <div class="label-btn subtitle span-2-red d-inline-flex flex-grow-0">@lang('text.read_more')
                                            </div>
                                            <i class="fa-light fa-arrow-right-long text-danger ms-auto"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    @foreach ($data['posts'] as $post)
        <div class="modal fade" id="modal-team-{{ $post['id'] }}" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-container modal-dialog-centered">
                <div class="row g-0 justify-content-center flex-grow-1">
                    <div class="col-lg-10">
                        <div class="modal-content">
                            <div class="row g-0">
                                <div class="col-lg-5">
                                    <div class="ratio ratio-1x1 h-100">
                                        <img src="{{ $post['coverSrc'] }}" alt="" class="thumb">
                                    </div>
                                </div>
                                <div class="col-lg-6 mx-auto">
                                    <div class="modal-body h-100 d-flex flex-column justify-content-center">
                                        <div class="team-name mb-5">
                                            <h3 class="title title-name fw-700 mb-2">{{ $post->fieldLang('title') }}</h3>
                                            <div class="job-title text-danger subtitle">{!! $post->fieldLang('intro') !!}</div>
                                        </div>
                                        <div class="post-entry mb-5">
                                            {!! $post->fieldLang('content') !!}
                                        </div>
                                        <div class="social-share d-flex align-items-center">
                                            <a href="{{ $post['addon_fields']['facebook'] }}"
                                                class="social-link text-dark">
                                                <div class="label-btn span-2-red">
                                                    <i class="fa-brands fa-facebook fs-20"></i>
                                                </div>
                                            </a>
                                            <a href="{{ $post['addon_fields']['twitter'] }}" class="social-link text-dark">
                                                <div class="label-btn span-2-red">
                                                    <i class="fa-brands fa-twitter fs-20"></i>
                                                </div>
                                            </a>
                                            <a href="{{ $post['addon_fields']['instagram'] }}"
                                                class="social-link text-dark">
                                                <div class="label-btn span-2-red">
                                                    <i class="fa-brands fa-instagram fs-20"></i>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
