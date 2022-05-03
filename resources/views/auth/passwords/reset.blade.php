@extends('layouts.backend.layout-auth')

@section('content')
<h5 class="text-center text-muted font-weight-normal mb-4">@lang('auth.reset_password.text')</h5>

<form class="my-2" action="{{ route('password.update') }}" method="POST">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email ?? old('email') }}" 
        required 
        autocomplete="email">

    <div class="form-group">
        <label class="form-label">
            @lang('auth.reset_password.label.field1')
            <i class="las la-info-circle" data-toggle="popover" data-placement="right" 
                data-content="@lang('module/user.password_info')" title="Info">
            </i>
        </label>
        <div class="input-group">
            <input type="password" id="password-field" class="form-control @error('password') is-invalid @enderror" 
              name="password" placeholder="@lang('auth.reset_password.placeholder.field1')">
            <div class="input-group-append">
                <span toggle="#password-field" class="input-group-text toggle-password fas fa-eye"></span>
            </div>
            @include('components.field-error', ['field' => 'password'])
        </div>
    </div>
    <div class="form-group">
        <label class="form-label">@lang('auth.reset_password.label.field2')</label>
        <div class="input-group">
            <input type="password" id="password-field-confrimation" class="form-control @error('password_confirmation') is-invalid @enderror" 
              name="password_confirmation" placeholder="@lang('auth.reset_password.placeholder.field2')">
            <div class="input-group-append">
                <span toggle="#password-field-confrimation" class="input-group-text toggle-password-confirmation fas fa-eye"></span>
            </div>
            @include('components.field-error', ['field' => 'password_confirmation'])
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center m-0">
        <button type="submit" style="background: #0084ff;" class="btn btn-primary btn-block" 
            title="@lang('auth.reset_password.title')">
            @lang('auth.reset_password.title')
        </button>
    </div>

</form>
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
@include('components.toastr-error')
@endsection
