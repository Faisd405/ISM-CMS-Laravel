@extends('layouts.backend.layout-auth')

@section('jshead')
@if (config('cms.setting.recaptcha') == true)    
    {!! htmlScriptTagJsApi() !!}
@endif
@endsection

@section('content')
<!-- Text -->
<div class="title-heading text-center">
    <h3>@lang('auth.login_backend.text')</h3>
</div>

<!-- Form -->
<form class="form-box" action="{{ route('login') }}" method="POST">
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
        <div class="input-alt w-icon">
            <label class="form-label" for="username">
                @lang('auth.login_backend.label.username')
            </label>
            <input id="username" type="text" class="form-control text-bolder @error('username') is-invalid @enderror" 
                name="username" value="{{ old('username') }}"
                placeholder="@lang('auth.login_backend.placeholder.username')">
            <i class="fi fi-br-user"></i>
        </div>
        @include('components.field-error', ['field' => 'username'])
    </div>
    <div class="form-group">
        <div class="input-alt w-icon">
            <label class="form-label" for="password">
                @lang('auth.login_backend.label.password')
            </label>
            <input id="password" type="password" class="form-control text-bolder @error('password') is-invalid @enderror"
                name="password" placeholder="@lang('auth.login_backend.placeholder.password')">
            <i class="toggle-password fi fi-br-eye" toggle="#password"></i>
        </div>
        @include('components.field-error', ['field' => 'password'])
    </div>
    @if (config('recaptcha.version') == 'v2')
    <div class="form-group">
        {!! htmlFormSnippet() !!}
        @error('g-recaptcha-response')
            <label class="error jquery-validation-error small form-text invalid-feedback">
                {!! $message !!}
            </label>
        @enderror
    </div>
    @endif
    <div class="form-group row">
        <div class="col-md-6">
            @if (config('cms.module.auth.login.remember') == true)
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input form-check-input"
                    name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="custom-control-label form-check-label" for="remember">
                    @lang('auth.login_backend.label.remember')
                </label>
            </div>
            @endif
        </div>
        <div class="col-md-6 text-right">
            @if (config('cms.module.auth.forgot_password.active') == true)
            <a href="{{ route('password.email') }}" class="font-weight-semibold" title="@lang('auth.forgot_password.title') ?">
                @lang('auth.forgot_password.title') ?
            </a>
            @endif
        </div>
    </div>

    <button type="submit" class="btn btn-main btn-xl w-100 mt-3" title="@lang('auth.login_backend.label.signin')">
        @lang('auth.login_backend.label.signin')
    </button>
</form>
<!-- / Form -->
<div class="d-flex justify-content-center">
    <a href="{{ route('home') }}" target="_blank" title="@lang('global.view_frontend')">
        @lang('global.view_frontend')
    </a>
</div>
@endsection

@section('jsbody')
<!-- Show Password -->
<script>
    $(".toggle-password").click(function () {

        $(".toggle-password").toggleClass("looked");

        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });
</script>
@endsection
