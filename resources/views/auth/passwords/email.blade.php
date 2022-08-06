@extends('layouts.backend.layout-auth')

@section('content')
<!-- Text -->
<div class="title-heading text-center">
    <h3>@lang('auth.forgot_password.title')</h3>
    <p>@lang('auth.forgot_password.text')</p>
</div>

<!-- Form -->
<form class="form-box" action="{{ route('password.email') }}" method="POST">
    @csrf

    <div class="form-group">
        <div class="input-alt w-icon">
            <label class="form-label" for="email">
                @lang('auth.forgot_password.label.email')
            </label>
            <input id="email" type="text" class="form-control text-bolder @error('email') is-invalid @enderror" 
                name="email" value="{{ old('email') }}"
                placeholder="@lang('auth.forgot_password.placeholder.email')">
            <i class="fi fi-rr-envelope"></i>
        </div>
        @include('components.field-error', ['field' => 'email'])
    </div>

    <button type="submit" class="btn btn-main btn-xl w-100 mt-3" title="@lang('auth.forgot_password.label.send')">
        @lang('auth.forgot_password.label.send')
    </button>
</form>
<!-- / Form -->
<div class="d-flex justify-content-center">
    <a href="{{ route('login.frontend') }}" title="@lang('auth.back.login')">
        @lang('auth.back.login')
    </a>
</div>
@endsection
