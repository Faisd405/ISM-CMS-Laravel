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
    $data['read']['posts']
    $data['read']['fields']

    {!! $data['creator'] !!}

--}}
    <div class="box-wrap banner-breadcrumb">
        <div class="container">
            <div class="banner-content">
                <div class="title-heading">
                    <h1>Investment Trend</h1>
                </div>
                <div class="list-breadcrumb">
                    <ul>
                        <li><a href="">Home</a></li>
                        <li class="current">Investment Trend</a></li>
                    </ul>
                </div>
            </div>
            <div class="box-content">
                <div class="row">
                    <div class="col-xl-9">
                        @foreach ($data['posts'] as $post)
                        <a href="{{ route('content.post.read.' . $post->section->slug, ['slugPost' => $post->slug]) }}" class="item-review">
                            <div class="title-review">
                                <h5>{{ $post->fieldLang('title') }}</h5>
                            </div>
                            <div class="btn btn-primary btn-small btn-icon icon-r">
                                View More <i class="las la-eye"></i>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    <div class="col-xl-3">
                        <x-frontend.sidenav.category :section="$data['section']" :categories="$data['section']
                            ->categories()
                            ->whereNotIn('slug', [$data['read']['slug']])
                            ->get()">
                        </x-frontend.sidenav.category>
                    </div>
                </div>
            </div>
        </div>
        <div class="thumbnail-img">
            <img src="{{ $data['read']['cover_src'] }}" alt="">
        </div>
    </div>
@endsection
