@extends('layouts.frontend.layout')

@section('content')
@if (!empty($data['read']['content_template']))
    {!! $data['read']['content_template'] !!}
@else
{{-- DETAIL

    DATA :
    {!! $data['read']->fieldLang('title') !!} // title
    
    @if ($data['read']['config']['hide_body'] == false)
    {!! $data['read']->fieldLang('body') !!} //body
    @endif

    {!! $data['read']->fieldLang('after_body') !!} //after_body

    @if ($data['read']['config']['hide_banner'] == false) //banner
    <img src="{{ $data['banner'] }}" title="{{ $data['read']['banner']['title'] }}" alt="{{ $data['read']['banner']['alt'] }}">
    @endif

    DATA LOOPING :
    $data['read']['fields']
    $data['read']['custom_fields']

    {!! $data['creator'] !!}
    
--}}
@endif
@endsection