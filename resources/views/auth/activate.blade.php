@extends('layouts.backend.layout-auth')

@section('content')
<h5 class="text-center text-muted font-weight-normal mb-4">@lang('auth.activate.text')</h5>

<form class="my-2" action="{{ route('register.activate.send') }}" method="POST">
    @csrf
    <div class="form-group">
        <label class="form-label">@lang('auth.activate.label.field1')</label>
        <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" 
          value="{{ old('email') }}" placeholder="@lang('auth.activate.placeholder.field1')" autofocus>
        @include('components.field-error', ['field' => 'email'])
    </div>
    <div class="d-flex justify-content-between align-items-center m-0">
        <button type="submit" style="background: #0084ff;" class="btn btn-primary btn-block" 
            title="@lang('auth.activate.label.send')">
            @lang('auth.activate.label.send')
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
