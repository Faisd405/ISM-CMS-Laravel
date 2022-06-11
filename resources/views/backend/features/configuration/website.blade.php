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
                        <a class="list-group-item list-group-item-action {{ (Request::get('tab') == 'upload') ? 'active' : '' }}" href="{{ route('configuration.website', ['tab' => 'upload']) }}" 
                            title="@lang('feature/configuration.website.label.tabs1')">
                            @lang('feature/configuration.website.label.tabs1')
                        </a>
                        <a class="list-group-item list-group-item-action {{ empty(Request::get('tab')) ? 'active' : '' }}" href="{{ route('configuration.website') }}" 
                            title="@lang('feature/configuration.website.label.tabs2')">
                            @lang('feature/configuration.website.label.tabs2')
                        </a>
                        <a class="list-group-item list-group-item-action {{ (Request::get('tab') == 'meta-data') ? 'active' : '' }}" href="{{ route('configuration.website', ['tab' => 'meta-data']) }}"
                            title="@lang('feature/configuration.website.label.tabs3')">
                            @lang('feature/configuration.website.label.tabs3')
                        </a>
                        <a class="list-group-item list-group-item-action {{ (Request::get('tab') == 'social-media') ? 'active' : '' }}" href="{{ route('configuration.website', ['tab' => 'social-media']) }}"
                            title="@lang('feature/configuration.website.label.tabs4')">
                            @lang('feature/configuration.website.label.tabs4')
                        </a>
                        @role ('super')
                        <a class="list-group-item list-group-item-action {{ (Request::get('tab') == 'dev-only') ? 'active' : '' }}" href="{{ route('configuration.website', ['tab' => 'dev-only']) }}"
                            title="@lang('feature/configuration.website.label.tabs5')">
                            @lang('feature/configuration.website.label.tabs5')
                        </a>
                        @endrole
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="tab-content">

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

                                    <div class="text-light small mt-1">
                                        [<span class="text-muted">@lang('global.type_file') : <strong>{{ Str::upper(config('cms.files.config.'.$upload['name'].'.mimes')) }}</strong></span>] - 
                                        [<span class="text-muted">Pixel : <strong>{{ config('cms.files.config.'.$upload['name'].'.pixel') }}</strong></span>] - 
                                        [<span class="text-muted">@lang('global.max_upload') : <strong>{{ config('cms.files.config.'.$upload['name'].'.size') }}</strong></span>]
                                    </div>
                                    @error($upload['name'])
                                    <div class="small mt-1" style="color:#d9534f;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            @endforeach
                        </div>
    
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
                        </div>

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
                        </div>

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
                        </div>

                        {{-- Developer Only --}}
                        @role ('super')
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
                                    @elseif ($dev['name'] == 'default_lang')
                                    <select class="custom-select" name="name[{{ $dev['name'] }}]">
                                        @foreach ($data['languages'] as $key => $value)
                                        <option value="{{ $value['iso_codes'] }}" {{ $value['iso_codes'] == App::getLocale() ? 'selected' : '' }} 
                                            title="{{ $value['iso_codes'] }}">{{ $value['name'] }}</option>
                                        @endforeach
                                    </select>
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
                        </div>
                        @endrole
    
                    </div>
                </div>
            </div>
        </div>

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
    });
</script>
@endsection