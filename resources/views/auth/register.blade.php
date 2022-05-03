@extends('layouts.backend.layout-auth')

@if (config('cms.setting.recaptcha') == true)    
@section('jshead')
{!! htmlScriptTagJsApi() !!}
@endsection
@endif

@section('content')
<h5 class="text-center text-muted font-weight-normal mb-4">@lang('auth.register.text')</h5>

@php
    $start = $data['register']->start_date;
    $end = $data['register']->end_date;
    $now = now()->format('Y-m-d H:i');
@endphp
@if (empty($start) || !empty($start) && $now >= $start->format('Y-m-d H:i') && $now <= $end->format('Y-m-d H:i'))
<!-- Form -->
<form class="my-2" action="{{ route('register') }}" method="POST">
    @csrf

    <div class="form-group">
        <label class="form-label">@lang('auth.register.label.field1')</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" 
          value="{{ old('name') }}" placeholder="@lang('auth.register.placeholder.field1')" autofocus>
        @include('components.field-error', ['field' => 'name'])
    </div>
    <div class="form-group">
        <label class="form-label">@lang('auth.register.label.field2')</label>
        <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" 
          value="{{ old('email') }}" placeholder="@lang('auth.register.placeholder.field2')" autofocus>
        @include('components.field-error', ['field' => 'email'])
    </div>
    <div class="form-group">
        <label class="form-label">@lang('auth.register.label.field6')</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">+62</span>
            </div>
            <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" 
                value="{{ old('phone') }}"placeholder="@lang('auth.register.placeholder.field6')" autofocus>
            @include('components.field-error', ['field' => 'phone'])
        </div>
    </div>
    <div class="form-group">
        <label class="form-label">
            @lang('auth.register.label.field3') 
            <i class="las la-info-circle" data-toggle="popover" data-placement="right" 
                data-content="@lang('module/user.username_info')" title="Info">
            </i>
        </label>
        <input type="text" class="form-control @error('username') is-invalid @enderror"  name="username" 
          value="{{ old('username') }}" placeholder="@lang('auth.register.placeholder.field3')" autofocus>
        @include('components.field-error', ['field' => 'username'])
    </div>
    <div class="form-group">
        <label class="form-label">
            @lang('auth.register.label.field4')
            <i class="las la-info-circle"  data-toggle="popover" data-placement="right" 
                data-content="@lang('module/user.password_info')" title="Info">
            </i>
        </label>
        <div class="input-group">
            <input type="password" id="password-field" class="form-control @error('password') is-invalid @enderror" 
              name="password" placeholder="@lang('auth.register.placeholder.field4')">
            <div class="input-group-append">
                <span toggle="#password-field" class="input-group-text toggle-password fas fa-eye"></span>
            </div>
            @include('components.field-error', ['field' => 'password'])
        </div>
    </div>
    <div class="form-group">
        <label class="form-label">@lang('auth.register.label.field5')</label>
        <div class="input-group">
            <input type="password" id="password-field-confrimation" class="form-control @error('password_confirmation') is-invalid @enderror" 
              name="password_confirmation" placeholder="@lang('auth.register.placeholder.field5')">
            <div class="input-group-append">
                <span toggle="#password-field-confrimation" class="input-group-text toggle-password-confirmation fas fa-eye"></span>
            </div>
            @include('components.field-error', ['field' => 'password_confirmation'])
        </div>
    </div>
    @if (config('cms.module.auth.register.agree') == true)
    <div class="form-group">
        <label class="custom-control custom-checkbox m-0">
            <input type="checkbox" class="custom-control-input" name="agree" 
              id="agree" {{ old('agree') ? 'checked' : '' }}>
            <span class="custom-control-label">@lang('auth.register.label.agree')</span>
        </label>
        @error('agree')
        <small style="color: red;">{{ $message }}</small> 
        @enderror
    </div>
    @endif
    <div class="d-flex justify-content-between align-items-center m-0">
        <button type="submit" style="background: #0084ff;" class="btn btn-primary btn-block" 
            title="@lang('auth.register.label.signup')">
            @lang('auth.register.label.signup')
        </button>
    </div>

</form>
@else
    @if ($now < $start->format('Y-m-d H:i'))
    <div class="alert alert-warning alert-dismissible">
        @lang('feature/alert.form_open')
    </div>
    @endif
    @if ($now > $end->format('Y-m-d H:i'))
    <div class="alert alert-warning alert-dismissible">
        @lang('feature/alert.form_close')
    </div>
    @endif
@endif
@endsection

@section('content-footer')
@lang('auth.register.label.already_account')
<a href="{{ route('login.frontend') }}" title="@lang('auth.login_frontend.title')">
    @lang('auth.login_frontend.title')
</a>
@endsection

@section('scripts')
<script src="{{ asset('assets/backend/js/ui_tooltips.js') }}"></script>
@endsection

@section('jsbody')
<script>
  $(".toggle-password, .toggle-password-confirmation").click(function() {
      $(this).toggleClass("fa-eye fa-eye-slash");
      
      var input = $($(this).attr("toggle"));
      if (input.attr("type") == "password") {
        input.attr("type", "text");
      } else {
        input.attr("type", "password");
      }
  });
</script>
@endsection