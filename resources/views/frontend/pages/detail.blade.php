@extends('layouts.frontend.layout')

@section('content')
    {{-- DETAIL

    DATA :
    {!! $data['read']->fieldLang('title') !!} // title

    @if ($data['read']['config']['show_intro'] == true)
    {!! $data['read']->fieldLang('intro') !!} //intro
    @endif

    @if ($data['read']['config']['show_content'] == true)
    {!! $data['read']->fieldLang('content') !!} //intro
    @endif

    @if ($data['read']['config']['show_cover'] == true) //cover
    <img src="{{ $data['cover'] }}" title="{{ $data['read']['cover']['title'] }}" alt="{{ $data['read']['cover']['alt'] }}">
    @endif

    @if ($data['read']['config']['show_banner'] == true) //banner
    <img src="{{ $data['banner'] }}" title="{{ $data['read']['banner']['title'] }}" alt="{{ $data['read']['banner']['alt'] }}">
    @endif

    DATA LOOPING :
    $data['read']['childs']
    $data['read']['medias']
    $data['read']['fields']
    @if ($data['read']['config']['show_tags'] == true)
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
            <div class="title-heading">
                <h1>{{ $data['read']->fieldLang('title') }}</h1>
            </div>
            <div class="list-breadcrumb">
                <ul>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li class="current">{{ $data['read']->fieldLang('title') }}</a></li>
                </ul>
            </div>
        </div>
        <div class="box-content">
            <div class="row">
                <div class="col-xl-9">
                    <div class="box-entry">
                        <article>
                            {!! $data['read']->fieldLang('content') !!}
                        </article>
                    </div>
                </div>
                <div class="col-xl-3">
                    <div class="item-sidenav">
                        <ul>
                            @if ($parentPage = $data['read']->getParent())
                            @foreach ($parentPage->childs()->where('slug', '!=', $data['read']->slug)->get() as $child)
                            <li class="menu-sidenav">
                                <a href="{{route('page.read.child.'. $child->slug)}}">
                                    <span class="sn-title">{{$child->fieldLang('title')}}</span>
                                    <span class="sn-icon"><i class="las la-arrow-right"></i></span>
                                </a>
                            </li>
                            @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thumbnail-img">
        <img src="{{ $data['cover'] }}" alt="">
    </div>
</div>
@endsection
