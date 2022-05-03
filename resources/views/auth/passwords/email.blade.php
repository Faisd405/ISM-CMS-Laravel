@extends('layouts.backend.layout-auth')

@section('content')
<h5 class="text-center text-muted font-weight-normal mb-4">@lang('auth.forgot_password.text')</h5>

<form class="my-2" action="{{ route('password.email') }}" method="POST">
    @csrf

    <div class="form-group">
        <label class="form-label">@lang('auth.forgot_password.label.field1')</label>
        <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" 
          value="{{ old('email') }}" placeholder="@lang('auth.forgot_password.placeholder.field1')" autofocus>
        @include('components.field-error', ['field' => 'email'])
    </div>
    <div class="d-flex justify-content-between align-items-center m-0">
        <button type="submit" style="background: #0084ff;" class="btn btn-primary btn-block" 
            title="@lang('auth.forgot_password.label.send')">
            @lang('auth.forgot_password.label.send')
        </button>
    </div>

</form>
@endsection

@section('content-footer')
    <a href="{{ route('login.frontend') }}" title="@lang('auth.back.login')">
        <i class="las la-arrow-left mr-1" style="font-size: 1.3em;"></i>
        @lang('auth.back.login') 
    </a>
@endsection
