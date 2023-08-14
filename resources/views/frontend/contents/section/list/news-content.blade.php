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

<div class="box-wrap banner-breadcrumb">
    <div class="container">
        <div class="banner-content">
            <div class="row justify-content-between align-items-center">
                <div class="col-xl-6">
                    <div class="title-heading">
                        <h1>{{ $data['read']->fieldLang('name') }}</h1>
                    </div>
                    <div class="list-breadcrumb">
                        <ul>
                            <li><a href="">Home</a></li>
                            <li class="current">{{ $data['read']->fieldLang('name') }}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-3">
                    <x-frontend.archive-year
                        :sectionSlug="$data['read']->slug"
                        :listArchiveYear="$data['read']->listYearUnique()"
                    ></x-frontend.archive-year>
                </div>
            </div>

        </div>
        <div class="row flex-column-reverse flex-xl-row">
            <div class="col-xl-3">
                @php
                    // $platinum = App\Models\Module\Content\ContentCategory::where('slug', 'platinum-anggota')->orderBy('position', 'asc')->limit(5)->get();
                    // $gold = App\Models\Module\Content\ContentCategory::where('slug', 'gold-anggota')->orderBy('position', 'asc')->limit(5)->get();
                @endphp
                <x-frontend.sidenav.sponsor-platinum></x-frontend.sidenav.sponsor-platinum>

                <x-frontend.sidenav.sponsor-gold></x-frontend.sidenav.sponsor-gold>
            </div>
            <div class="col-xl-9">
                <div class="row justify-content-center">
                    @foreach ($data['posts'] as $post)
                        <div class="col-md-6">
                            <a href="{{ route('content.post.read.' . $post->section->slug, ['slugPost' => $post->slug]) }}" class="item-news">
                                <div class="box-img">
                                    <div class="thumbnail-img">
                                        <img src="{{ $post['cover_src'] }}" alt="">
                                    </div>
                                </div>
                                <div class="box-post">
                                    <div class="post-info">
                                        <span class="post-date">{{ $post['created_at']->format('d F Y')}}</span>
                                        @if ($post->categories()->count() > 0)
                                        <span class="post-cat">
                                            @foreach ($post->categories() as $category)
                                                {{ $category->fieldLang('name') }}
                                            @endforeach
                                        </span>
                                        @endif
                                    </div>
                                    <h4 class="title-post">
                                        {{ $post->fieldLang('title') }}
                                    </h4>
                                    <article class="summary-content">
                                        {!! $post->fieldLang('intro') !!}
                                    </article>
                                    <div href="{{ route('content.post.read.' . $post->section->slug, ['slugPost' => $post->slug]) }}" class="line-btn justify-content-end">
                                        <span>Read More</span>
                                        <span class="lb-icon"><i class="las la-plus"></i></span>
                                    </div>

                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                @if ($data['read']['config']['paginate_post'])
                <nav aria-label="Page navigation">
                    {{ $data['posts']->links('vendor.pagination.frontend') }}
                </nav>
                @endif
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
        link.html(text+icon);
        list.slideToggle(200);
        if (text === '* Reset') {
          link.html('Select one option'+icon);
        }
      });
    });
</script>
@endsection
