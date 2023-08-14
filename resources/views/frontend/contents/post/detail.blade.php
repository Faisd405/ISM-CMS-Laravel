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
    <div class="box-wrap banner-breadcrumb">
        <div class="container">
            <div class="banner-content">
                <div class="list-breadcrumb">
                    <ul>
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li>
                            <a href="{{ route('content.section.read.' . $data['section']->slug) }}">
                                {{ $data['section']->fieldLang('name') }}
                            </a>
                        </li>
                        <li class="current">{{ $data['read']->fieldLang('title') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="box-content">
                <div class="row">
                    <div class="col-xl-9">
                        <div class="post-info">
                            <span class="post-date">
                                {{ $data['read']['created_at']->format('d F Y') }}
                            </span>
                            @if ($data['read']->categories()->count())
                                <span class="post-cat">
                                    @foreach ($data['read']->categories() as $category)
                                        <a
                                            href="{{ route('content.category.read.' . $data['section']->slug, ['slugCategory' => $category['slug']]) }}">
                                            {{ $category->fieldLang('name') }}
                                        </a>
                                    @endforeach
                                </span>
                            @endif
                        </div>
                        <div class="title-heading">
                            <h1>{{ $data['read']->fieldLang('title') }}</h1>
                        </div>
                        <div class="box-img mb-4">
                            <img src="images/slide-1.jpg" alt="">
                        </div>
                        <div class="box-entry">
                            <article>
                                {!! $data['read']->fieldLang('content') !!}
                            </article>
                        </div>
                        <div class="box-btn mb-4 mb-lg-0">
                            <a href="{{ route('content.section.read.'.$data['read']['section']->slug) }}" class="btn btn-primary">Go Back</a>
                        </div>
                    </div>
                    <div class="col-xl-3">
                        <x-frontend.sidenav
                            :section="$data['section']"
                            :categories="$data['section']
                                ->categories()
                                ->whereNotIn('slug', $data['read']->categories()->pluck('slug'))
                                ->get()"
                            :listArchiveYear="$data['section']->listYearUnique()"
                            />
                    </div>
                </div>
            </div>
        </div>
        <div class="thumbnail-img">
            <img src="{{ $data['read']['cover_src'] }}" alt="">
        </div>
    </div>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/frontend/css/swiper.min.css') }}">
@endsection

@section('jsbody')
    <script src="{{ asset('assets/frontend/js/swiper.min.js') }}"></script>

    <script>
        //SLIDER SPONSOR PLATINUM
        var swiper = new Swiper('.news-sponsor .platinum-slider', {
            direction: 'vertical',
            slidesPerView: 3,
            // slidesPerGroup: 3,
            loop: 'true',
            spaceBetween: 20,
            autoplay: {
                delay: 1500,
            },
            speed: 1500,
            navigation: {
                nextEl: '.sbn-2',
                prevEl: '.sbp-2',
            },
            breakpoints: {
                // when window width is <= 991.98px
                767.98: {
                    direction: 'horizontal',
                    slidesPerView: 1,
                },
                // when window width is <= 991.98px
                1198.98: {
                    direction: 'horizontal',
                    slidesPerView: 2,
                }
            }
        });

        //SLIDER SPONSOR GOLD
        var swiper = new Swiper('.news-sponsor .gold-slider', {
            direction: 'vertical',
            slidesPerView: 3,
            // slidesPerGroup: 3,
            loop: 'true',
            spaceBetween: 20,
            autoplay: {
                delay: 2000,
            },
            speed: 1500,
            navigation: {
                nextEl: '.sbn-3',
                prevEl: '.sbp-3',
            },
            breakpoints: {
                // when window width is <= 991.98px
                767.98: {
                    direction: 'horizontal',
                    slidesPerView: 1,
                },
                // when window width is <= 991.98px
                1198.98: {
                    direction: 'horizontal',
                    slidesPerView: 2,
                }
            }
        });


        $(function() {
            var list = $('.js-dropdown-list');
            var link = $('.js-link');
            link.click(function(e) {
                e.preventDefault();
                list.slideToggle(200);
            });
            list.find('li').click(function() {
                var text = $(this).html();
                var icon = '<i class="las la-folder-open"></i>';
                link.html(text + icon);
                list.slideToggle(200);
                if (text === '* Reset') {
                    link.html('Select one option' + icon);
                }
            });
        });
    </script>
@endsection
