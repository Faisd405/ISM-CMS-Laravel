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
    $data['read']['childs']
    $data['read']['medias']
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
@endsection