@extends('layouts.frontend.layout')

@section('content')
{{-- DETAIL

    DATA :
    {!! $data['read']->fieldLang('name') !!} // name
    
    @if ($data['read']['config']['hide_description'] == false)
    {!! $data['read']->fieldLang('description') !!} //description
    @endif

    //image preview
    <img src="{{ $data['image_preview'] }}" title="{{ $data['read']['image_preview']['title'] }}" alt="{{ $data['read']['image_preview']['alt'] }}">

    @if ($data['read']['config']['hide_banner'] == false) //banner
    <img src="{{ $data['banner'] }}" title="{{ $data['read']['banner']['title'] }}" alt="{{ $data['read']['banner']['alt'] }}">
    @endif

    DATA LOOPING :
    $data['medias']
    $data['read']['fields']

    {!! $data['creator'] !!}
--}}
@endsection