@extends('layouts.backend.layout-auth')

@section('jshead')
@if (config('cms.setting.recaptcha') == true)    
    {!! htmlScriptTagJsApi() !!}
@endif
@endsection

@section('content')
<!-- Text -->
<div class="title-heading text-center">
    <h3>@lang('auth.register.title')</h3>
    <p>@lang('auth.register.text')</p>
</div>

<!-- Form -->
<form class="form-box" action="{{ route('register') }}" method="POST">
    @csrf

    @php
        $start = $data['register']['start_date'];
        $end = $data['register']['end_date'];
        $now = now()->format('Y-m-d H:i');
    @endphp
    @if (!empty($start) && $now < $start->format('Y-m-d H:i') || !empty($end) && $now > $end->format('Y-m-d H:i'))
        @if (!empty($start) && $now < $start->format('Y-m-d H:i'))
        <div class="alert alert-warning alert-dismissible">
            @lang('auth.register.label.form_open', [
                'attribute' => $start->format('d F Y H:i (A)')
            ])
        </div>
        @endif
        @if (!empty($end) && $now > $end->format('Y-m-d H:i'))
        <div class="alert alert-warning alert-dismissible">
            @lang('auth.register.label.form_close')
        </div>
        @endif
    @endif

    <div class="form-group">
        <div class="input-alt w-icon">
            <label class="form-label" for="name">
                @lang('auth.register.label.name')
            </label>
            <input id="name" type="text" class="form-control text-bolder @error('name') is-invalid @enderror" 
                name="name" value="{{ old('name') }}"
                placeholder="@lang('auth.register.placeholder.name')">
            <i class="fi fi-rr-portrait"></i>
        </div>
        @include('components.field-error', ['field' => 'name'])
    </div>
    <div class="form-group">
        <div class="input-alt w-icon">
            <label class="form-label" for="email">
                @lang('auth.register.label.email')
            </label>
            <input id="email" type="text" class="form-control text-bolder @error('email') is-invalid @enderror" 
                name="email" value="{{ old('email') }}"
                placeholder="@lang('auth.register.placeholder.email')">
            <i class="fi fi-rr-envelope"></i>
        </div>
        @include('components.field-error', ['field' => 'email'])
    </div>
    <div class="form-group">
        <div class="input-alt w-icon">
            <label class="form-label" for="phone">
                @lang('auth.register.label.phone')
            </label>
            <input id="phone" type="text" class="form-control text-bolder @error('phone') is-invalid @enderror" 
                name="phone" value="{{ old('phone') }}"
                placeholder="@lang('auth.register.placeholder.phone')">
                <i class="fi fi-rr-phone-call"></i>
        </div>
        @include('components.field-error', ['field' => 'phone'])
    </div>
    <div class="form-group">
        <div class="input-alt w-icon">
            <label class="form-label" for="username">
                @lang('auth.register.label.username')
            </label>
            <input id="username" type="text" class="form-control text-bolder @error('username') is-invalid @enderror" 
                name="username" value="{{ old('username') }}"
                placeholder="@lang('auth.register.placeholder.username')">
            <i class="fi fi-br-user"></i>
        </div>
        @include('components.field-error', ['field' => 'username'])
        <small class="form-text font-weight-semibold">
            @lang('module/user.username_info')
        </small>
    </div>
    <div class="form-group">
        <div class="input-alt w-icon">
            <label class="form-label" for="password">
                @lang('auth.register.label.password')
            </label>
            <input id="password" type="password" class="form-control text-bolder @error('password') is-invalid @enderror"
                name="password" placeholder="@lang('auth.register.placeholder.password')">
            <i class="toggle-password fi fi-br-eye" toggle="#password"></i>
        </div>
        @include('components.field-error', ['field' => 'password'])
        <small class="form-text font-weight-semibold">
            @lang('module/user.password_info')
        </small>
    </div>
    <div class="form-group">
        <div class="input-alt w-icon">
            <label class="form-label" for="password_confirmation">
                @lang('auth.register.label.password_confirmation')
            </label>
            <input id="password_confirmation" type="password" class="form-control text-bolder @error('password_confirmation') is-invalid @enderror"
                name="password_confirmation" placeholder="@lang('auth.register.placeholder.password_confirmation')">
            <i class="toggle-password_confirmation fi fi-br-eye" toggle="#password_confirmation"></i>
        </div>
        @include('components.field-error', ['field' => 'password_confirmation'])
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
    @if (config('cms.module.auth.register.agree') == true)
    <div class="form-group">
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input form-check-input"
                name="agree" id="agree" {{ old('agree') ? 'checked' : '' }}>
            <label class="custom-control-label form-check-label" for="agree">
                @lang('auth.register.label.agree')
            </label>
        </div>
    </div>
    @endif

    <button type="submit" class="btn btn-main btn-xl w-100 mt-3" title="@lang('auth.register.label.signup')">
        @lang('auth.register.label.signup')
    </button>
</form>
<!-- / Form -->
<div class="d-flex justify-content-center">
    @lang('auth.register.label.already_account') &nbsp;
    <a href="{{ route('login.frontend') }}" title="@lang('auth.login_frontend.title')">
        @lang('auth.login_frontend.title')
    </a>
</div>
@endsection

@section('jsbody')
<!-- Show Password -->
<script>
    $(".toggle-password, .toggle-password_confirmation").click(function () {

        $(this).toggleClass("looked");

        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });
</script>
@endsection