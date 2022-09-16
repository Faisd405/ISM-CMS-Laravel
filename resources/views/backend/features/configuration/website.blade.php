@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/pages/account.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/fancybox/fancybox.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/sweetalert2/sweetalert2.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10 col-lg-10 col-md-10">

        <div class="card overflow-hidden">
            <div class="row no-gutters row-bordered row-border-light">

                <div class="col-md-3 pt-0">
                    <div class="list-group list-group-flush account-settings-links">
                        @if ($data['file']->count() > 0)
                        <a class="list-group-item list-group-item-action {{ (Request::get('tab') == 'file') ? 'active' : '' }}" href="{{ route('configuration.website', ['tab' => 'file']) }}" 
                            title="@lang('feature/configuration.website.label.file')">
                            @lang('feature/configuration.website.label.file')
                        </a>
                        @endif
                        @if ($data['general']->count() > 0)
                        <a class="list-group-item list-group-item-action {{ empty(Request::get('tab')) ? 'active' : '' }}" href="{{ route('configuration.website') }}" 
                            title="@lang('feature/configuration.website.label.general')">
                            @lang('feature/configuration.website.label.general')
                        </a>
                        @endif
                        @if ($data['seo']->count() > 0)
                        <a class="list-group-item list-group-item-action {{ (Request::get('tab') == 'seo') ? 'active' : '' }}" href="{{ route('configuration.website', ['tab' => 'seo']) }}"
                            title="@lang('feature/configuration.website.label.seo')">
                            @lang('feature/configuration.website.label.seo')
                        </a>
                        @endif
                        @if ($data['social_media']->count() > 0)
                        <a class="list-group-item list-group-item-action {{ (Request::get('tab') == 'socmed') ? 'active' : '' }}" href="{{ route('configuration.website', ['tab' => 'socmed']) }}"
                            title="@lang('feature/configuration.website.label.socmed')">
                            @lang('feature/configuration.website.label.socmed')
                        </a>
                        @endif
                        @if ($data['notification']->count() > 0)
                        <a class="list-group-item list-group-item-action {{ (Request::get('tab') == 'notif') ? 'active' : '' }}" href="{{ route('configuration.website', ['tab' => 'notif']) }}"
                            title="@lang('feature/configuration.website.label.notif')">
                            @lang('feature/configuration.website.label.notif')
                        </a>
                        @endif
                        @role ('developer|super')
                        <a class="list-group-item list-group-item-action {{ (Request::get('tab') == 'dev') ? 'active' : '' }}" href="{{ route('configuration.website', ['tab' => 'dev']) }}"
                            title="@lang('feature/configuration.website.label.dev')">
                            @lang('feature/configuration.website.label.dev')
                        </a>
                        <a class="list-group-item list-group-item-action {{ (Request::get('tab') == 'set-config') ? 'active' : '' }}" href="{{ route('configuration.website', ['tab' => 'set-config']) }}"
                            title="@lang('feature/configuration.website.label.set')">
                            @lang('feature/configuration.website.label.set')
                        </a>
                        @endrole
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="tab-content">

                        @if ($data['file']->count() > 0)
                        {{-- Upload --}}
                        <div class="tab-pane fade {{ (Request::get('tab') == 'file') ? 'show active' : '' }}">
                            @foreach ($data['file'] as $file)
                            <div class="card-body media align-items-center">
                                <a href="{{ $file->file($file['name']) }}" data-fancybox="gallery">
                                    <img src="{{ $file->file($file['name']) }}" alt="" class="d-block ui-w-80">
                                </a>
                                <div class="media-body ml-4">
                                    <form id="file-{{ $file['name'] }}" action="{{ route('configuration.website.upload', ['name' => $file['name']]) }}" 
                                        method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="box-btn">
                                            <label class="btn btn-outline-main btn-sm">
                                                {{ $file['label'] }}
                                                <input type="hidden" name="old_{{ $file['name'] }}" value="{{ $file['value'] }}">
                                                <input type="file" id="{{ $file['name'] }}" name="{{ $file['name'] }}" class="account-settings-fileinput">
                                            </label>
                                            @if ($file['value'] != null)
                                            <button type="button" class="btn icon-btn btn-sm btn-danger swal-delete"
                                                data-name="{{ $file['name'] }}" title="@lang('global.delete_attr', [
                                                    'attribute' => __('feature/configuration.website.label.file')
                                                ])">
                                                <i class="fi fi-rr-cross-circle"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </form>

                                    @error($file['name'])
                                    <div class="small mt-1" style="color:#d9534f;">{{ $message }}</div>
                                    @enderror
                                    <div class="text-light small mt-1">
                                        Allowed : <strong>{{ Str::upper(config('cms.files.config.'.$file['name'].'.mimes')) }}</strong></span>.
                                        @if(config('cms.files.config.'.$file['name'].'.pixel'))
                                        Pixel : <strong>{{ config('cms.files.config.'.$file['name'].'.pixel') }}</strong>. 
                                        @endif
                                        Max Upload : <strong>{{ config('cms.files.config.'.$file['name'].'.size') }}</strong>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        @if ($data['general']->count() > 0)
                        {{-- General --}}
                        <div class="tab-pane fade {{ empty(Request::get('tab')) ? 'show active' : '' }}">
                            <form action="{{ route('configuration.website.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    @foreach ($data['general'] as $general)
                                    <div class="form-group">
                                        <label class="form-label">{{ $general['label'] }}</label>
                                        <textarea class="form-control text-bolder" name="name[{{ $general['name'] }}]" 
                                            placeholder="{{ Str::replace('_', ' ', $general['name']) }}">{!! old($general['name'], $general['value']) !!}</textarea>
                                    </div>
                                    @endforeach
                                    <hr>
                                    <div class="text-center mt-3">
                                        <button type="submit" class="btn btn-main w-icon" title="@lang('global.save_change')">
                                            <i class="fi fi-rr-disk"></i> <span>@lang('global.save_change')</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endif

                        @if ($data['seo']->count() > 0)
                        {{-- SEO --}}
                        <div class="tab-pane fade {{ (Request::get('tab') == 'seo') ? 'show active' : '' }}">
                            <form action="{{ route('configuration.website.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    @foreach ($data['seo'] as $seo)
                                    <div class="form-group">
                                        <label class="form-label">{{ $seo['label'] }}</label>
                                        <textarea class="form-control mb-1" name="name[{{ $seo['name'] }}]" 
                                            placeholder="{{ Str::replace('_', ' ', $seo['name']) }}">{!! old($seo['name'], $seo['value']) !!}</textarea>
                                    </div>
                                    @endforeach
                                    <hr>
                                    <div class="text-center mt-3">
                                        <button type="submit" class="btn btn-main w-icon" title="@lang('global.save_change')">
                                            <i class="fi fi-rr-disk"></i> <span>@lang('global.save_change')</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endif

                        @if ($data['social_media']->count() > 0)
                        {{-- Socmed --}}
                        <div class="tab-pane fade {{ (Request::get('tab') == 'socmed') ? 'show active' : '' }}">
                            <form action="{{ route('configuration.website.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    @foreach ($data['social_media'] as $socmed)
                                    <div class="form-group">
                                        <label class="form-label">{{ $socmed['label'] }}</label>
                                        <textarea class="form-control mb-1" name="name[{{ $socmed['name'] }}]" 
                                            placeholder="{{ Str::replace('_', ' ', $socmed['name']) }}">{!! old($socmed['name'], $socmed['value']) !!}</textarea>
                                    </div>
                                    @endforeach
                                    <hr>
                                    <div class="text-center mt-3">
                                        <button type="submit" class="btn btn-main w-icon" title="@lang('global.save_change')">
                                            <i class="fi fi-rr-disk"></i> <span>@lang('global.save_change')</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endif

                        @if ($data['notification']->count() > 0)
                        {{-- Notif --}}
                        <div class="tab-pane fade {{ (Request::get('tab') == 'notif') ? 'show active' : '' }}">
                            <form action="{{ route('configuration.website.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    @foreach ($data['notification'] as $notif)
                                    <div class="form-group">
                                        <label class="form-label">{{ $notif['label'] }}</label>
                                        <select class="form-control" name="name[{{ $notif['name'] }}]">
                                            @foreach (__('global.label.active') as $key => $value)
                                            <option value="{{ $key }}" {{ $notif['value'] == $key ? 'selected' : '' }} 
                                                title="{{ $key }}">{{ $value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endforeach
                                    <hr>
                                    <div class="text-center mt-3">
                                        <div class="text-center mt-3">
                                            <button type="submit" class="btn btn-main w-icon" title="@lang('global.save_change')">
                                                <i class="fi fi-rr-disk"></i> <span>@lang('global.save_change')</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endif

                        @role ('developer|super')
                        {{-- Developer Only --}}
                        <div class="tab-pane fade {{ (Request::get('tab') == 'dev') ? 'show active' : '' }}">
                            <form action="{{ route('configuration.website.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    @foreach ($data['dev_only'] as $dev)
                                    <div class="form-group">
                                        <label class="form-label">{{ $dev['label'] }}</label>
                                        @if ($dev['name'] == 'maintenance' || $dev['name'] == 'pwa')
                                        <select class="form-control" name="name[{{ $dev['name'] }}]">
                                            @foreach (__('global.label.optional') as $key => $value)
                                            <option value="{{ $key }}" {{ $dev['value'] == $key ? 'selected' : '' }} 
                                                title="{{ $key }}">{{ $value}}</option>
                                            @endforeach
                                        </select>
                                        @elseif ($dev['name'] == 'default_lang')
                                        <select class="form-control" name="name[{{ $dev['name'] }}]">
                                            @foreach ($data['languages'] as $key => $value)
                                            <option value="{{ $value['iso_codes'] }}" {{ $value['iso_codes'] == $dev['value'] ? 'selected' : '' }} 
                                                title="{{ $value['iso_codes'] }}">{{ $value['name'] }}</option>
                                            @endforeach
                                        </select>
                                        @else
                                        <textarea class="form-control text-bolder" name="name[{{ $dev['name'] }}]" 
                                            placeholder="{{ Str::replace('_', ' ', $dev['name']) }}">{!! old($dev['name'], $dev['value']) !!}</textarea> 
                                        @endif
                                    </div>
                                    @endforeach
                                    <hr>
                                    <div class="text-center mt-3">
                                        <button type="submit" class="btn btn-main w-icon" title="@lang('global.save_change')">
                                            <i class="fi fi-rr-disk"></i> <span>@lang('global.save_change')</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- Set Config --}}
                        <div class="tab-pane fade {{ (Request::get('tab') == 'set-config') ? 'show active' : '' }}">
                            <form action="{{ route('configuration.website.set') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body" style="overflow: scroll; height: 600px;">
                                    @foreach ($data['all_config'] as $all)
                                    <h6 class="text-main">{{ $loop->iteration.'. '.Str::upper(str_replace('_', ' ', $all['name'])) }}</h6>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label class="form-label">Label</label>
                                            <input type="text" class="form-control text-bolder" name="label[{{ $all['name'] }}]" 
                                                value="{!! old($all['name'], $all['label']) !!}">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="form-label">Group</label>
                                            <select class="form-control show-tick" name="group[{{ $all['name'] }}]" data-style="btn-default">
                                                @foreach (config('cms.module.feature.configuration.group') as $key => $value)
                                                    <option value="{{ $value }}" {{ old('group', $all['group']) == ''.$value.'' ? 'selected' : '' }}>
                                                        @lang('feature/configuration.website.label.'.$value)
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Show Form</label> <br>
                                            <label class="switcher switcher-success">
                                                <input type="checkbox" class="switcher-input" name="show_form[{{ $all['name'] }}]" value="1" 
                                                    {{ old($all['name'], $all['show_form']) ? 'checked' : '' }}>
                                                <span class="switcher-indicator">
                                                <span class="switcher-yes">
                                                    <span class="ion ion-md-checkmark"></span>
                                                </span>
                                                <span class="switcher-no">
                                                    <span class="ion ion-md-close"></span>
                                                </span>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Active</label> <br>
                                            <label class="switcher switcher-success">
                                                <input type="checkbox" class="switcher-input" name="active[{{ $all['name'] }}]" value="1" 
                                                    {{ old($all['name'], $all['active']) ? 'checked' : '' }}>
                                                <span class="switcher-indicator">
                                                <span class="switcher-yes">
                                                    <span class="ion ion-md-checkmark"></span>
                                                </span>
                                                <span class="switcher-no">
                                                    <span class="ion ion-md-close"></span>
                                                </span>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label class="form-label">@lang('global.locked')</label> <br>
                                            <label class="switcher switcher-success">
                                                <input type="checkbox" class="switcher-input" name="locked[{{ $all['name'] }}]" value="1" 
                                                    {{ old($all['name'], $all['locked']) ? 'checked' : '' }}>
                                                <span class="switcher-indicator">
                                                <span class="switcher-yes">
                                                    <span class="ion ion-md-checkmark"></span>
                                                </span>
                                                <span class="switcher-no">
                                                    <span class="ion ion-md-close"></span>
                                                </span>
                                                </span>
                                            </label>
                                        </div>
                                        @if ($all['locked'] == 0)
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Delete</label> <br>
                                            <button type="button" class="ml-2 btn icon-btn btn-sm btn-danger swal-delete-config"
                                                data-name="{{ $all['name'] }}" 
                                                data-toggle="tooltip" data-placement="bottom"
                                                data-original-title="@lang('global.delete_attr', [
                                                    'attribute' => __('feature/configuration.caption')
                                                ])">
                                                <i class="fi fi-rr-trash"></i>
                                            </button>
                                        </div>
                                        @endif
                                    </div>
                                    <hr class="border-light mb-2">
                                    @endforeach
                                </div>
                                <hr>
                                <div class="text-center mt-3 mb-4">
                                    <button type="button" class="btn btn-success w-icon mr-2" data-toggle="modal" data-target="#modals-add"
                                        title="@lang('global.add_new')">
                                        <i class="fi fi-rr-add"></i> <span>@lang('global.add_new')</span>
                                    </button>
                                    <button type="submit" class="btn btn-main w-icon" title="@lang('global.save_change')">
                                        <i class="fi fi-rr-disk"></i> <span>@lang('global.save_change')</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endrole

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

{{-- modal --}}
<div class="modal fade" id="modals-add">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('configuration.website.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">
                    Form
                    <span class="font-weight-light">@lang('feature/configuration.caption')</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group col">
                        <label class="form-label">Group <i class="text-danger">*</i></label>
                        <select class="form-control show-tick" name="group" data-style="btn-default">
                            <option value=" " selected disabled>@lang('global.select')</option>
                            @foreach (config('cms.module.feature.configuration.group') as $key => $value)
                                <option value="{{ $value }}" {{ old('group') == ''.$value.'' ? 'selected' : '' }}>
                                    @lang('feature/configuration.website.label.'.$value)
                                </option>
                            @endforeach
                        </select>
                        @error('group')
                        <label class="error jquery-validation-error small form-text invalid-feedback" style="display: inline-block; color:red;">{!! $message !!}</label>
                        @enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <label class="form-label">Name / Key <i class="text-danger">*</i></label>
                        <input type="text" class="form-control text-bolder @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}">
                        @include('components.field-error', ['field' => 'name'])
                        <small class="text-form text-muted">@lang('global.lower_case')</small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <label class="form-label">Label <i class="text-danger">*</i></label>
                        <input type="text" class="form-control text-bolder @error('label') is-invalid @enderror" name="label" value="{{ old('label') }}">
                        @include('components.field-error', ['field' => 'label'])
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <label class="form-label">Value</label>
                        <textarea class="form-control text-bolder @error('value') is-invalid @enderror" name="value">{{ old('vale') }}</textarea>
                        @include('components.field-error', ['field' => 'value'])
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <label class="form-label">@lang('global.locked')</label>
                        <label class="custom-control custom-checkbox m-0">
                            <input type="checkbox" class="custom-control-input" name="locked" value="1"
                            {{ (old('locked') ? 'checked' : '') }}>
                            <span class="custom-control-label">@lang('global.label.optional.1')</span>
                        </label>
                        <small class="form-text text-muted">@lang('global.locked_info')</small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col mb-0">
                        <label class="form-label">Upload</label>
                        <label class="switcher switcher-success">
                            <input type="checkbox" class="switcher-input" name="is_upload" value="1" 
                                {{ old('is_upload') ? 'checked' : '' }}>
                            <span class="switcher-indicator">
                            <span class="switcher-yes">
                                <span class="ion ion-md-checkmark"></span>
                            </span>
                            <span class="switcher-no">
                                <span class="ion ion-md-close"></span>
                            </span>
                            </span>
                        </label>
                    </div>
                    <div class="form-group col mb-0">
                        <label class="form-label">Show Form</label>
                        <label class="switcher switcher-success">
                            <input type="checkbox" class="switcher-input" name="show_form" value="1" 
                                {{ old('show_form') ? 'checked' : '' }}>
                            <span class="switcher-indicator">
                            <span class="switcher-yes">
                                <span class="ion ion-md-checkmark"></span>
                            </span>
                            <span class="switcher-no">
                                <span class="ion ion-md-close"></span>
                            </span>
                            </span>
                        </label>
                    </div>
                    <div class="form-group col mb-0">
                        <label class="form-label">Active</label>
                        <label class="switcher switcher-success">
                            <input type="checkbox" class="switcher-input" name="active" value="1" 
                                {{ old('active') ? 'checked' : '' }}>
                            <span class="switcher-indicator">
                            <span class="switcher-yes">
                                <span class="ion ion-md-checkmark"></span>
                            </span>
                            <span class="switcher-no">
                                <span class="ion ion-md-close"></span>
                            </span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default w-icon" data-dismiss="modal" title="@lang('global.close')">
                    <i class="fi fi-rr-cross-circle"></i> <span>@lang('global.close')</span>
                </button>
                <button type="submit" class="btn btn-main w-icon" title="@lang('global.save')">
                    <i class="fi fi-rr-disk"></i> <span>@lang('global.save')</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/backend/js/ui_tooltips.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/fancybox/fancybox.min.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('jsbody')
<script>
    @foreach ($data['file'] as $file)
    $('#{{ $file['name'] }}').change(function() {
        $('#file-{{ $file['name'] }}').submit();
    });
    @endforeach

    //delete
    $(document).ready(function () {
        $('.swal-delete').on('click', function () {
            var name = $(this).attr('data-name');
            Swal.fire({
                title: "@lang('global.alert.delete_confirm_title')",
                text: "@lang('global.alert.delete_confirm_text')",
                icon: "warning",
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
                        url: '/admin/configuration/website/'+name+'/delete',
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
                        text: "@lang('global.alert.delete_success', ['attribute' => __('feature/configuration.website.label.file')])"
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

        $('.swal-delete-config').on('click', function () {
            var name = $(this).attr('data-name');
            Swal.fire({
                title: "@lang('global.alert.delete_confirm_title')",
                text: "@lang('global.alert.delete_confirm_text')",
                icon: "warning",
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
                        url: '/admin/configuration/website/'+name+'/delete-config',
                        method: 'DELETE',
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
                        text: "@lang('global.alert.delete_success', ['attribute' => __('feature/configuration.caption')])"
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
</script>
@endsection