@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/pages/account.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/select2/select2.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-9 col-md-9">

        <form action="{{ !isset($data['field']) ? route('inquiry.field.store', array_merge(['inquiryId' => $data['inquiry']['id']], $queryParam)) : 
            route('inquiry.field.update', array_merge(['inquiryId' => $data['inquiry']['id'], 'id' => $data['field']['id']], $queryParam)) }}" method="POST" 
                enctype="multipart/form-data">
            @csrf
            @isset($data['field'])
                @method('PUT')
            @endisset

            <div class="card">
                <h5 class="card-header my-2">
                    @lang('global.form_attr', [
                        'attribute' => __('module/inquiry.field.caption')
                    ])
                </h5>
                <hr class="border-light m-0">
                <div class="card-header m-0">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <span>{{ Str::upper(__('module/inquiry.caption')) }}</span>
                        </li>
                        <li class="breadcrumb-item active">
                            <b class="text-main">{{ $data['inquiry']->fieldLang('name') }}</b>
                        </li>
                    </ol>
                </div>
                <hr class="border-light m-0">
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/inquiry.field.label.name') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" 
                            value="{{ !isset($data['field']) ? old('name') : old('name', $data['field']['name']) }}" placeholder="">
                            @include('components.field-error', ['field' => 'name'])
                        </div>
                    </div>
                </div>
            </div>

            @if (config('cms.module.feature.language.multiple') == true)
            <ul class="nav nav-tabs mb-4">
                @foreach ($data['languages'] as $lang)
                <li class="nav-item">
                    <a class="nav-link{{ $lang['iso_codes'] == config('cms.module.feature.language.default') ? ' active' : '' }}"
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
                    <div class="tab-pane fade{{ $lang['iso_codes'] == config('cms.module.feature.language.default') ? ' show active' : '' }}" id="{{ $lang['iso_codes'] }}">
                        <div class="card-header d-flex justify-content-center">
                            <span class="font-weight-semibold">
                                @lang('global.language') : <b class="text-main">{{ $lang['name'] }}</b>
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/inquiry.field.label.label') <i class="text-danger">*</i></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control mb-1 @error('label_'.$lang['iso_codes']) is-invalid @enderror" lang="{{ $lang['iso_codes'] }}" 
                                        name="label_{{ $lang['iso_codes'] }}" 
                                        value="{{ !isset($data['field']) ? old('label_'.$lang['iso_codes']) : old('label_'.$lang['iso_codes'], $data['field']->fieldLang('label', $lang['iso_codes'])) }}">
                                    @include('components.field-error', ['field' => 'label_'.$lang['iso_codes']])
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/inquiry.field.label.placeholder')</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control mb-1 @error('placeholder_'.$lang['iso_codes']) is-invalid @enderror" lang="{{ $lang['iso_codes'] }}" name="placeholder_{{ $lang['iso_codes'] }}" 
                                        value="{{ !isset($data['field']) ? old('placeholder_'.$lang['iso_codes']) : old('placeholder_'.$lang['iso_codes'], $data['field']->fieldLang('placeholder', $lang['iso_codes'])) }}">
                                    @include('components.field-error', ['field' => 'placeholder_'.$lang['iso_codes']])
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card">
                <h6 class="card-header text-main">
                    SETTING
                </h6>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/inquiry.field.label.form_type')</label>
                        <div class="col-sm-10">
                            <select class="select2 show-tick" name="type" data-style="btn-default">
                                @foreach (config('cms.module.inquiry.field.type') as $key => $field)
                                    <option value="{{ $key }}" {{ !isset($data['field']) ? (old('type') == ''.$key.'' ? 'selected' : '') : (old('type', $data['field']['type']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $field }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/inquiry.field.label.type')</label>
                        <div class="col-sm-10">
                            <select class="form-control show-tick" name="property_type" data-style="btn-default">
                                @foreach (config('cms.module.inquiry.field.input') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['field']) ? (old('property_type') == ''.$key.'' ? 'selected' : '') : (old('property_type', $data['field']['properties']['type']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row hide-form">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/inquiry.field.label.class')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('property_class') is-invalid @enderror" name="property_class" 
                                value="{{ !isset($data['field']) ? old('property_class') : old('property_class', $data['field']['properties']['class']) }}" 
                                placeholder="@lang('module/inquiry.field.placeholder.class')">
                        </div>
                    </div>
                    <div class="form-group row hide-form">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/inquiry.field.label.attribute')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('property_attribute') is-invalid @enderror" name="property_attribute" 
                                value="{{ !isset($data['field']) ? old('property_attribute') : old('property_attribute', $data['field']['properties']['attribute']) }}" 
                                placeholder="@lang('module/inquiry.field.placeholder.attribute')">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/inquiry.field.label.validation')</label>
                        <div class="col-sm-10">
                            @foreach (__('module/inquiry.field.validations') as $key => $val)
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="validation[]" value="{{ $key }}" 
                                    {{ isset($data['field']) && !empty($data['field']['validation']) ? (in_array($key, $data['field']['validation']) ? 'checked' : '') : '' }}>
                                <span class="custom-control-label">{{ $val['caption'] }} <small class="text-muted">({{ $val['desc'] }})</small></span>
                              </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/inquiry.field.label.is_unique')</label>
                        <div class="col-sm-10">
                            <label class="switcher switcher-success">
                                <input type="checkbox" class="switcher-input" name="is_unique" value="1" 
                                    {{ !isset($data['field']) ? (old('is_unique') ? 'checked' : '') : (old('is_unique', $data['field']['is_unique']) ? 'checked' : '') }}>
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

                <hr class="border-light m-0 hide-form">
                <div class="card-body hide-form">
                    <table class="table">
                        <thead>
                            <tr>
                                <td colspan="3" class="text-center">
                                    <button id="add_field" type="button" class="btn btn-success btn-sm w-icon">
                                        <i class="fi fi-rr-add"></i> <span>@lang('module/inquiry.field.label.option')</span>
                                    </button>
                                </td>
                            </tr>
                            <tr class="text-center">
                                <th>Label</th>
                                <th>Value</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="list_field">
                            @if (isset($data['field']) && !empty($data['field']['options']))
                                @foreach ($data['field']['options'] as $key => $val)
                                <tr class="num-list" id="delete-{{ $key }}">
                                    <td>
                                        <input type="text" class="form-control text-bolder" name="opt_label[]" placeholder="label" value="{{ $key }}">
                                    </td>
                                    <td>
                                        <textarea class="form-control text-bolder" name="opt_value[]" placeholder="value">{{ $val }}</textarea>
                                    </td>
                                    <td style="width: 30px;">
                                        <button type="button" class="btn icon-btn btn-sm btn-danger" id="remove_field" data-id="{{ $key }}"><i class="fi fi-rr-cross-small"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                <hr class="border-light m-0">
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="form-label">@lang('global.status')</label>
                            <select class="form-control show-tick" name="publish" data-style="btn-default">
                                @foreach (__('global.label.publish') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['field']) ? (old('publish') == ''.$key.'' ? 'selected' : '') : (old('publish', $data['field']['publish']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">@lang('global.public')</label>
                            <select class="form-control show-tick" name="public" data-style="btn-default">
                                @foreach (__('global.label.optional') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['field']) ? (old('public') == ''.$key.'' ? 'selected' : '') : (old('public', $data['field']['public']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4 hide-form">
                            <label class="form-label">@lang('global.locked')</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="locked" value="1"
                                {{ !isset($data['field']) ? (old('locked') ? 'checked' : '') : (old('locked', $data['field']['locked']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                            <small class="form-text">@lang('global.locked_info')</small>
                        </div>
                    </div>
                </div>

                <div class="card-footer justify-content-center">
                    <div class="box-btn">
                        <button class="btn btn-main w-icon" type="submit" name="action" value="back" title="{{ isset($data['field']) ? __('global.save_change') : __('global.save') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['field']) ? __('global.save_change') : __('global.save') }}</span>
                        </button>
                        <button class="btn btn-success w-icon" type="submit" name="action" value="exit" title="{{ isset($data['field']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['field']) ? __('global.save_change_exit') : __('global.save_exit') }}</span>
                        </button>
                        <button type="reset" class="btn btn-default w-icon" title="{{ __('global.reset') }}">
                            <i class="fi fi-rr-refresh"></i>
                            <span>{{ __('global.reset') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/backend/vendor/libs/select2/select2.js') }}"></script>
@endsection

@section('jsbody')
<script>
    //select2
    $(function () {
        $('.select2').select2();
    });

    //custom field
    $(function()  {

    @if(isset($data['field']) && !empty($data['field']['options']))
        var no = {{ count($data['field']['options']) }};
    @else
        var no = 1;
    @endif

    $("#add_field").click(function() {
        $("#list_field").append(`
            <tr class="num-list" id="delete-`+no+`">
                <td>
                    <input type="text" class="form-control text-bolder" name="opt_label[]" placeholder="label">
                </td>
                <td>
                    <textarea class="form-control text-bolder" name="opt_value[]" placeholder="value"></textarea>
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
</script>

@if (!Auth::user()->hasRole('developer|super'))
<script>
    $('.hide-form').hide();
</script>
@endif
@endsection