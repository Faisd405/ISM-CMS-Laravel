@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/pages/account.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/bootstrap-tagsinput/bootstrap-tagsinput.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-9 col-md-9">

        <form action="{{ !isset($data['widget']) ? route('widget.store', array_merge(['type' => $data['type']], $queryParam)) :
            route('widget.update', array_merge(['type' => $data['type'], 'id' => $data['widget']['id']], $queryParam)) }}" method="POST">
            @csrf

            @if (isset($data['widget']))
                @method('PUT')
            @endif

            <div class="card {{ isset($data['widget']) ? 'hide-form' : '' }}">
                <h5 class="card-header my-2">
                    @lang('global.form_attr', [
                        'attribute' => __('module/widget.caption')
                    ])
                </h5>
                <hr class="border-light m-0">
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/widget.label.name') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control mb-1 @error('name') is-invalid @enderror" name="name"
                                value="{{ !isset($data['widget']) ? old('name') : old('name', $data['widget']['name']) }}"
                                placeholder="@lang('module/widget.placeholder.name')">
                            <small class="form-text">@lang('global.lower_case')</small>
                            @include('components.field-error', ['field' => 'name'])
                        </div>
                    </div>
                </div>
            </div>

            @if (config('cms.module.feature.language.multiple') == true)
            <ul class="nav nav-tabs mb-4">
                @foreach ($data['languages'] as $lang)
                <li class="nav-item">
                    <a class="nav-link{{ $lang['iso_codes'] == config('app.fallback_locale') ? ' active' : '' }}"
                        data-toggle="tab" href="#{{ $lang['iso_codes'] }}">
                        {!! $lang['name'] !!}
                    </a>
                </li>
                @endforeach
            </ul>
            @endif

            <div class="card">
                <div class="tab-content">
                    @foreach ($data['languages'] as $lang)
                    <div class="tab-pane fade{{ $lang['iso_codes'] == config('app.fallback_locale') ? ' show active' : '' }}" id="{{ $lang['iso_codes'] }}">
                        <div class="card-header d-flex justify-content-center">
                            <span class="font-weight-semibold">
                                @lang('global.language') : <b class="text-main">{{ $lang['name'] }}</b>
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/widget.label.title') <i class="text-danger">{{ $data['type'] == 'text' ? '*' : '' }}</i></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control mb-1 @error('title_'.$lang['iso_codes']) is-invalid @enderror" lang="{{ $lang['iso_codes'] }}"
                                        name="title_{{ $lang['iso_codes'] }}"
                                        value="{{ !isset($data['widget']) ? old('title_'.$lang['iso_codes']) : old('title_'.$lang['iso_codes'], $data['widget']->fieldLang('title', $lang['iso_codes'])) }}"
                                        placeholder="@lang('module/widget.placeholder.title')">
                                    @include('components.field-error', ['field' => 'title_'.$lang['iso_codes']])
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/widget.label.description')</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control tiny-mce" name="description_{{ $lang['iso_codes'] }}">{!! !isset($data['widget']) ? old('description_'.$lang['iso_codes']) : old('description_'.$lang['iso_codes'], $data['widget']->fieldLang('description', $lang['iso_codes'])) !!}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <hr class="border-light m-0">
                <div class="card-body">
                    <div class="form-group row hide-form">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/widget.label.widget_set')</label>
                        <div class="col-sm-10">
                            <select class="form-control show-tick" name="widget_set" data-style="btn-default">
                                @foreach (config('cms.module.widget.set') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['widget']) ? (old('widget_set') == ''.$key.'' ? 'selected' : '') : (old('widget_set', $data['widget']['widget_set']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- <h6 class="font-weight-bold text-main mb-4"><b>{{ Str::replace('_', ' ', Str::upper($data['type'])) }}</b></h6> --}}
                    @include('backend.widgets.type.'.$data['type'])
                    @if ($data['type'] != 'text')
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">Order By</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <select class="form-control show-tick" name="config_order_by" data-style="btn-default">
                                    @foreach (config('cms.module.ordering.by') as $key => $value)
                                        <option value="{{ $key }}" {{ !isset($data['widget']) ? (old('config_order_by') == ''.$key.'' ? 'selected' : '') : (old('config_order_by', $data['widget']['config']['order_by']) == ''.$key.'' ? 'selected' : '') }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                <select class="form-control show-tick" name="config_order_type" data-style="btn-default">
                                    @foreach (config('cms.module.ordering.type') as $key => $value)
                                        <option value="{{ $key }}" {{ !isset($data['widget']) ? (old('config_order_type') == ''.$key.'' ? 'selected' : '') : (old('config_order_type', $data['widget']['config']['order_type']) == ''.$key.'' ? 'selected' : '') }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="form-group row {{ isset($data['widget']) && $data['widget']['config']['show_url'] == false ? 'hide-form' : '' }}">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/widget.label.url')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control mb-1 @error('url') is-invalid @enderror" name="url"
                                value="{{ !isset($data['widget']) ? old('url') : old('url', $data['widget']['content']['url']) }}"
                                placeholder="">
                            @include('components.field-error', ['field' => 'url'])
                        </div>
                    </div>
                </div>
                <div class="card-footer justify-content-center">
                    <div class="box-btn">
                        <button class="btn btn-main w-icon" type="submit" name="action" value="back" title="{{ isset($data['widget']) ? __('global.save_change') : __('global.save') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['widget']) ? __('global.save_change') : __('global.save') }}</span>
                        </button>
                        <button class="btn btn-success w-icon" type="submit" name="action" value="exit" title="{{ isset($data['widget']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['widget']) ? __('global.save_change_exit') : __('global.save_exit') }}</span>
                        </button>
                        <button type="reset" class="btn btn-default w-icon" title="{{ __('global.reset') }}">
                            <i class="fi fi-rr-refresh"></i>
                            <span>{{ __('global.reset') }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="card">
                <h6 class="card-header text-main">
                    SETTING
                </h6>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-12 hide-form">
                            <label class="form-label">@lang('global.template')</label>
                            <input type="text" class="form-control mb-1 @error('template') is-invalid @enderror" name="template"
                                value="{{ !isset($data['widget']) ? old('template') : old('template', $data['widget']['template']) }}"
                                placeholder="template-name">
                            @include('components.field-error', ['field' => 'template'])
                        </div>
                        <div class="form-group col-md-12" style="display: none;">
                            <label class="form-label">Content Template</label>
                            <textarea class="my-code-area" rows="10" style="width: 100%" name="content_template">{!! !isset($data['widget']) ? old('content_template') : old('content_template', $data['widget']['content_template']) !!}</textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">@lang('global.status')</label>
                            <select class="form-control show-tick" name="publish" data-style="btn-default">
                                @foreach (__('global.label.publish') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['widget']) ? (old('publish') == ''.$key.'' ? 'selected' : '') : (old('publish', $data['widget']['publish']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">@lang('global.public')</label>
                            <select class="form-control show-tick" name="public" data-style="btn-default">
                                @foreach (__('global.label.optional') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['widget']) ? (old('public') == ''.$key.'' ? 'selected' : '') : (old('public', $data['widget']['public']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="border-light m-0 hide-form">
                <div class="card-body hide-form">
                    <div class="form-row">
                        <div class="form-group col-md-3 form-hide">
                            <label class="form-label">@lang('global.locked')</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="locked" value="1"
                                {{ !isset($data['widget']) ? (old('locked') ? 'checked' : '') : (old('locked', $data['widget']['locked']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                            <small class="form-text">@lang('global.locked_info')</small>
                        </div>
                        <div class="form-group col-md-2 form-hide">
                            <label class="form-label">@lang('module/widget.label.global')</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="global" value="1"
                                    {{ !isset($data['widget']) ? (old('global') ? 'checked' : '') : (old('global', $data['widget']['global']) ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 {{ $data['type'] != 'text' ? 'form-hide' : '' }}">
                            <label class="form-label">Show Image</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_image" value="1"
                                    {{ !isset($data['widget']) ? (old('config_show_image') ? 'checked' : '') : (old('config_show_image', $data['widget']['config']['show_image']) ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 form-hide">
                            <label class="form-label">Show URL</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_url" value="1"
                                    {{ !isset($data['widget']) ? (old('config_show_url') ? 'checked' : '') : (old('config_show_url', $data['widget']['config']['show_url']) ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 form-hide">
                            <label class="form-label">Show Custom Field</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_custom_field" value="1"
                                    {{ !isset($data['widget']) ? (old('config_show_custom_field') ? 'checked' : '') : (old('config_show_custom_field', $data['widget']['config']['show_custom_field']) ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                    </div>
                </div>

                @if (Auth::user()->hasRole('developer|super') || isset($data['widget']) && $data['widget']['config']['show_custom_field'] == true && !empty($data['widget']['custom_fields']))
                {{-- CUSTOM FIELD --}}
                <hr class="border-light m-0">
                <div class="table-responsive text-center">
                    <table class="table card-table">
                        <thead class="text-center">
                            @role('developer|super')
                            <tr>
                                <td colspan="3" class="text-center">
                                    <button id="add_field" type="button" class="btn btn-success btn-sm w-icon">
                                        <i class="fi fi-rr-add"></i> <span>Field</span>
                                    </button>
                                </td>
                            </tr>
                            @endrole
                            <tr>
                                <th>NAME</th>
                                <th>VALUE</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="list_field">
                            @if (isset($data['widget']) && !empty($data['widget']['custom_fields']))
                                @foreach ($data['widget']['custom_fields'] as $key => $val)
                                <tr class="num-list" id="delete-{{ $key }}">
                                    <td>
                                        <input type="text" class="form-control text-bolder" name="cf_name[]" placeholder="name"
                                            value="{{ $key }}" {{ !Auth::user()->hasRole('developer|super') ? 'readonly' : '' }}>
                                    </td>
                                    <td>
                                        <textarea class="form-control text-bolder" name="cf_value[]" placeholder="value">{{ $val }}</textarea>
                                    </td>
                                    @role('developer|super')
                                    <td style="width: 30px;">
                                        <button type="button" class="btn icon-btn btn-sm btn-danger" id="remove_field" data-id="{{ $key }}"><i class="fi fi-rr-cross-small"></i></button>
                                    </td>
                                    @endrole
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </form>

    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.tiny.cloud/1/9p772cxf3cqe1smwkua8bcgyf2lf2sa9ak2cm6tunijg1zr9/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script src="{{ asset('assets/backend/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/bootstrap-tagsinput/bootstrap-tagsinput.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/jquery-ace/ace/ace.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/jquery-ace/ace/theme-monokai.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/jquery-ace/ace/mode-html.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/jquery-ace/jquery-ace.min.js') }}"></script>
@endsection

@section('jsbody')
<script>

    //select2
    $(function () {
        $('.select2').select2();
    });

    //module
    $(document).ready(function () {
        var val = "{{ $data['type'] }}";

        if (val != 'text' || val != 'banner') {
            $('.select-autocomplete').select2({
                minimumInputLength: 1,
                ajax: {
                    url: '/api/module/'+val,
                    dataType: 'json',
                    type: "GET",
                    quietMillis: 50,
                    data: function (params) {
                        return {
                            q: params.term,
                        }
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data.data, function (item) {
                                return {
                                    text: item.title,
                                    id: item.id
                                }
                            })
                        };
                    }
                }
            });
        }
    });

    //custom field
    $(function()  {

        @if(isset($data['widget']) && !empty($data['widget']['custom_fields']))
            var no = {{ count($data['widget']['custom_fields']) }};
        @else
            var no = 1;
        @endif
        $("#add_field").click(function() {
            $("#list_field").append(`
                <tr class="num-list" id="delete-`+no+`">
                    <td>
                        <input type="text" class="form-control" name="cf_name[]" placeholder="name">
                    </td>
                    <td>
                        <textarea class="form-control" name="cf_value[]" placeholder="value"></textarea>
                    </td>
                    <td style="width: 30px;">
                        <button type="button" class="btn icon-btn btn-sm btn-danger" id="remove_field" data-id="`+no+`"><i class="fi fi-rr-cross-small"></i></button>
                    </td>
                </tr>
            `);

            var noOfColumns = $('.num-list').length;
            var maxNum = 10;
            if (noOfColumns < maxNum) {
                $("#add_field").show();
            } else {
                $("#add_field").hide();
            }

            no++;
        });

    });

    //remove custom field
    $(document).on('click', '#remove_field', function() {
    var id = $(this).attr("data-id");
    $("#delete-"+id).remove();
    });

    $('.my-code-area').ace({ theme: 'monokai', lang: 'html' });
</script>

@if (!Auth::user()->hasRole('developer|super'))
<script>
    $('.hide-form').hide();
</script>
@endif

@include('includes.button-fm')
@include('includes.tinymce-fm')
@endsection
