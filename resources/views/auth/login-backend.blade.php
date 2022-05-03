@extends('layouts.backend.layout-auth')

@if (config('cms.setting.recaptcha') == true)    
@section('jshead')
{!! htmlScriptTagJsApi() !!}
@endsection
@endif

@section('content')
<h5 class="text-center text-muted font-weight-normal mb-4">@lang('auth.login_backend.text')</h5>
<!-- Form -->
<form class="my-2" action="{{ route('login') }}" method="POST">
  @csrf

  @if ($data['failed_logins']->count() >= config('cms.module.auth.login.backend.lock_warning'))
  <div class="alert alert-warning alert-dismissible fade show">
    <button type="button" class="close" data-dismiss="alert">×</button>
    @lang('auth.lock_warning', [
      'attr_failed' => $data['failed_logins']->count(),
      'attr_failed_def' => config('cms.module.auth.login.backend.lock_total'),
      'attr_hour' => config('cms.module.auth.login.backend.lock_time'),
    ])
  </div>
  @endif

  <div class="form-group">
    <label class="form-label">@lang('auth.login_backend.label.field1')</label>
    <input type="text" 
      class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}"
      placeholder="@lang('auth.login_backend.placeholder.field1')" autofocus>
    @include('components.field-error', ['field' => 'username'])
  </div>
  <div class="form-group">
    <label class="form-label d-flex justify-content-between align-items-end">
      <div>@lang('auth.login_backend.label.field2')</div>
      @if (config('cms.module.auth.forgot_password.active') == true)
      <a href="{{ route('password.email') }}" class="d-block small" title="@lang('auth.forgot_password.title') ?">
        @lang('auth.forgot_password.title') ?
      </a>
      @endif
    </label>
    <div class="input-group">
      <input type="password" id="password-field" class="form-control @error('password') is-invalid @enderror" 
        name="password" placeholder="@lang('auth.login_backend.placeholder.field2')">
      <div class="input-group-append">
          <span toggle="#password-field" class="input-group-text toggle-password fas fa-eye"></span>
      </div>
      @include('components.field-error', ['field' => 'password'])
    </div>
  </div>
  @if (config('recaptcha.version') == 'v2')
  <div class="form-group">
    {!! htmlFormSnippet() !!}
    @error('g-recaptcha-response')
    <p style="color:red;">{{ $message }}<p>
    @enderror
  </div>
  @endif
  <div class="d-flex justify-content-between align-items-center m-0">
    @if (config('cms.module.auth.login.remember') == true)
    <label class="custom-control custom-checkbox m-0">
      <input type="checkbox" class="custom-control-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
      <span class="custom-control-label">@lang('auth.login_backend.label.field3')</span>
    </label>
    @endif
    <button type="submit" style="background: #0084ff;" class="btn btn-primary" title="@lang('auth.login_backend.label.signin')">
      @lang('auth.login_backend.label.signin')
    </button>
  </div>

</form>
<!-- / Form -->
@endsection

@section('content-footer')
<a href="{{ route('home') }}" target="_blank" title="@lang('global.view_frontend')">
    @lang('global.view_frontend')
    <i class="las la-external-link-alt ml-1" style="font-size: 1.3em;"></i>
  </a>
@endsection

@section('jsbody')
<script>
  $(".toggle-password").click(function() {
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
