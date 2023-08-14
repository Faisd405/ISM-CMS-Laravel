@extends('layouts.frontend.layout')

@section('content')
    {{-- DETAIL

    DATA :
    {!! $data['read']->fieldLang('title') !!} // name
    {!! $data['read']->fieldLang('description') !!} // description
    {{ $data['read']['file'] }} //data

    Download :
    {{ route('document.download', ['id' => $data['file']['id']]) }}

    Document :
    $data['document']
--}}

    <div class="box-wrap banner-breadcrumb">
        <div class="container">
            <div class="banner-content">
                <div class="title-heading">
                    <h1>
                        {{ $data['read']->fieldLang('title') ?? basename($data['read']['file']) }}
                    </h1>
                </div>
                <div class="list-breadcrumb">
                    <ul>
                        <li><a href="">Home</a></li>
                        <li>
                            <a href="{{ route('document.read', ['slugDocument' => $data['document']['slug']]) }}">
                                {{ $data['document']->fieldLang('name') }}
                            </a>
                        </li>
                        <li class="current">
                            {{ $data['read']->fieldLang('title') ?? basename($data['read']['file']) }}
                        </li>
                    </ul>
                </div>
            </div>
            <div class="box-content">
                <div class="box-entry">
                    <iframe src="{{ $data['read']['file'] }}" frameborder="no" style="width:100%;height:850px;"></iframe>
                </div>
            </div>
        </div>
        <div class="thumbnail-img">
            <img src="{{ $data['read']['cover_src'] }}" alt="">
        </div>
    @endsection
