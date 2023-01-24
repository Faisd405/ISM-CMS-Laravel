<section class="content-wrap p-0" id="home-products" data-nav-color="light">
    <a href="#!" class="back-intro d-flex align-items-center text-white is-hide">
        <div class="label-btn subtitle">@lang('text.back_to_intro')</div>
    </a>
    <div class="banner-intro overflow-hidden">
        <div class="slide-intro">
            <div class="slide-bg-item slide-item position-relative">
                <div class="slide-img thumb">
                    <img src="{{ $widget['module']['section']['bannerSrc'] }}" alt="" class="thumb" data-rellax loading="lazy"
                        data-rellax-speed="-4" data-rellax-percentage=".5">
                    <div class="bg-overlay"></div>
                </div>
                <div class="slide-caption content-wrap d-flex flex-column vw-100 anim-scroll-up" data-aos>
                    <div class="container my-xl-auto">
                        <div class="row g-0">
                            <div class="col-lg-6">
                                <div class="main-title mb-5">
                                    <div class="subtitle mb-4 mb-xl-5 split-text text-danger" data-aos>{!! $widget['module']['section']->fieldLang('name') !!}</div>
                                    <h1 class="title fw-700 title-display-1 text-white text-uppercase line-height-sm split-text"
                                        data-aos>{!! $widget->fieldLang('title') !!}</h1>
                                </div>
                                <div class="caption-text text-muted anim-scroll-up" data-aos>
                                    {!! $widget->fieldLang('description') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="product-slide slide-bg vw-100 overflow-hidden">
        @foreach ($widget['module']['posts'] as $post)
            <div class="slide-bg-item slide-item position-relative">
                <div class="slide-img thumb">
                    <img src="{{ $post['bannerSrc'] }}" alt="" class="thumb" data-rellax loading="lazy"
                        data-rellax-speed="-4" data-rellax-percentage=".5">
                    <div class="bg-overlay"></div>
                </div>
                <div class="slide-caption content-wrap d-flex flex-column vw-100 anim-scroll-up" data-aos>
                    <div class="container my-xl-auto">
                        <div class="row g-0">
                            <div class="col-lg-6">
                                <div class="caption-inner">
                                    <div class="main-title mb-5">
                                        <h1 class="title fw-700 title-display-1 text-white text-uppercase line-height-sm split-text"
                                            data-aos>{!! $post->fieldLang('title') !!}</h1>
                                    </div>
                                    <div class="caption-text text-muted anim-scroll-up delay-300 mb-5" data-aos>
                                        <div class="clamp-3">
                                            @if (isset($post->intro_banner) && !empty($post->fieldLang('intro_banner')))
                                            {!! $post->fieldLang('intro_banner') !!}
                                            @else
                                            {!! $post->fieldLang('intro') !!}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="caption-btn anim-scroll-up delay-400" data-aos>
                                        <a href="{{ route('content.post.read.' . $post->section->slug, ['slugPost' => $post->slug]) }}"
                                            class="btn btn-danger">
                                            <div class="label-btn subtitle">@lang('text.discover_now')</div><i
                                                class="fa-light fa-arrow-right-long ms-3"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="slide-thumb-wrapper">
        <div class="container">
            <div class="product-slide slide-thumb anim-scroll-up delay-300" data-aos>
                @foreach ($widget['module']['posts'] as $post)
                    @if ($post['position'] == 1)
                        <div class="slide-item product-item overflow-hidden position-relative">
                            <div class="ratio ratio-1x1">
                                <img src="{{ !empty($post['logo_banner']['filepath'])?$post['logoBannerSrc']:$post['coverSrc']}}" alt="" class="thumb object-fit-contain" loading="lazy">
                            </div>
                        </div>
                    @else
                        <div class="product-item overflow-hidden position-relative">
                            <div class="ratio ratio-1x1">
                                <img src="{{ !empty($post['logo_banner']['filepath'])?$post['logoBannerSrc']:$post['coverSrc']}}" alt="" class="thumb object-fit-contain" loading="lazy">
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</section>
