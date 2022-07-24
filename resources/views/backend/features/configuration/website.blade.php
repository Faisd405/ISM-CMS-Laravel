@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/pages/account.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/fancybox/fancybox.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/sweetalert2/sweetalert2.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10 col-lg-10 col-md-10">

        <div class="card overflow-hidden">
            <div class="row no-gutters row-bordered row-border-light">
                <div class="col-md-3 pt-0">
                    <div class="list-group list-group-flush account-settings-links">
                        @if ($data['upload']->count() > 0)
                        <a class="list-group-item list-group-item-action {{ (Request::get('tab') == 'upload') ? 'active' : '' }}" href="{{ route('configuration.website', ['tab' => 'upload']) }}" 
                            title="@lang('feature/configuration.website.label.tabs1')">
                            @lang('feature/configuration.website.label.tabs1')
                        </a>
                        @endif
                        @if ($data['general']->count() > 0)
                        <a class="list-group-item list-group-item-action {{ empty(Request::get('tab')) ? 'active' : '' }}" href="{{ route('configuration.website') }}" 
                            title="@lang('feature/configuration.website.label.tabs2')">
                            @lang('feature/configuration.website.label.tabs2')
                        </a>
                        @endif
                        @if ($data['meta_data']->count() > 0)
                        <a class="list-group-item list-group-item-action {{ (Request::get('tab') == 'meta-data') ? 'active' : '' }}" href="{{ route('configuration.website', ['tab' => 'meta-data']) }}"
                            title="@lang('feature/configuration.website.label.tabs3')">
                            @lang('feature/configuration.website.label.tabs3')
                        </a>
                        @endif
                        @if ($data['social_media']->count() > 0)
                        <a class="list-group-item list-group-item-action {{ (Request::get('tab') == 'social-media') ? 'active' : '' }}" href="{{ route('configuration.website', ['tab' => 'social-media']) }}"
                            title="@lang('feature/configuration.website.label.tabs4')">
                            @lang('feature/configuration.website.label.tabs4')
                        </a>
                        @endif
                        @if ($data['notification']->count() > 0)
                        <a class="list-group-item list-group-item-action {{ (Request::get('tab') == 'notification') ? 'active' : '' }}" href="{{ route('configuration.website', ['tab' => 'notification']) }}"
                            title="@lang('feature/configuration.website.label.tabs5')">
                            @lang('feature/configuration.website.label.tabs5')
                        </a>
                        @endif
                        @role ('developer|super')
                        <a class="list-group-item list-group-item-action {{ (Request::get('tab') == 'dev-only') ? 'active' : '' }}" href="{{ route('configuration.website', ['tab' => 'dev-only']) }}"
                            title="@lang('feature/configuration.website.label.tabs100')">
                            @lang('feature/configuration.website.label.tabs100')
                        </a>
                        <a class="list-group-item list-group-item-action {{ (Request::get('tab') == 'set-config') ? 'active' : '' }}" href="{{ route('configuration.website', ['tab' => 'set-config']) }}"
                            title="@lang('feature/configuration.website.label.tabs_set')">
                            @lang('feature/configuration.website.label.tabs_set')
                        </a>
                        @endrole
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="tab-content">

                        @if ($data['upload']->count() > 0)
                        {{-- Upload --}}
                        <div class="tab-pane fade {{ (Request::get('tab') == 'upload') ? 'show active' : '' }}">
                            @foreach ($data['upload'] as $upload)
                            <div class="card-body media align-items-center">
                                <a href="{{ $upload->file($upload['name']) }}" data-fancybox="gallery">
                                    <img src="{{ $upload->file($upload['name']) }}" alt="" class="d-block ui-w-80">
                                </a>
                                <div class="media-body ml-4">
                                    <form id="upload-{{ $upload['name'] }}" action="{{ route('configuration.website.upload', ['name' => $upload['name']]) }}" 
                                        method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <label class="btn btn-outline-primary btn-sm">
                                            {{ $upload['label'] }}
                                            <input type="hidden" name="old_{{ $upload['name'] }}" value="{{ $upload['value'] }}">
                                            <input type="file" id="{{ $upload['name'] }}" name="{{ $upload['name'] }}" class="account-settings-fileinput">
                                        </label>
                                        @if ($upload['value'] != null)
                                        <button type="button" class="ml-2 btn btn-danger icon-btn btn-sm swal-delete"
                                            data-name="{{ $upload['name'] }}" title="@lang('global.delete_attr', [
                                                'attribute' => __('feature/configuration.website.label.tabs1')
                                            ])">
                                            <i class="las la-trash-alt"></i>
                                        </button>
                                        @endif
                                    </form>

                                    @error($upload['name'])
                                    <div class="small mt-1" style="color:#d9534f;">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-light small mt-1">
                                        [<span class="text-muted">@lang('global.type_file') : <strong>{{ Str::upper(config('cms.files.config.'.$upload['name'].'.mimes')) }}</strong></span>] -
                                        @if(config('cms.files.config.'.$upload['name'].'.pixel'))
                                        [<span class="text-muted">Pixel : <strong>{{ config('cms.files.config.'.$upload['name'].'.pixel') }}</strong></span>] - 
                                        @endif
                                        [<span class="text-muted">@lang('global.max_upload') : <strong>{{ config('cms.files.config.'.$upload['name'].'.size') }}</strong></span>]
                                    </small>
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
                                        <textarea class="form-control mb-1" name="name[{{ $general['name'] }}]" 
                                            placeholder="{{ Str::replace('_', ' ', $general['name']) }}">{!! old($general['name'], $general['value']) !!}</textarea>
                                    </div>
                                    @endforeach
                                    <hr>
                                    <div class="text-center mt-3">
                                        <button type="submit" class="btn btn-primary" title="@lang('global.save_change')">
                                            <i class="las la-save"></i> @lang('global.save_change')
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endif

                        @if ($data['meta_data']->count() > 0)
                        {{-- SEO --}}
                        <div class="tab-pane fade {{ (Request::get('tab') == 'meta-data') ? 'show active' : '' }}">
                            <form action="{{ route('configuration.website.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    @foreach ($data['meta_data'] as $meta)
                                    <div class="form-group">
                                        <label class="form-label">{{ $meta['label'] }}</label>
                                        <textarea class="form-control mb-1" name="name[{{ $meta['name'] }}]" 
                                            placeholder="{{ Str::replace('_', ' ', $meta['name']) }}">{!! old($meta['name'], $meta['value']) !!}</textarea>
                                    </div>
                                    @endforeach
                                    <hr>
                                    <div class="text-center mt-3">
                                        <button type="submit" class="btn btn-primary" title="@lang('global.save_change')">
                                            <i class="las la-save"></i> @lang('global.save_change')
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endif

                        @if ($data['social_media']->count() > 0)
                        {{-- Socmed --}}
                        <div class="tab-pane fade {{ (Request::get('tab') == 'social-media') ? 'show active' : '' }}">
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
                                        <button type="submit" class="btn btn-primary" title="@lang('global.save_change')">
                                            <i class="las la-save"></i> @lang('global.save_change')
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endif

                        @if ($data['notification']->count() > 0)
                        {{-- Socmed --}}
                        <div class="tab-pane fade {{ (Request::get('tab') == 'notification') ? 'show active' : '' }}">
                            <form action="{{ route('configuration.website.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    @foreach ($data['notification'] as $notif)
                                    <div class="form-group">
                                        <label class="form-label">{{ $notif['label'] }}</label>
                                        <select class="custom-select" name="name[{{ $notif['name'] }}]">
                                            @foreach (__('global.label.active') as $key => $value)
                                            <option value="{{ $key }}" {{ $notif['value'] == $key ? 'selected' : '' }} 
                                                title="{{ $key }}">{{ $value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endforeach
                                    <hr>
                                    <div class="text-center mt-3">
                                        <button type="submit" class="btn btn-primary" title="@lang('global.save_change')">
                                            <i class="las la-save"></i> @lang('global.save_change')
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endif

                        @role ('developer|super')
                        {{-- Developer Only --}}
                        <div class="tab-pane fade {{ (Request::get('tab') == 'dev-only') ? 'show active' : '' }}">
                            <form action="{{ route('configuration.website.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    @foreach ($data['dev_only'] as $dev)
                                    <div class="form-group">
                                        <label class="form-label">{{ $dev['label'] }}</label>
                                        @if ($dev['name'] == 'maintenance')
                                        <select class="custom-select" name="name[{{ $dev['name'] }}]">
                                            @foreach (__('global.label.optional') as $key => $value)
                                            <option value="{{ $key }}" {{ $dev['value'] == $key ? 'selected' : '' }} 
                                                title="{{ $key }}">{{ $value}}</option>
                                            @endforeach
                                        </select>
                                        {{-- @elseif ($dev['name'] == 'default_lang')
                                        <select class="custom-select" name="name[{{ $dev['name'] }}]">
                                            @foreach ($data['languages'] as $key => $value)
                                            <option value="{{ $value['iso_codes'] }}" {{ $value['iso_codes'] == App::getLocale() ? 'selected' : '' }} 
                                                title="{{ $value['iso_codes'] }}">{{ $value['name'] }}</option>
                                            @endforeach
                                        </select> --}}
                                        @elseif ($dev['name'] == 'pwa')
                                        <select class="custom-select" name="name[{{ $dev['name'] }}]">
                                            @foreach (__('global.label.optional') as $key => $value)
                                                <option value="{{ $key }}" {{ $dev['value'] == $key ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @else
                                        <textarea class="form-control mb-1" name="name[{{ $dev['name'] }}]" 
                                            placeholder="{{ Str::replace('_', ' ', $dev['name']) }}">{!! old($dev['name'], $dev['value']) !!}</textarea> 
                                        @endif
                                    </div>
                                    @endforeach
                                    <hr>
                                    <div class="text-center mt-3">
                                        <button type="submit" class="btn btn-primary" title="@lang('global.save_change')">
                                            <i class="las la-save"></i> @lang('global.save_change')
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
                                    <h6 class="text-primary">{{ $loop->iteration.' '.Str::upper(str_replace('_', ' ', $all['name'])) }}</h6>
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label class="form-label">Label</label>
                                            <input type="text" class="form-control" name="label[{{ $all['name'] }}]" 
                                                value="{!! old($all['name'], $all['label']) !!}">
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
                                            <button type="button" class="ml-2 btn btn-danger icon-btn btn-sm swal-delete-config"
                                                data-name="{{ $all['name'] }}" title="@lang('global.delete_attr', [
                                                    'attribute' => __('feature/configuration.caption')
                                                ])">
                                                <i class="las la-trash-alt"></i>
                                            </button>
                                        </div>
                                        @endif
                                      </div>
                                    @endforeach
                                </div>
                                <hr>
                                <div class="text-center mt-3 mb-4">
                                    <button type="button" class="btn btn-success mr-2" data-toggle="modal" data-target="#modals-add"
                                        title="@lang('global.add_new')">
                                        <i class="las la-plus"></i> @lang('global.add_new')
                                    </button>
                                    <button type="submit" class="btn btn-primary" title="@lang('global.save_change')">
                                        <i class="las la-save"></i> @lang('global.save_change')
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
                                <option value="{{ $key }}" {{ old('group') == ''.$key.'' ? 'selected' : '' }}>
                                    @lang('feature/configuration.website.label.tabs'.$key)
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
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}">
                        @include('components.field-error', ['field' => 'name'])
                        <small class="text-form text-muted">@lang('global.lower_case')</small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <label class="form-label">Label <i class="text-danger">*</i></label>
                        <input type="text" class="form-control @error('label') is-invalid @enderror" name="label" value="{{ old('label') }}">
                        @include('components.field-error', ['field' => 'label'])
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <label class="form-label">Value</label>
                        <textarea class="form-control @error('value') is-invalid @enderror" name="value">{{ old('vale') }}</textarea>
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
                        <label class="form-label">Upload</label> <br>
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
                        <label class="form-label">Show Form</label> <br>
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
                        <label class="form-label">Active</label> <br>
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
                <button type="button" class="btn btn-default" data-dismiss="modal" title="@lang('global.close')">
                    <i class="las la-times"></i> @lang('global.close')
                </button>
                <button type="submit" class="btn btn-primary" title="@lang('global.save')">
                    <i class="las la-save"></i> @lang('global.save')
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/backend/fancybox/fancybox.min.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('jsbody')
<script>
    @foreach ($data['upload'] as $upload)
    $('#{{ $upload['name'] }}').change(function() {
        $('#upload-{{ $upload['name'] }}').submit();
    });
    @endforeach

    //delete
    $(document).ready(function () {
        $('.swal-delete').on('click', function () {
            var name = $(this).attr('data-name');
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
                            type: 'error',
                            text: 'Error while deleting data. Error Message: ' + error
                        })
                    });
                }
            }).then(response => {
                if (response.value.success) {
                    Swal.fire({
                        type: 'success',
                        text: "@lang('global.alert.delete_success', ['attribute' => __('feature/configuration.website.label.tabs1')])"
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

        $('.swal-delete-config').on('click', function () {
            var name = $(this).attr('data-name');
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
                            type: 'error',
                            text: 'Error while deleting data. Error Message: ' + error
                        })
                    });
                }
            }).then(response => {
                if (response.value.success) {
                    Swal.fire({
                        type: 'success',
                        text: "@lang('global.alert.delete_success', ['attribute' => __('feature/configuration.caption')])"
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
</script>
@endsection