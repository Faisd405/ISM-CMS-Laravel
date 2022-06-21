@extends('errors.custom-layout')

@section('title', __('global.errors.429.title'))

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/errors/css/error.css') }}">
@endsection

@section('layout-content')
<div class="layout-content">

  <!-- Content -->
  <div class="container-fluid flex-grow-1 container-p-y">

    <div class="container px-0">
      <h2 class="text-center font-weight-bolder pt-5">
          429 <br>
          @lang('global.errors.429.title')
      </h2>
      <div class="text-center text-muted text-big mx-auto mt-3" style="max-width: 500px;">
          @lang('global.errors.429.text')

      </div>
      <div class="text-center mt-4">
        <a href="{{ route('home') }}" class="btn btn-outline-dark">←&nbsp; @lang('global.back')</a>
        {{-- <button type="button" onclick="goBack()" class="btn btn-outline-dark">←&nbsp; @lang('global.back')</button> --}}
      </div>
    </div>

  </div>
  <!-- / Content -->

</div>
@endsection

@section('jsbody')
<script>
    function goBack() {
      window.history.back();
    }
</script>
@endsection
