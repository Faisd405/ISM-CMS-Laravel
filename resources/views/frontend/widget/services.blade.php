<section class="content-wrap pb-5" id="home-services" data-nav-color="dark">
    <div class="container">
        <div class="row g-0 align-items-center justify-content-center">
            <div class="col-lg-6">
                <div class="main-title text-center mb-5">
                    <div class="subtitle mb-4 mb-xl-5 split-text text-danger" data-aos>{{ $widget['module']['title'] }}</div>
                    <h1 class="title fw-700 title-display-1 text-uppercase line-height-sm split-text" data-aos>@lang('text.slogan')</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row g-0 list-services">
            @foreach ($widget['module']['childs'] as $keyChild => $child)
                <div class="col-12 col-xl">
                    <div class="service-item d-flex anim-scroll-up" data-aos>
                        <a href="{{route('page.read.child.' . $child['slug'])}}"
                            class="service-content d-flex flex-column justify-content-between flex-grow-1 bg-white">
                            <div class="service-icon align-self-end">
                                <img src="{{ $child['coverSrc'] }}" class="w-100"
                                    alt="">
                            </div>
                            <div class="service-title">
                                <h5 class="title text-uppercase fw-400">
                                    @foreach (explode(' ', $child->fieldLang('title')) as $key => $value)
                                        @if ($keyChild % 2 == 0)
                                            @if ($key == 0)
                                                <b>{!! $value !!}</b>
                                            @else
                                                {!! $value !!}
                                            @endif
                                        @else
                                            @if ($key == 1)
                                                <b>{!! $value !!}</b>
                                            @else
                                                {!! $value !!}
                                            @endif
                                        @endif
                                        <br />
                                    @endforeach
                                </h5>
                                <div class="service-excerpt clamp-2">
                                    {!! $child->fieldLang('intro') !!}
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
