@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/pages/account.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/fancybox/fancybox.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/sweetalert2/sweetalert2.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-12 col-lg-12 col-md-12">
        
        <form action="{{ route('profile') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card overflow-hidden">
                <div class="row no-gutters row-bordered row-border-light">
                    <div class="col-md-3 pt-0">
                        <div class="list-group list-group-flush account-settings-links">
                        <a class="list-group-item list-group-item-action {{ empty(Request::get('tab')) ? 'active' : '' }}" href="{{ route('profile') }}"  title="@lang('module/user.profile.label.tab1')">@lang('module/user.profile.label.tab1')</a>
                        <a class="list-group-item list-group-item-action {{ (Request::get('tab') == 'change-password') ? 'active' : '' }}" href="{{ route('profile', ['tab' => 'change-password']) }}" title="@lang('module/user.profile.label.tab2')">@lang('module/user.profile.label.tab2')</a>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="tab-content">
                        <div class="tab-pane fade {{ empty(Request::get('tab')) ? 'show active' : '' }}">

                            <div class="card-body media align-items-center">
                                <a href="{{ $data['user']->avatars() }}" data-fancybox="gallery">
                                    <img src="{{ $data['user']->avatars() }}" alt="" class="d-block ui-w-80">
                                </a>
                                <div class="media-body ml-4">
                                    <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-change-photo" title="@lang('global.change')">
                                        <i class="las la-camera"></i> @lang('global.change')
                                    </button>
                                    @if (!empty($data['user']['photo']))
                                    <button type="button" class="btn btn-outline-danger swal-delete-photo" title="@lang('global.remove')">
                                        <i class="las la-times"></i> @lang('global.remove')
                                    </button>
                                    @endif
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            Type of file : <strong>{{ Str::upper(config('cms.files.avatars.mimes')) }}</strong> | 
                                            Pixel : <strong>{{ config('cms.files.avatars.pixel') }}</strong> | 
                                            Max Size : <strong>{{ config('cms.files.avatars.size') }}</strong>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <hr class="border-light m-0">

                            <div class="card-body">
                                <div class="form-group">
                                    <label class="form-label">@lang('module/user.label.field1') <i class="text-danger">*</i></label>
                                    <input type="text" class="form-control mb-1 @error('name') is-invalid @enderror" name="name"
                                        value="{{ old('name', $data['user']['name']) }}" placeholder="@lang('module/user.placeholder.field1')" autofocus>
                                    @include('components.field-error', ['field' => 'name'])
                                </div>
                                <div class="form-group">
                                    <label class="form-label">@lang('module/user.label.field2') <i class="text-danger">*</i></label>
                                    <input type="hidden" name="old_email" value="{{ $data['user']['email'] }}">
                                    <input type="text" class="form-control mb-1 @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email', $data['user']['email']) }}" placeholder="@lang('module/user.placeholder.field2')">
                                    @include('components.field-error', ['field' => 'email'])
                                    @if ($data['user']['email_verified'] == 0)
                                    <div class="alert alert-warning mt-3">
                                        @lang('module/user.verification.warning')<br>
                                        <a href="{{ route('profile.email.send', ['email' => $data['user']['email'], 'expired' => now()->addHours(3)->format('YmdHis')]) }}">@lang('module/user.verification.btn')</a>
                                    </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="form-label"> @lang('module/user.label.field3') <i class="text-danger">*</i>
                                        <i class="las la-info-circle" data-toggle="popover" 
                                        data-placement="right" data-content="@lang('module/user.username_info')" title="Info">
                                        </i>
                                    </label>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror" name="username"
                                        value="{{ old('name', $data['user']['username']) }}" placeholder="@lang('module/user.placeholder.field3')">
                                    @include('components.field-error', ['field' => 'username'])
                                </div>
                                <div class="form-group">
                                    <label class="form-label">@lang('module/user.label.field4')</label>
                                    <input type="number" class="form-control @error('phone') is-invalid @enderror" name="phone"
                                        value="{{ old('phone', $data['user']->phone) }}" placeholder="@lang('module/user.placeholder.field4')">
                                    @include('components.field-error', ['field' => 'phone'])
                                </div>
                            </div>

                        </div>
                        <div class="tab-pane fade {{ (Request::get('tab') == 'change-password') ? 'show active' : '' }}">
                            <div class="card-body pb-2">

                                <div class="form-group">
                                    <label class="form-label">@lang('module/user.label.field7') <i class="text-danger">*</i></label>
                                    <div class="input-group">
                                        <input type="password" id="old-password-field" class="form-control @error('old_password') is-invalid @enderror" name="old_password" 
                                        placeholder="@lang('module/user.placeholder.field7')">
                                        <div class="input-group-append">
                                            <span toggle="#old-password-field" class="input-group-text toggle-old-password fas fa-eye"></span>
                                        </div>
                                        @include('components.field-error', ['field' => 'old_password'])
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">@lang('module/user.label.field5') <i class="text-danger">*</i>
                                        <i class="las la-info-circle"  data-toggle="popover" data-placement="right" 
                                        data-content="@lang('module/user.password_info')" title="Info">
                                        </i>
                                    </label>
                                    <div class="input-group">
                                        <input type="password" id="password-field" class="form-control gen-field @error('password') is-invalid @enderror" name="password"
                                            value="{{ old('password') }}" placeholder="@lang('module/user.placeholder.field5')">
                                        <div class="input-group-append">
                                            <span toggle="#password-field" class="input-group-text toggle-password fas fa-eye"></span>
                                        </div>
                                        @include('components.field-error', ['field' => 'password'])
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">@lang('module/user.label.field6') <i class="text-danger">*</i></label>
                                    <div class="input-group">
                                        <input type="password" id="password-confirm-field" class="form-control gen-field @error('password_confirmation') is-invalid @enderror" name="password_confirmation" 
                                            value="{{ old('password_confirmation') }}" placeholder="@lang('module/user.placeholder.field6')">
                                        <div class="input-group-append">
                                            <span toggle="#password-confirm-field" class="input-group-text toggle-password-confirm fas fa-eye"></span>
                                        </div>
                                        @include('components.field-error', ['field' => 'password_confirmation'])
                                    </div>
                                </div>

                            </div>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" title="@lang('global.save_change')">
                    <i class="las la-save"></i> @lang('global.save_change')
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

@include('backend.users.modal-change-photo')
@endsection

@section('scripts')
<script src="{{ asset('assets/backend/fancybox/fancybox.min.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
<script src="{{ asset('assets/backend/js/ui_tooltips.js') }}"></script>
@endsection

@section('jsbody')
<script>
  //delete photo
  $(document).ready(function () {
      $('.swal-delete-photo').on('click', function () {
          Swal.fire({
            title: "@lang('global.alert.delete_confirm_title')",
              text: "@lang('global.alert.delete_confirm_text')",
              type: "warning",
              confirmButtonText: "@lang('global.alert.delete_btn_yes')",
              customClass: {
                  confirmButton: "btn btn-danger btn-lg",
                  cancelButton: "btn btn-primary btn-lg"
              },
              showLoaderOnConfirm: true,
              showCancelButton: true,
              allowOutsideClick: () => !Swal.isLoading(),
              cancelButtonText: "@lang('global.alert.delete_btn_cancel')",
              preConfirm: () => {
                  return $.ajax({
                      url: '/admin/profile/photo/remove',
                      method: 'PUT',
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },
                      dataType: 'json'
                  }).then(response => {
                      if (!response.success) {
                          return new Error(response.message);
                      }
                      return response;
                  }).catch(error => {
                      swal({
                          type: 'error',
                          text: 'Error while deleting data. Error Message: ' + error
                      })
                  });
              }
          }).then(response => {
              if (response.value.success) {
                  Swal.fire({
                      type: 'success',
                      text: "{{ __('global.alert.delete_success', ['attribute' => __('module/user.label.photo')]) }}"
                  }).then(() => {
                      window.location.reload();
                  })
              } else {
                  Swal.fire({
                      type: 'error',
                      text: response.value.message
                  }).then(() => {
                      window.location.reload();
                  })
              }
          });
      });
  });
  //show & hide password
  $(".toggle-old-password, .toggle-password, .toggle-password-confirm").click(function() {
      $(this).toggleClass("fa-eye fa-eye-slash");
      var input = $($(this).attr("toggle"));
      if (input.attr("type") == "password") {
      input.attr("type", "text");
      } else {
      input.attr("type", "password");
      }
  });
</script>

@error('avatars')
@include('components.toastr-error')
@enderror
@endsection
