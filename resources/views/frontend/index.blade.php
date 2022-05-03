@extends('layouts.frontend.layout')

@section('body-attr')
{{-- attribute body jika tiap halaman memiliki attribute tag body yang berbeda, jika sama tidak dibutuhkan --}}

@endsection

@section('styles')
{{-- css tambahan per halaman --}}

@endsection

@section('content')
{{-- isi content --}}

{{-- panggil file widget custom disini contoh : @include('frontend.widget.nama-file') --}}
@endsection

@section('scripts')
{{-- scripts tambahan per halaman --}}

@endsection

@section('jsbody')
{{-- js tambahan per halaman --}}

@endsection