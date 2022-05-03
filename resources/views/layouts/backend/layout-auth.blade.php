@extends('layouts.backend.application')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/pages/authentication.css') }}">
@endsection

@section('layout-content')
<div class="authentication-wrapper authentication-2 ui-bg-cover ui-bg-overlay-container px-4" 
  style="background-image: url('{{ asset(config('cms.files.banner_login')) }}');">
  <div class="ui-bg-overlay bg-dark opacity-25"></div>

  <div class="authentication-inner py-5">

    <div class="card">
      <div class="p-4 p-sm-5">
        <!-- Logo -->
        <div class="d-flex justify-content-center align-items-center pb-2 mb-1">
          <div class="ui-w-70">
            <div class="w-100 position-relative">
              <img src="{!! $config['logo'] !!}" 
                style="width: 100px;" 
                alt="{{ $config['website_name'] }}" 
                title="{{ $config['website_name'] }}">
            </div>
          </div>
        </div>
        <!-- / Logo -->

        @yield('content')

      </div>
      <div class="card-footer py-3 px-4 px-sm-5">
        <div class="text-center text-muted">
          @yield('content-footer')
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
