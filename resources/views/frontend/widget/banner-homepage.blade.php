<section class="banner-area" data-nav-color="light">
    <div class="banner-slide w-100 swiper-container">
        <div class="swiper-wrapper">
            @foreach ($widget['module']['files'] as $file)
                <div class="swiper-slide banner-slide-item">
                    <div class="banner-img thumb">
                        <img src="{{ $file['file_src']['image'] }}" alt="" class="thumb" data-rellax data-rellax-speed="-4">
                        <div class="bg-overlay"></div>
                    </div>
                    <div class="banner-caption min-vh-100 d-flex flex-column">
                        <div class="container mt-auto my-xl-auto">
                            <div class="row g-0">
                                <div class="col-lg-6">
                                    <div class="main-title mb-5">
                                        <h1 class="title fw-700 title-display-2 split-text text-uppercase line-height-sm text-white"
                                        >{!! $file->fieldLang('title') !!} </h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="container mt-xl-auto">
                            <div class="row g-0">
                                <div class="col-lg-5">
                                    <div class="caption-text anim-load-up delay-300 text-white mb-5 text-light">
                                        {!! $file->fieldLang('description') !!}
                                    </div>
                                    <div class="anim-load-up delay-400">
                                        <a href="{{ $file['url'] }}" class="btn btn-danger">
                                            <div class="label-btn d-inline-flex subtitle">@lang('text.discover_now')</div><i
                                                class="fa-light fa-arrow-right-long ms-3"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="ornament ornament-1">
        <img src="{{ asset('assets/frontend/img/ornament01.svg') }}" class="w-100 anim-load-left delay-600"
            alt="">
    </div>
</section>
