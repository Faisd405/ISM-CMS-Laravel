@extends('layouts.frontend.layout')

@if (config('cms.setting.recaptcha') == true)    
@section('jshead')
{!! htmlScriptTagJsApi() !!}
@endsection
@endif

@section('content')
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
@endsection