@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/pages/account.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/fancybox/fancybox.min.css') }}">
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
                            <a class="list-group-item list-group-item-action{{ empty(Request::get('tab')) ? ' active' : '' }}" 
                                href="{{ route('profile') }}" title="@lang('module/user.profile.label.tab1')">
                                @lang('module/user.profile.label.tab1')
                            </a>
                            <a class="list-group-item list-group-item-action{{ Request::get('tab') == 'change-password' ? ' active' : '' }}" 
                                href="{{ route('profile', ['tab' => 'change-password']) }}" title="@lang('module/user.profile.label.tab2')">
                                @lang('module/user.profile.label.tab2')
                            </a>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="tab-content">

                            <div class="tab-pane fade{{ empty(Request::get('tab')) ? ' show active' : '' }}">
                                <div class="card-body media align-items-center">
                                    <a href="{{ $data['user']['avatar'] }}" data-fancybox="gallery">
                                        <img src="{{ $data['user']['avatar'] }}" alt="{{ $data['user']['name'] }} photo" class="d-block ui-w-80 rounded-circle">
                                    </a>
                                    <div class="media-body ml-4">
                                        <button type="button" class="btn btn-outline-main w-icon mr-2" data-toggle="modal" data-target="#modal-change-photo" title="@lang('global.change')">
                                            <i class="fi fi-rr-camera"></i> <span>@lang('global.change')</span>
                                        </button>
                                        @if (!empty($data['user']['photo']))
                                        <button type="button" class="btn btn-outline-danger w-icon swal-delete-photo" title="@lang('global.remove')">
                                            <i class="fi fi-rr-cross-circle"></i> <span>@lang('global.remove')</span>
                                        </button>
                                        @endif
                                        <div class="text-light small mt-1">
                                            Allowed : <strong>{{ Str::upper(config('cms.files.avatar.mimes')) }}</strong>.
                                            Pixel : <strong>{{ config('cms.files.avatar.pixel') }}</strong>.
                                            Max Size : <strong>{{ config('cms.files.avatar.size') }}</strong>
                                        </div>
                                    </div>
                                </div>

                                <hr class="border-light m-0">

                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="form-label" for="name">@lang('module/user.label.name') <i class="text-danger">*</i></label>
                                        <input type="text" class="form-control text-bolder @error('name') is-invalid @enderror" name="name"
                                            value="{{ old('name', $data['user']['name']) }}" placeholder="@lang('module/user.placeholder.name')" autofocus>
                                        @include('components.field-error', ['field' => 'name'])
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="email">@lang('module/user.label.email') <i class="text-danger">*</i></label>
                                        <input type="hidden" name="old_email" value="{{ $data['user']['email'] }}">
                                        <input type="text" class="form-control text-bolder @error('email') is-invalid @enderror" name="email"
                                            value="{{ old('email', $data['user']['email']) }}" placeholder="@lang('module/user.placeholder.email')">
                                        @include('components.field-error', ['field' => 'email'])
                                        @if ($data['user']['email_verified'] == 0)
                                        <div class="alert alert-warning mt-3">
                                            @lang('module/user.verification.warning')<br>
                                            <a href="{{ route('profile.email.send', ['email' => $data['user']['email'], 'expired' => now()->addHours(3)->format('YmdHis')]) }}" class="font-weight-semibold">
                                                @lang('module/user.verification.btn')
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="username">@lang('module/user.label.username') <i class="text-danger">*</i></label>
                                        <input type="text" class="form-control text-bolder @error('username') is-invalid @enderror" name="username"
                                            value="{{ old('name', $data['user']['username']) }}" placeholder="@lang('module/user.placeholder.username')">
                                        @include('components.field-error', ['field' => 'username'])
                                        <small class="form-text">
                                            @lang('module/user.username_info')
                                        </small>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="phone">@lang('module/user.label.phone')</label>
                                        <input type="number" class="form-control text-bolder @error('phone') is-invalid @enderror" name="phone"
                                            value="{{ old('phone', $data['user']->phone) }}" placeholder="@lang('module/user.placeholder.phone')">
                                        @include('components.field-error', ['field' => 'phone'])
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade{{ Request::get('tab') == 'change-password' ? ' show active' : '' }}">
                                <div class="card-body pb-2">
                                    <div class="form-group">
                                        <label class="form-label" for="old_password">@lang('module/user.label.password_old') <i class="text-danger">*</i></label>
                                        <div class="input-group input-group-merge">
                                            <input id="password_old" type="password" class="form-control @error('old_password') is-invalid @enderror"
                                                name="old_password"  placeholder="@lang('module/user.placeholder.password_old')">
                                            <div class="input-group-append">
                                                <i class="input-group-text toggle-password-old fi fi-rr-eye text-main" toggle="#password_old"></i>
                                            </div>
                                            @include('components.field-error', ['field' => 'old_password'])
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="password">@lang('module/user.label.password') <i class="text-danger">*</i></label>
                                        <div class="input-group input-group-merge">
                                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                                name="password" placeholder="@lang('module/user.placeholder.password')">
                                            <div class="input-group-append">
                                                <i class="input-group-text toggle-password fi fi-rr-eye text-main" toggle="#password"></i>
                                            </div>
                                            @include('components.field-error', ['field' => 'password'])
                                        </div>
                                        <small class="form-text">
                                            @lang('module/user.password_info')
                                        </small>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="password_confirmation">@lang('module/user.label.password_confirmation') <i class="text-danger">*</i></label>
                                        <div class="input-group input-group-merge">
                                            <input id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                                name="password_confirmation"  placeholder="@lang('module/user.placeholder.password_confirmation')">
                                            <div class="input-group-append">
                                                <i class="input-group-text toggle-password-confirmation fi fi-rr-eye text-main" toggle="#password_confirmation"></i>
                                            </div>
                                            @include('components.field-error', ['field' => 'password_confirmation'])
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <hr class="border-light m-0 mb-3">
                <div class="card-footer justify-content-center">
                    <button type="submit" class="btn btn-main w-icon" title="@lang('global.save_change')">
                        <i class="fi fi-rr-disk"></i> <span>@lang('global.save_change')</span>
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

@include('backend.users.modal-change-photo')
@endsection

@section('scripts')
<script src="{{ asset('assets/backend/vendor/libs/fancybox/fancybox.min.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
<script src="{{ asset('assets/backend/js/ui_tooltips.js') }}"></script>
@endsection

@section('jsbody')
<script>
    // FILE BROWSE
    $(".custom-file-input").on("change", function () {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });

    // delete photo
    $(document).ready(function () {
        $('.swal-delete-photo').on('click', function () {
            Swal.fire({
                title: "@lang('global.alert.delete_confirm_title')",
                text: "@lang('global.alert.delete_confirm_text')",
                icon: 'warning',
                confirmButtonText: "@lang('global.alert.delete_btn_yes')",
                customClass: {
                    confirmButton: "btn btn-danger btn-lg",
                    cancelButton: "btn btn-secondary btn-lg"
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
                            icon: 'error',
                            text: 'Error while deleting data. Error Message: ' + error
                        })
                    });
                }
            }).then(response => {
                if (response.value.success) {
                    Swal.fire({
                        icon: 'success',
                        text: "{{ __('global.alert.delete_success', ['attribute' => __('module/user.label.photo')]) }}"
                    }).then(() => {
                        window.location.reload();
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        text: response.value.message
                    }).then(() => {
                        window.location.reload();
                    })
                }
            });
        });
    });
    
    // show & hide password
    $(".toggle-password-old, .toggle-password, .toggle-password-confirmation").click(function() {

        $(this).toggleClass("fi-rr-eye fi-rr-eye-crossed");

        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });
</script>

@error('avatar')
    @include('components.toastr-error')
@enderror
@endsection
