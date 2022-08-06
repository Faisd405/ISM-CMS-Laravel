@extends('layouts.backend.layout-auth')

@section('content')
<!-- Text -->
<div class="title-heading text-center">
    <h3>@lang('auth.reset_password.title')</h3>
</div>

<!-- Form -->
<form class="form-box" action="{{ route('password.update') }}" method="POST">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email ?? old('email') }}" 
        required autocomplete="email">

    <div class="form-group">
        <div class="input-alt w-icon">
            <label class="form-label" for="password">
                @lang('auth.reset_password.label.password')
            </label>
            <input id="password" type="password" class="form-control text-bolder @error('password') is-invalid @enderror"
                name="password" placeholder="@lang('auth.reset_password.placeholder.password')">
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
                @lang('auth.reset_password.label.password_confirmation')
            </label>
            <input id="password_confirmation" type="password" class="form-control text-bolder @error('password_confirmation') is-invalid @enderror"
                name="password_confirmation" placeholder="@lang('auth.reset_password.placeholder.password_confirmation')">
            <i class="toggle-password_confirmation fi fi-br-eye" toggle="#password_confirmation"></i>
        </div>
        @include('components.field-error', ['field' => 'password_confirmation'])
    </div>

    <button type="submit" class="btn btn-main btn-xl w-100 mt-3" title="@lang('auth.reset_password.title')">
        @lang('auth.reset_password.title')
    </button>
</form>
<!-- / Form -->
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
