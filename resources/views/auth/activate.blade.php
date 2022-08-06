@extends('layouts.backend.layout-auth')

@section('content')
<!-- Text -->
<div class="title-heading text-center">
    <h3>@lang('auth.activate.title')</h3>
    <p>@lang('auth.activate.text')</p>
</div>

<!-- Form -->
<form class="form-box" action="{{ route('register.activate.send') }}" method="POST">
    @csrf

    <div class="form-group">
        <div class="input-alt w-icon">
            <label class="form-label" for="email">
                @lang('auth.activate.label.email')
            </label>
            <input id="email" type="text" class="form-control text-bolder @error('email') is-invalid @enderror" 
                name="email" value="{{ old('email') }}"
                placeholder="@lang('auth.activate.placeholder.email')">
            <i class="fi fi-rr-envelope"></i>
        </div>
        @include('components.field-error', ['field' => 'email'])
    </div>

    <button type="submit" class="btn btn-main btn-xl w-100 mt-3" title="@lang('auth.activate.label.send')">
        @lang('auth.activate.label.send')
    </button>
</form>
<!-- / Form -->
<div class="d-flex justify-content-center">
    <a href="{{ route('login.frontend') }}" title="@lang('auth.back.login')">
        @lang('auth.back.login')
    </a>
</div>
@endsection
