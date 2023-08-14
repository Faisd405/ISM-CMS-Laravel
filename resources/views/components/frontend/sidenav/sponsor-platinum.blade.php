<div class="item-sidenav">
    <div class="news-section news-sponsor">
        <div class="title-heading">
            <h6>{{ $advertisingContent->fieldLang('name') }}</h6>
            <h2>Sponsor Platinum</h2>
        </div>
        <div class="swiper-button-next sbn-2">
            <i class="las la-angle-up"></i>
        </div>
        <div class="swiper-button-prev sbp-2">
            <i class="las la-angle-down"></i>
        </div>
        <div class="swiper-container platinum-slider">
            <div class="swiper-wrapper">
                @foreach ($advertisingPosts as $advertisingPost)
                    <div class="swiper-slide">
                        <a href="{{ route('content.post.read.' . $advertisingPost->section->slug, ['slugPost' => $advertisingPost->slug]) }}"
                            class="item-banner">
                            <div class="box-img">
                                <img src="{{ asset($advertisingPost['cover_src']) }}" alt="">
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

        </div>
        <div class="box-btn text-center">
            <a href="{{ route('content.section.read.' . $advertisingContent['slug']) }}"
                class="btn btn-primary">
                {{ $advertisingContent->fieldLang('name') }}
            </a>
        </div>
    </div>
</div>
