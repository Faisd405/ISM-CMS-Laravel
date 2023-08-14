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
            <div class="title-heading">
                <h1>Organisation</h1>
            </div>
            <div class="list-breadcrumb">
                <ul>
                    <li><a href="">Home</a></li>
                    <li class="current">organisation</a></li>
                </ul>
            </div>
        </div>
        @foreach ($data['read']['categories']->sortBy('position') as $key => $category)
        <div class="box-section">
            <div class="title-heading collapse-btn"  data-toggle="collapse" data-target="#toggle-btn-{{ $key }}">
                <h4>
                    {{ $category->fieldLang('name') }}
                </h4>
            </div>
            <div id="toggle-btn-{{ $key }}" class="collapse" aria-expanded="false">
                <div class="row">
                    @foreach ($category->posts()->orderBy('position', 'asc')->get() as $post)
                    <div class="col-lg-6 col-xl-4">
                        <div class="item-identity">
                            <div class="top-identity">
                                <div class="img-identity">
                                    <div class="thumbnail-img">
                                        <img src="{{ $post['cover_src'] }}" alt="">
                                    </div>
                                </div>
                                <div class="name-identity">
                                    <h4>{{ $post->fieldLang('title') }}</h4>
                                    <span>
                                        {{ $post['addon_fields']['jabatan'] ?? '-' }}
                                    </span>
                                </div>
                            </div>
                            <div class="bottom-identity">
                                <div class="email-indentity">
                                    <a href="{{ !empty($post['addon_fields']['email']) ? 'mailto:' . $post['addon_fields']['email'] : 'javascript:void(0)' }}" class="btn btn-primary btn-small btn-icon icon-r">
                                        Send Email <i class="las la-envelope"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="thumbnail-img">
        <img src="{{ $data['read']['cover_src'] }}" alt="">
    </div>
</div>
@endsection
