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
                        <div class="subtitle mb-4 mb-xl-5 text-danger split-text">{!! $data['read']->fieldLang('name') !!}</div>
                        <h1 class="title fw-700 title-display-2 text-uppercase split-text line-height-sm text-white">{{ $data['read']['header_text'] ? $data['read']->fieldLang('header_text') : __('text.header_title')}}</h1>
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
        <div class="row justify-content-center">
            @foreach ($data['medias'] as $media)
            <div class="col-lg-3 anim-scroll-up" data-aos>
                <div class="card partner-item mb-4 shadow-none border">
                    <a class="card-body partner-content d-flex flex-column" href="{{ $media['url'] }}" target="_blank">
                        <div class="ratio ratio-4x3 partner-logo mx-auto mb-4">
                            <img src="{{ $media['coverSrc'] }}" alt="" class="thumb object-fit-contain">
                        </div>
                        <div class="partner-info mt-auto text-center">
                            <h5 class="title mb-3">{!! $media->fieldLang('title') !!}</h5>
                            <p class="clamp-3 fs-12">{!! $media->fieldLang('description') !!}</p>
                        </div>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @if ($data['read']['config']['paginate_media'])
            <nav class="pagination-nav d-flex justify-content-center anim-scroll-up" data-aos>
                {{ $data['medias']->links('vendor.pagination.frontend') }}
            </nav>
        @endif
    </div>
</section>
@endsection
