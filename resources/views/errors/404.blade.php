@extends('errors.custom-layout')

@section('title', __('global.errors.404.title'))

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/errors/css/error.css') }}">
@endsection

@section('layout-content')
<div class="container d-flex align-items-center ui-mh-100vh text-white">
    <div class="row align-content-center align-items-start align-items-md-center w-100 py-5">

      <div class="col-md-6 order-2 order-md-1 text-md-left text-center">
        <h1 class="display-2 font-weight-bolder mb-4">@lang('global.errors.404.title')</h1>
        <div class="text-xlarge font-weight-light mb-5">@lang('global.errors.404.text')</div>
        <button type="button" onclick="goBack()" class="btn btn-outline-white">←&nbsp; @lang('global.back')</button>
      </div>

      <div class="col-md-6 order-1 order-md-2 text-center mb-5 mb-md-0">
        <div class="ui-device macbook w-100 rounded" style="max-width: 500px">
          <img class="device-img" src="{{ asset(config('cms.files.config.logo.file')) }}" alt style="width: 55%">
          {{-- <div class="device-content">
            <div class="error-device-content bg-primary">
              <div class="error-device-code text-white font-weight-bolder">404</div>
            </div>
          </div> --}}
        </div>
      </div>

    </div>
</div>
@endsection

@section('jsbody')
<script>
    function goBack() {
      window.history.back();
    }
</script>
@endsection
