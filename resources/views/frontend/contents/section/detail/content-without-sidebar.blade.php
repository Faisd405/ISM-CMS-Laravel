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
                        <li class="current">
                            {{ $data['section']->fieldLang('name') }}
                        </li>
                    </ul>
                </div>
            </div>
            <div class="box-content">
                <div class="post-info">
                    <span class="post-cat">{{ !empty($data['read']['addon_fields']['perusahaan']) ? $data['read']['addon_fields']['perusahaan'] : '-' }}</span>
                </div>
                <div class="title-heading">
                    <h1>
                        {{ $data['read']->fieldLang('title') }}
                    </h1>
                </div>
                <div class="box-img mb-4">
                    <img src="{{ $data['read']['cover_src'] }}" alt="">
                </div>
                <div class="box-entry">
                    <article>
                        {!! $data['read']->fieldLang('content') !!}
                    </article>
                </div>
                <div class="box-btn d-flex justify-content-between mb-4 mb-lg-0">
                    <a href="{{ route('content.section.read.'.$data['read']['section']->slug) }}" class="btn btn-primary">Go Back</a>
                    @if (!empty($data['read']['addon_fields']['link']))
                    <a href="{{ $data['read']['addon_fields']['link'] }}" class="btn btn-primary">Kunjungi Pengiklan</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="thumbnail-img">
            <img src="{{ $data['cover'] }}" alt="">
        </div>
    </div>
@endsection

@section('jsbody')
<script>
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
