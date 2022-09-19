@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/pages/account.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/bootstrap-tagsinput/bootstrap-tagsinput.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-9 col-md-9">

        <form action="{{ !isset($data['section']) ? route('content.section.store', $queryParam) : 
            route('content.section.update', array_merge(['id' => $data['section']['id']], $queryParam)) }}" method="POST">
            @csrf
            @isset($data['section'])
                @method('PUT')
                <input type="hidden" name="index_url_id" value="{{ $data['section']['indexing']['id'] }}">
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
                        'attribute' => __('module/content.section.caption')
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
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/content.section.label.name') <i class="text-danger">*</i></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control text-bolder {{ !isset($data['section']) ? 'gen_slug' : '' }} @error('name_'.$lang['iso_codes']) is-invalid @enderror" lang="{{ $lang['iso_codes'] }}" 
                                        name="name_{{ $lang['iso_codes'] }}" 
                                        value="{{ !isset($data['section']) ? old('name_'.$lang['iso_codes']) : old('name_'.$lang['iso_codes'], $data['section']->fieldLang('name', $lang['iso_codes'])) }}" 
                                        placeholder="@lang('module/content.section.placeholder.name')">
                                    @include('components.field-error', ['field' => 'name_'.$lang['iso_codes']])
                                </div>
                            </div>
                            <div class="form-group row {{ isset($data['section']) && $data['section']['config']['show_description'] == false ? 'hide-form' : '' }}">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/content.section.label.description')</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control text-bolder tiny-mce" name="description_{{ $lang['iso_codes'] }}">{!! !isset($data['section']) ? old('description_'.$lang['iso_codes']) : old('description_'.$lang['iso_codes'], $data['section']->fieldLang('description', $lang['iso_codes'])) !!}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <hr class="border-light m-0">
                <div class="card-body hide-form">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/content.section.label.slug') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control text-bolder slug_spot @error('slug') is-invalid @enderror" lang="{{ App::getLocale() }}" name="slug"
                                value="{{ !isset($data['section']) ? old('slug') : old('slug', $data['section']['slug']) }}" placeholder="{{ url('/') }}/url">
                            @include('components.field-error', ['field' => 'slug'])
                        </div>
                    </div>
                </div>
                <div class="card-footer justify-content-center">
                    <div class="box-btn">
                        <button class="btn btn-main w-icon" type="submit" name="action" value="back" title="{{ isset($data['section']) ? __('global.save_change') : __('global.save') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['section']) ? __('global.save_change') : __('global.save') }}</span>
                        </button>
                        <button class="btn btn-success w-icon" type="submit" name="action" value="exit" title="{{ isset($data['section']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['section']) ? __('global.save_change_exit') : __('global.save_exit') }}</span>
                        </button>
                        <button type="reset" class="btn btn-default w-icon" title="{{ __('global.reset') }}">
                            <i class="fi fi-rr-refresh"></i>
                            <span>{{ __('global.reset') }}</span>
                        </button>
                    </div>
                </div>
            </div>

            @role('developer|super')
            <div class="card">
                <table class="table card-table table-bordered">
                    <thead class="text-center">
                        <tr>
                            <td colspan="4">
                                <button id="add_addon_field" type="button" class="btn btn-success btn-sm w-icon">
                                    <i class="fi fi-rr-add"></i> <span>Addon Field</span>
                                </button>
                            </td>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <tr>
                            <td colspan="4">
                                <small class="form-text">Checbox field caption format (JSON) : ["Field Caption",{"OPTION_VALUE1":"Option caption 1","OPTION_VALUE2":"Option caption 2"}]</small>
                            </td>
                        </tr>
                        <tr>
                            <th>NAME</th>
                            <th>TYPE</th>
                            <th>VALUE</th>
                            <th></th>
                        </tr>
                    </tbody>
                    <tbody id="list_addon_field">
                        @if (isset($data['section']) && !empty($data['section']['addon_fields']))
                            @foreach ($data['section']['addon_fields'] as $key => $val)
                            <tr class="num-addon-list" id="delete-addon-{{ $key }}">
                                <td>
                                    <input type="text" class="form-control text-bolder" name="af_name[]" placeholder="name" 
                                        value="{{ $val['name'] }}" {{ !Auth::user()->hasRole('super') ? 'readonly' : '' }}>
                                </td>
                                <td>
                                    <select class="form-control show-tick" name="af_type[]" data-style="btn-default">
                                        @foreach (config('cms.module.content.section.addon_field') as $keyT => $valT)
                                            <option value="{{ $valT }}" {{ $valT == $val['type'] ? 'selected' : '' }}>
                                                {{ $valT }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <textarea class="form-control text-bolder" name="af_value[]" placeholder="value">{{ $val['value'] }}</textarea>
                                </td>
                                <td style="width: 30px;">
                                    <button type="button" class="btn icon-btn btn-sm btn-danger" id="remove_addon_field" data-id="{{ $key }}"><i class="fi fi-rr-cross-small"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            @endrole

            <div class="card">
                <h6 class="card-header text-main">
                    SEO
                </h6>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.meta_title')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control text-bolder" name="meta_title" value="{{ !isset($data['section']) ? old('meta_title') : old('meta_title', $data['section']['seo']['title']) }}" placeholder="@lang('global.meta_title')">
        
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.meta_description')</label>
                        <div class="col-sm-10">
                            <textarea class="form-control text-bolder" name="meta_description" placeholder="@lang('global.meta_description')">{{ !isset($data['section']) ? old('meta_description') : old('meta_description', $data['section']['seo']['description'])  }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.meta_keywords')</label>
                        <div class="col-sm-10">
                            <input class="form-control" name="meta_keywords" data-role="tagsinput" value="{{ !isset($data['section']) ? old('meta_keywords') : old('meta_keywords', $data['section']['seo']['keywords'])  }}" placeholder="">
                            <small class="form-text text-muted">@lang('global.separated_comma')</small>
                        </div>
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
                                    <option value="{{ $key }}" {{ !isset($data['section']) ? (old('publish') == ''.$key.'' ? 'selected' : '') : (old('publish', $data['section']['publish']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">@lang('global.public')</label>
                            <select class="form-control show-tick" name="public" data-style="btn-default">
                                @foreach (__('global.label.optional') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['section']) ? (old('public') == ''.$key.'' ? 'selected' : '') : (old('public', $data['section']['public']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4 hide-form">
                            <label class="form-label">@lang('module/content.section.label.template_list')</label>
                            <select class="select2 show-tick" name="template_list_id" data-style="btn-default">
                                <option value=" " selected>DEFAULT</option>
                                @foreach ($data['template_lists'] as $tmpList)
                                    <option value="{{ $tmpList['id'] }}" {{ !isset($data['section']) ? (old('template_list_id') == $tmpList['id'] ? 'selected' : '') : (old('template_list_id', $data['section']['template_list_id']) == $tmpList['id'] ? 'selected' : '') }}>
                                        {{ $tmpList['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4 hide-form">
                            <label class="form-label">@lang('module/content.section.label.template_detail') Category</label>
                            <select class="select2 show-tick" name="template_detail_category_id" data-style="btn-default">
                                <option value=" " selected>DEFAULT</option>
                                @foreach ($data['template_category_details'] as $tmpDetailCat)
                                    <option value="{{ $tmpDetailCat['id'] }}" {{ !isset($data['section']) ? (old('template_detail_category_id') == $tmpDetailCat['id'] ? 'selected' : '') : (old('template_detail_category_id', $data['section']['template_detail_category_id']) == $tmpDetailCat['id'] ? 'selected' : '') }}>
                                        {{ $tmpDetailCat['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4 hide-form">
                            <label class="form-label">@lang('module/content.section.label.template_detail') Post</label>
                            <select class="select2 show-tick" name="template_detail_post_id" data-style="btn-default">
                                <option value=" " selected>DEFAULT</option>
                                @foreach ($data['template_post_details'] as $tmpDetailPost)
                                    <option value="{{ $tmpDetailPost['id'] }}" {{ !isset($data['section']) ? (old('template_detail_post_id') == $tmpDetailPost['id'] ? 'selected' : '') : (old('template_detail_post_id', $data['section']['template_detail_post_id']) == $tmpDetailPost['id'] ? 'selected' : '') }}>
                                        {{ $tmpDetailPost['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-12 {{ isset($data['section']) && $data['section']['config']['show_cover'] == false ? 'hide-form' : '' }}">
                            <label class="form-label">@lang('global.cover')</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control text-bolder" id="image1" aria-label="Image" aria-describedby="button-image" name="cover_file"
                                        value="{{ !isset($data['section']) ? old('cover_file') : old('cover_file', $data['section']['cover']['filepath']) }}" placeholder="@lang('global.browse') file...">
                                <div class="input-group-append" title="browse file">
                                    <button class="btn btn-main file-name w-icon" id="button-image" type="button"><i class="fi fi-rr-folder"></i>&nbsp; @lang('global.browse')</button>
                                </div>
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control text-bolder" name="cover_title" placeholder="@lang('global.title')"
                                    value="{{ !isset($data['section']) ? old('cover_title') : old('cover_title', $data['section']['cover']['title']) }}">
                                <input type="text" class="form-control text-bolder" name="cover_alt" placeholder="@lang('global.alt')"
                                    value="{{ !isset($data['section']) ? old('cover_alt') : old('cover_alt', $data['section']['cover']['alt']) }}">
                            </div>
                        </div>
                        <div class="form-group col-md-12 {{ isset($data['section']) && $data['section']['config']['show_banner'] == false ? 'hide-form' : '' }}">
                            <label class="form-label">@lang('global.banner')</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control text-bolder" id="image2" aria-label="Image2" aria-describedby="button-image2" name="banner_file"
                                    value="{{ !isset($data['section']) ? old('banner_file') : old('banner_file', $data['section']['banner']['filepath']) }}" placeholder="@lang('global.browse') file...">
                                <div class="input-group-append" title="browse file">
                                    <button class="btn btn-main file-name w-icon" id="button-image2" type="button"><i class="fi fi-rr-folder"></i>&nbsp; @lang('global.browse')</button>
                                </div>
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control text-bolder" name="banner_title" placeholder="@lang('global.title')"
                                    value="{{ !isset($data['section']) ? old('banner_title') : old('banner_title', $data['section']['banner']['title']) }}">
                                <input type="text" class="form-control text-bolder" name="banner_alt" placeholder="@lang('global.alt')"
                                    value="{{ !isset($data['section']) ? old('banner_alt') : old('banner_alt', $data['section']['banner']['alt']) }}">
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
                                {{ !isset($data['section']) ? (old('locked') ? 'checked' : '') : (old('locked', $data['section']['locked']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                            <small class="form-text">@lang('global.locked_info')</small>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">@lang('global.detail')</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="detail" value="1"
                                    {{ !isset($data['section']) ? (old('detail') ? 'checked' : 'checked') : (old('detail', $data['section']['detail']) ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                            <small class="form-text">@lang('global.detail_info')</small>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Description</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_description" value="1"
                                {{ !isset($data['section']) ? (old('config_show_description', 1) ? 'checked' : '') : (old('config_show_description', $data['section']['config']['show_description']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Cover</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_cover" value="1"
                                {{ !isset($data['section']) ? (old('config_show_cover', 1) ? 'checked' : '') : (old('config_show_cover', $data['section']['config']['show_cover']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Banner</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_banner" value="1"
                                {{ !isset($data['section']) ? (old('config_show_banner', 1) ? 'checked' : '') : (old('config_show_banner', $data['section']['config']['show_banner']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Category</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_category" value="1"
                                {{ !isset($data['section']) ? (old('config_show_category', 1) ? 'checked' : '') : (old('config_show_category', $data['section']['config']['show_category']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Multiple Category</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_multiple_category" value="1"
                                {{ !isset($data['section']) ? (old('config_multiple_category') ? 'checked' : '') : (old('config_multiple_category', $data['section']['config']['multiple_category']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Post</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_post" value="1"
                                {{ !isset($data['section']) ? (old('config_show_post', 1) ? 'checked' : '') : (old('config_show_post', $data['section']['config']['show_post']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Post Selected</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_post_selected" value="1"
                                {{ !isset($data['section']) ? (old('config_post_selected') ? 'checked' : '') : (old('config_post_selected', $data['section']['config']['post_selected']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Tags</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_tags" value="1"
                                {{ !isset($data['section']) ? (old('config_show_tags') ? 'checked' : '') : (old('config_show_tags', $data['section']['config']['show_tags']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Latest Post</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_latest_post" value="1"
                                {{ !isset($data['section']) ? (old('config_latest_post', 1) ? 'checked' : '') : (old('config_latest_post', $data['section']['config']['latest_post']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Detail Category</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_detail_category" value="1"
                                {{ !isset($data['section']) ? (old('config_detail_category') ? 'checked' : '') : (old('config_detail_category', $data['section']['config']['detail_category']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Detail Post</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_detail_post" value="1"
                                {{ !isset($data['section']) ? (old('config_detail_post', 1) ? 'checked' : '') : (old('config_detail_post', $data['section']['config']['detail_post']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Paginate Category</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_paginate_category" value="1"
                                {{ !isset($data['section']) ? (old('config_paginate_category') ? 'checked' : '') : (old('config_paginate_category', $data['section']['config']['paginate_category']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Paginate Post</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_paginate_post" value="1"
                                {{ !isset($data['section']) ? (old('config_paginate_post', 1) ? 'checked' : '') : (old('config_paginate_post', $data['section']['config']['paginate_post']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Media Post</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_media" value="1"
                                {{ !isset($data['section']) ? (old('config_show_media') ? 'checked' : '') : (old('config_show_media', $data['section']['config']['show_media']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Custom Field</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_custom_field" value="1"
                                {{ !isset($data['section']) ? (old('config_show_custom_field') ? 'checked' : '') : (old('config_show_custom_field', $data['section']['config']['show_custom_field']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-3 hide-form">
                            <label class="form-label">Category Limit</label>
                            <input type="number" class="form-control text-bolder" name="config_category_limit"
                                 value="{{ !isset($data['section']) ? old('config_category_limit', 6) : old('config_category_limit', $data['section']['config']['category_limit']) }}">
                        </div>
                        <div class="form-group col-md-3 hide-form">
                            <label class="form-label">Post Limit</label>
                            <input type="number" class="form-control text-bolder" name="config_post_limit"
                                 value="{{ !isset($data['section']) ? old('config_post_limit', 6) : old('config_post_limit', $data['section']['config']['post_limit']) }}">
                        </div>
                        <div class="form-group col-md-3 hide-form">
                            <label class="form-label">Latest Post Limit</label>
                            <input type="number" class="form-control text-bolder" name="config_latest_post_limit"
                                 value="{{ !isset($data['section']) ? old('config_latest_post_limit', 4) : old('config_latest_post_limit', $data['section']['config']['latest_post_limit']) }}">
                        </div>
                        <div class="form-group col-md-6 hide-form">
                            <label class="form-label">Post Order By</label>
                            <div class="input-group">
                                <select class="form-control show-tick" name="config_post_order_by" data-style="btn-default">
                                    @foreach (config('cms.module.content.post.ordering') as $key => $value)
                                        <option value="{{ $key }}" {{ !isset($data['section']) ? (old('config_post_order_by') == ''.$key.'' ? 'selected' : '') : (old('config_post_order_by', $data['section']['config']['post_order_by']) == ''.$key.'' ? 'selected' : '') }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                <select class="form-control show-tick" name="config_post_order_type" data-style="btn-default">
                                    @foreach (config('cms.module.ordering.type') as $key => $value)
                                        <option value="{{ $key }}" {{ !isset($data['section']) ? (old('config_post_order_type') == ''.$key.'' ? 'selected' : '') : (old('config_post_order_type', $data['section']['config']['post_order_type']) == ''.$key.'' ? 'selected' : '') }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                @if (Auth::user()->hasRole('developer|super') || isset($data['section']) && $data['section']['config']['show_custom_field'] == true && !empty($data['section']['custom_fields']))
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
                            @if (isset($data['section']) && !empty($data['section']['custom_fields']))
                                @foreach ($data['section']['custom_fields'] as $key => $val)
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
<script src="{{ asset('assets/backend/vendor/libs/bootstrap-tagsinput/bootstrap-tagsinput.js') }}"></script>
@endsection

@section('jsbody')
<script>

    //select2
    $(function () {
        $('.select2').select2();
    });

    //addon field
    $(function()  {

        @if(isset($data['section']) && !empty($data['section']['addon_fields']))
            var no = {{ count($data['section']['addon_fields']) }};
        @else
            var no = 1;
        @endif
        $("#add_addon_field").click(function() {
            $("#list_addon_field").append(`
                <tr class="num-addon-list" id="delete-addon-`+no+`">
                    <td>
                        <input type="text" class="form-control text-bolder" name="af_name[]" placeholder="name">
                    </td>
                    <td>
                        <select class="form-control show-tick" name="af_type[]" data-style="btn-default">
                            @foreach (config('cms.module.content.section.addon_field') as $key => $value)
                                <option value="{{ $value }}">
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <textarea class="form-control text-bolder" name="af_value[]" placeholder="value"></textarea>
                    </td>
                    <td style="width: 30px;">
                        <button type="button" class="btn icon-btn btn-sm btn-danger" id="remove_addon_field" data-id="`+no+`"><i class="fi fi-rr-cross-small"></i></button>
                    </td>
                </tr>
            `);

            var noOfColumns = $('.num-addon-list').length;
            var maxNum = 10;
            if (noOfColumns < maxNum) {
                $("#add_addon_field").show();
            } else {
                $("#add_addon_field").hide();
            }

            no++;
        });

    });

    //custom field
    $(function()  {

        @if(isset($data['section']) && !empty($data['section']['custom_fields']))
            var no = {{ count($data['section']['custom_fields']) }};
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

    //remove addon field
    $(document).on('click', '#remove_addon_field', function() {
        var id = $(this).attr("data-id");
        $("#delete-addon-"+id).remove();
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