@extends('layouts.frontend.layout')

@section('body-attr')
class="single-page"
@endsection

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
            <img src="{{asset('assets/frontend/img/bg-career.jpg')}}" alt="" class="thumb" data-rellax data-rellax-speed="-4">
            <div class="bg-overlay"></div>
        </div>
    </div>
    <div class="page-header-content d-flex flex-column">
        <div class="container mt-auto">
            <div class="row g-0">
                <div class="col-lg-6">
                    <div class="main-title">
                        <div class="subtitle mb-4 text-danger split-text">{!! $data['read']->fieldLang('name') !!}</div>
                        <h1 class="title fw-700 split-text line-height-sm">{!! $data['read']->fieldLang('description') !!}</h1>
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
        <div class="container">
            <div class="row g-0 justify-content-center">
                <div class="col-lg-8">
                    <div class="list-career accordion" id="career-accordion">
                        @foreach ($data['posts'] as $post)
                            <div class="accordion-item career-item anim-scroll-up" data-aos>
                                <div class="accordion-header">
                                    <a href="#col-1" class="accordion-button" data-bs-toggle="collapse">
                                        <div class="flex-grow-1 me-4">
                                            <div class="subtitle text-muted mb-2">@lang('text.position')</div>
                                            <h4 class="title fw-700">{!! $post->fieldLang('title') !!}</h4>
                                        </div>
                                    </a>
                                </div>
                                <div class="accordion-collapse collapse" id="col-1" data-bs-parent="#career-accordion">
                                    <div class="accordion-body">
                                        <div class="post-entry">
                                            <p>{!! $post->fieldLang('content') !!}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
