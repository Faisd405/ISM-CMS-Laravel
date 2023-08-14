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
@endsection