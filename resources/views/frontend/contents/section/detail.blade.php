@extends('layouts.frontend.layout')

@section('content')
@isset ($data['read']['templateDetail']['content_template'])
    {!! $data['read']['templateDetail']['content_template'] !!}
@else
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
@endisset
@endsection