@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/pages/account.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/select2/select2.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-9 col-md-9">

        <form action="{{ !isset($data['category']) ? route('gallery.category.store', $queryParam) : 
            route('gallery.category.update', array_merge(['id' => $data['category']['id']], $queryParam)) }}" method="POST">
            @csrf
            @isset($data['category'])
                @method('PUT')
            @endisset

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
                <h5 class="card-header my-2">
                    @lang('global.form_attr', [
                        'attribute' => __('module/gallery.category.caption')
                    ])
                </h5>
                <hr class="border-light m-0">
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
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/content.category.label.name') <i class="text-danger">*</i></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control text-bolder {{ !isset($data['category']) ? 'gen_slug' : '' }} @error('name_'.$lang['iso_codes']) is-invalid @enderror" lang="{{ $lang['iso_codes'] }}" 
                                        name="name_{{ $lang['iso_codes'] }}" 
                                        value="{{ !isset($data['category']) ? old('name_'.$lang['iso_codes']) : old('name_'.$lang['iso_codes'], $data['category']->fieldLang('name', $lang['iso_codes'])) }}" 
                                        placeholder="@lang('module/content.category.placeholder.name')">
                                    @include('components.field-error', ['field' => 'name_'.$lang['iso_codes']])
                                </div>
                            </div>
                            <div class="form-group row {{ isset($data['category']) && $data['category']['config']['show_description'] == false ? 'hide-form' : '' }}">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/content.category.label.description')</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control tiny-mce" name="description_{{ $lang['iso_codes'] }}">{!! !isset($data['category']) ? old('description_'.$lang['iso_codes']) : old('description_'.$lang['iso_codes'], $data['category']->fieldLang('description', $lang['iso_codes'])) !!}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <hr class="border-light m-0">
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/gallery.category.label.slug') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control text-bolder slug_spot @error('slug') is-invalid @enderror" lang="{{ App::getLocale() }}" name="slug"
                                value="{{ !isset($data['category']) ? old('slug') : old('slug', $data['category']['slug']) }}" placeholder="{{ url('/') }}/gallery/cat/url">
                            @include('components.field-error', ['field' => 'slug'])
                        </div>
                    </div>
                </div>
                <div class="card-footer justify-content-center">
                    <div class="box-btn">
                        <button class="btn btn-main w-icon" type="submit" name="action" value="back" title="{{ isset($data['category']) ? __('global.save_change') : __('global.save') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['category']) ? __('global.save_change') : __('global.save') }}</span>
                        </button>
                        <button class="btn btn-success w-icon" type="submit" name="action" value="exit" title="{{ isset($data['category']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['category']) ? __('global.save_change_exit') : __('global.save_exit') }}</span>
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
                        <div class="form-group col-md-6">
                            <label class="form-label">@lang('global.status')</label>
                            <select class="form-control show-tick" name="publish" data-style="btn-default">
                                @foreach (__('global.label.publish') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['category']) ? (old('publish') == ''.$key.'' ? 'selected' : '') : (old('publish', $data['category']['publish']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">@lang('global.public')</label>
                            <select class="form-control show-tick" name="public" data-style="btn-default">
                                @foreach (__('global.label.optional') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['category']) ? (old('public') == ''.$key.'' ? 'selected' : '') : (old('public', $data['category']['public']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6 hide-form">
                            <label class="form-label">@lang('module/gallery.category.label.template_list')</label>
                            <select class="select2 show-tick" name="template_list_id" data-style="btn-default">
                                <option value=" " selected>DEFAULT</option>
                                @foreach ($data['template_lists'] as $tmpList)
                                    <option value="{{ $tmpList['id'] }}" {{ !isset($data['category']) ? (old('template_list_id') == $tmpList['id'] ? 'selected' : '') : (old('template_list_id', $data['category']['template_list_id']) == $tmpList['id'] ? 'selected' : '') }}>
                                        {{ $tmpList['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6 hide-form">
                            <label class="form-label">@lang('module/gallery.category.label.template_detail')</label>
                            <select class="select2 show-tick" name="template_detail_id" data-style="btn-default">
                                <option value=" " selected>DEFAULT</option>
                                @foreach ($data['template_details'] as $tmpDetail)
                                    <option value="{{ $tmpDetail['id'] }}" {{ !isset($data['category']) ? (old('template_detail_id') == $tmpDetail['id'] ? 'selected' : '') : (old('template_detail_id', $data['category']['template_detail_id']) == $tmpDetail['id'] ? 'selected' : '') }}>
                                        {{ $tmpDetail['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-12 {{ isset($data['category']) && $data['category']['config']['show_cover'] == false ? 'hide-form' : '' }}">
                            <label class="form-label">@lang('global.cover')</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control text-bolder" id="image1" aria-label="Image" aria-describedby="button-image" name="cover_file"
                                        value="{{ !isset($data['category']) ? old('cover_file') : old('cover_file', $data['category']['cover']['filepath']) }}" placeholder="@lang('global.browse') file...">
                                <div class="input-group-append" title="browse file">
                                    <button class="btn btn-main file-name w-icon" id="button-image" type="button"><i class="fi fi-rr-folder"></i>&nbsp; @lang('global.browse')</button>
                                </div>
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control text-bolder" name="cover_title" placeholder="@lang('global.title')"
                                    value="{{ !isset($data['category']) ? old('cover_title') : old('cover_title', $data['category']['cover']['title']) }}">
                                <input type="text" class="form-control text-bolder" name="cover_alt" placeholder="@lang('global.alt')"
                                    value="{{ !isset($data['category']) ? old('cover_alt') : old('cover_alt', $data['category']['cover']['alt']) }}">
                            </div>
                        </div>
                        <div class="form-group col-md-12 {{ isset($data['category']) && $data['category']['config']['show_banner'] == false ? 'hide-form' : '' }}">
                            <label class="form-label">@lang('global.banner')</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control text-bolder" id="image2" aria-label="Image2" aria-describedby="button-image2" name="banner_file"
                                    value="{{ !isset($data['category']) ? old('banner_file') : old('banner_file', $data['category']['banner']['filepath']) }}" placeholder="@lang('global.browse') file...">
                                <div class="input-group-append" title="browse file">
                                    <button class="btn btn-main file-name w-icon" id="button-image2" type="button"><i class="fi fi-rr-folder"></i>&nbsp; @lang('global.browse')</button>
                                </div>
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control text-bolder" name="banner_title" placeholder="@lang('global.title')"
                                    value="{{ !isset($data['category']) ? old('banner_title') : old('banner_title', $data['category']['banner']['title']) }}">
                                <input type="text" class="form-control text-bolder" name="banner_alt" placeholder="@lang('global.alt')"
                                    value="{{ !isset($data['category']) ? old('banner_alt') : old('banner_alt', $data['category']['banner']['alt']) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-light m-0 hide-form">
                <div class="card-body hide-form">
                    <div class="form-row">
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">@lang('global.locked')</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="locked" value="1"
                                {{ !isset($data['category']) ? (old('locked') ? 'checked' : '') : (old('locked', $data['category']['locked']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                            <small class="form-text">@lang('global.locked_info')</small>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">@lang('global.detail')</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="detail" value="1"
                                    {{ !isset($data['category']) ? (old('detail') ? 'checked' : 'checked') : (old('detail', $data['category']['detail']) ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                            <small class="form-text">@lang('global.detail_info')</small>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Description</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_description" value="1"
                                {{ !isset($data['category']) ? (old('config_show_description', 1) ? 'checked' : '') : (old('config_show_description', $data['category']['config']['show_description']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Cover</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_cover" value="1"
                                {{ !isset($data['category']) ? (old('config_show_cover', 1) ? 'checked' : '') : (old('config_show_cover', $data['category']['config']['show_cover']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Banner</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_banner" value="1"
                                {{ !isset($data['category']) ? (old('config_show_banner', 1) ? 'checked' : '') : (old('config_show_banner', $data['category']['config']['show_banner']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Paginate Album</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_paginate_album" value="1"
                                {{ !isset($data['category']) ? (old('config_paginate_album', 1) ? 'checked' : '') : (old('config_paginate_album', $data['category']['config']['paginate_album']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Paginate File</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_paginate_file" value="1"
                                {{ !isset($data['category']) ? (old('config_paginate_file', 1) ? 'checked' : '') : (old('config_paginate_file', $data['category']['config']['paginate_file']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Custom Field</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_custom_field" value="1"
                                {{ !isset($data['category']) ? (old('config_show_custom_field') ? 'checked' : '') : (old('config_show_custom_field', $data['category']['config']['show_custom_field']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-3 hide-form">
                            <label class="form-label">Album Limit</label>
                            <input type="number" class="form-control text-bolder" name="config_album_limit"
                                 value="{{ !isset($data['category']) ? old('config_album_limit', 6) : old('config_album_limit', $data['category']['config']['album_limit']) }}">
                        </div>
                        <div class="form-group col-md-3 hide-form">
                            <label class="form-label">File Limit</label>
                            <input type="number" class="form-control text-bolder" name="config_file_limit"
                                 value="{{ !isset($data['category']) ? old('config_file_limit', 12) : old('config_file_limit', $data['category']['config']['file_limit']) }}">
                        </div>
                    </div>
                </div>

                @if (Auth::user()->hasRole('developer|super') || isset($data['category']) && $data['category']['config']['show_custom_field'] == true && !empty($data['category']['custom_fields']))
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
                            @if (isset($data['category']) && !empty($data['category']['custom_fields']))
                                @foreach ($data['category']['custom_fields'] as $key => $val)
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
<script src="{{ asset('assets/backend/js/admin.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/wysiwyg/tinymce.min.js') }}"></script>
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

        @if(isset($data['category']) && !empty($data['category']['custom_fields']))
            var no = {{ count($data['category']['custom_fields']) }};
        @else
            var no = 1;
        @endif
        $("#add_field").click(function() {
            $("#list_field").append(`
                <tr class="num-list" id="delete-`+no+`">
                    <td>
                        <input type="text" class="form-control text-bolder" name="cf_name[]" placeholder="name">
                    </td>
                    <td>
                        <textarea class="form-control text-bolder" name="cf_value[]" placeholder="value"></textarea>
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

@include('includes.button-fm')
@include('includes.tinymce-fm')
@endsection