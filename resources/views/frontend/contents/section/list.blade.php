@extends('layouts.frontend.layout')

@section('content')
@isset ($data['read']['templateList']['content_template'])
    {!! $data['read']['templateList']['content_template'] !!}
@else
{{-- LIST

    DATA :
    $data['banner'] // jika diperlukan banner default
    $data['sections']

    LOOPING :
    $data['sections']

    ATTRIBUTE DIDALAM LOOPING :
    contoh penulisan attribute ada di detail

--}}
@endisset
@endsection