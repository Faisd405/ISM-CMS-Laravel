@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/pages/account.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/bootstrap-tagsinput/bootstrap-tagsinput.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-9 col-md-9">

        <form action="{{ !isset($data['page']) ? route('page.store', ['parent' => Request::get('parent')]) :
            route('page.update', ['id' => $data['page']['id']]) }}" method="POST">
            @csrf
            @isset($data['page'])
                @method('PUT')
                @if ($data['page']['parent'] == 0)
                <input type="hidden" name="index_url_id" value="{{ $data['page']['indexing']['id'] }}">
                @endif
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
                        'attribute' => __('module/page.caption')
                    ])
                </h5>
                <hr class="border-light m-0">
                @if (isset($data['parent']))
                <div class="card-header m-0">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <span>Under</span>
                        </li>
                        <li class="breadcrumb-item active">
                            <b class="text-main">{!! $data['parent']->fieldLang('title') !!}</b>
                        </li>
                    </ol>
                </div>
                <hr class="border-light m-0">
                @endif
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
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/page.label.header_text') </label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control text-bolder @error('header_text_'.$lang['iso_codes']) is-invalid @enderror" lang="{{ $lang['iso_codes'] }}"
                                        name="header_text_{{ $lang['iso_codes'] }}"
                                        value="{{ !isset($data['page']) ? old('header_text_'.$lang['iso_codes']) : (isset($data['page']['header_text'])?old('header_text_'.$lang['iso_codes'], $data['page']->fieldLang('header_text', $lang['iso_codes'])) : old('header_text_'.$lang['iso_codes'])) }}"
                                        placeholder="@lang('module/page.label.header_text')">
                                    @include('components.field-error', ['field' => 'header_text_'.$lang['iso_codes']])
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/page.label.title') <i class="text-danger">*</i></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control text-bolder {{ !isset($data['page']) ? 'gen_slug' : '' }} @error('title_'.$lang['iso_codes']) is-invalid @enderror" lang="{{ $lang['iso_codes'] }}"
                                        name="title_{{ $lang['iso_codes'] }}"
                                        value="{{ !isset($data['page']) ? old('title_'.$lang['iso_codes']) : old('title_'.$lang['iso_codes'], $data['page']->fieldLang('title', $lang['iso_codes'])) }}"
                                        placeholder="@lang('module/page.placeholder.title')">
                                    @include('components.field-error', ['field' => 'title_'.$lang['iso_codes']])
                                </div>
                            </div>
                            <div class="form-group row {{ isset($data['page']) && $data['page']['config']['show_intro'] == false ? 'hide-form' : '' }}">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/page.label.intro')</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control tiny-mce" name="intro_{{ $lang['iso_codes'] }}">{!! !isset($data['page']) ? old('intro_'.$lang['iso_codes']) : old('intro_'.$lang['iso_codes'], $data['page']->fieldLang('intro', $lang['iso_codes'])) !!}</textarea>
                                </div>
                            </div>
                            <div class="form-group row {{ isset($data['page']) && $data['page']['config']['show_content'] == false ? 'hide-form' : '' }}">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/page.label.content')</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control tiny-mce" name="content_{{ $lang['iso_codes'] }}">{!! !isset($data['page']) ? old('content_'.$lang['iso_codes']) : old('content_'.$lang['iso_codes'], $data['page']->fieldLang('content', $lang['iso_codes'])) !!}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <hr class="border-light m-0">
                <div class="card-body">
                    <div class="form-group row {{ Auth::user()->hasRole('developer|super') || Auth::user()->can('page_create') && isset($data['page']) && $data['page']['parent'] > 0 ? '' : 'hide-form' }}">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/page.label.slug') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control text-bolder slug_spot @error('slug') is-invalid @enderror" lang="{{ App::getLocale() }}" name="slug"
                                value="{{ !isset($data['page']) ? old('slug') : old('slug', $data['page']['slug']) }}" placeholder="{{ url('/') }}/url">
                            @include('components.field-error', ['field' => 'slug'])
                        </div>
                    </div>
                    @if (Auth::user()->hasRole('developer|super') || config('cms.module.master.tags.active') == true)
                    <div class="form-group row {{ isset($data['page']) && $data['page']['config']['show_tags'] == false ? 'hide-form' : '' }}">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('master/tags.caption')</label>
                        <div class="col-sm-10">
                            <input class="form-control" data-role="tagsinput" name="tags"
                                value="{{ !isset($data['tags']) ? old('tags') : old('tags', $data['tags']) }}" placeholder="">
                            <small class="form-text">@lang('global.separated_comma')</small>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-footer justify-content-center">
                    <div class="box-btn">
                        <button class="btn btn-main w-icon" type="submit" name="action" value="back" title="{{ isset($data['page']) ? __('global.save_change') : __('global.save') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['page']) ? __('global.save_change') : __('global.save') }}</span>
                        </button>
                        <button class="btn btn-success w-icon" type="submit" name="action" value="exit" title="{{ isset($data['page']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['page']) ? __('global.save_change_exit') : __('global.save_exit') }}</span>
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
                    SEO
                </h6>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.meta_title')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control text-bolder" name="meta_title" value="{{ !isset($data['page']) ? old('meta_title') : old('meta_title', $data['page']['seo']['title']) }}" placeholder="@lang('global.meta_title')">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.meta_description')</label>
                        <div class="col-sm-10">
                            <textarea class="form-control text-bolder" name="meta_description" placeholder="@lang('global.meta_description')">{{ !isset($data['page']) ? old('meta_description') : old('meta_description', $data['page']['seo']['description'])  }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.meta_keywords')</label>
                        <div class="col-sm-10">
                            <input class="form-control" name="meta_keywords" data-role="tagsinput" value="{{ !isset($data['page']) ? old('meta_keywords') : old('meta_keywords', $data['page']['seo']['keywords'])  }}" placeholder="">
                            <small class="form-text">@lang('global.separated_comma')</small>
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
                                    <option value="{{ $key }}" {{ !isset($data['page']) ? (old('publish') == ''.$key.'' ? 'selected' : '') : (old('publish', $data['page']['publish']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">@lang('global.public')</label>
                            <select class="form-control show-tick" name="public" data-style="btn-default">
                                @foreach (__('global.label.optional') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['page']) ? (old('public') == ''.$key.'' ? 'selected' : '') : (old('public', $data['page']['public']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-12 hide-form">
                            <label class="form-label">@lang('global.template')</label>
                            <select class="select2 show-tick" name="template_id" data-style="btn-default">
                                <option value=" " selected>DEFAULT</option>
                                @foreach ($data['templates'] as $template)
                                    <option value="{{ $template['id'] }}" {{ !isset($data['page']) ? (old('template_id') == $template['id'] ? 'selected' : '') : (old('template_id', $data['page']['template_id']) == $template['id'] ? 'selected' : '') }}>
                                        {{ $template['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-12 {{ isset($data['page']) && $data['page']['config']['show_cover'] == false ? 'hide-form' : '' }}">
                            <label class="form-label">@lang('global.cover')</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control text-bolder" id="image1" aria-label="Image" aria-describedby="button-image" name="cover_file" placeholder="@lang('global.browse') file..."
                                        value="{{ !isset($data['page']) ? old('cover_file') : old('cover_file', $data['page']['cover']['filepath']) }}">
                                <div class="input-group-append" title="browse file">
                                    <button class="btn btn-main file-name w-icon" id="button-image" type="button"><i class="fi fi-rr-folder"></i>&nbsp; @lang('global.browse')</button>
                                </div>
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control text-bolder mr-2" name="cover_title" placeholder="@lang('global.title')"
                                    value="{{ !isset($data['page']) ? old('cover_title') : old('cover_title', $data['page']['cover']['title']) }}">
                                <input type="text" class="form-control text-bolder" name="cover_alt" placeholder="@lang('global.alt')"
                                    value="{{ !isset($data['page']) ? old('cover_alt') : old('cover_alt', $data['page']['cover']['alt']) }}">
                            </div>
                        </div>
                        <div class="form-group col-md-12 {{ isset($data['page']) && $data['page']['config']['show_banner'] == false ? 'hide-form' : '' }}">
                            <label class="form-label">@lang('global.banner')</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control text-bolder" id="image2" aria-label="Image2" aria-describedby="button-image2" name="banner_file" placeholder="@lang('global.browse') file..."
                                    value="{{ !isset($data['page']) ? old('banner_file') : old('banner_file', $data['page']['banner']['filepath']) }}">
                                <div class="input-group-append" title="browse file">
                                    <button class="btn btn-main file-name w-icon" id="button-image2" type="button"><i class="fi fi-rr-folder"></i>&nbsp; @lang('global.browse')</button>
                                </div>
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control text-bolder mr-2" name="banner_title" placeholder="@lang('global.title')"
                                    value="{{ !isset($data['page']) ? old('banner_title') : old('banner_title', $data['page']['banner']['title']) }}">
                                <input type="text" class="form-control text-bolder" name="banner_alt" placeholder="@lang('global.alt')"
                                    value="{{ !isset($data['page']) ? old('banner_alt') : old('banner_alt', $data['page']['banner']['alt']) }}">
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
                                {{ !isset($data['page']) ? (old('locked') ? 'checked' : '') : (old('locked', $data['page']['locked']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                            <small class="form-text">@lang('global.locked_info')</small>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">@lang('global.detail')</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="detail" value="1"
                                    {{ !isset($data['page']) ? (old('detail', (isset($data['parent']) ? $data['parent']['config']['detail_child'] : 1)) ? 'checked' : '') : (old('detail', $data['page']['detail']) ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                            <small class="form-text">@lang('global.detail_info')</small>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Header Text</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_header_text" value="1"
                                {{ !isset($data['page']) ? (old('config_show_header_text', 1) ? 'checked' : '') : (isset($data['page']['config']['show_header_text'])?(old('config_show_header_text', $data['page']['config']['show_header_text']) == 1 ? 'checked' : ''):old('config_show_header_text', 1) ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Intro</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_intro" value="1"
                                {{ !isset($data['page']) ? (old('config_show_intro', 1) ? 'checked' : '') : (old('config_show_intro', $data['page']['config']['show_intro']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Content</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_content" value="1"
                                {{ !isset($data['page']) ? (old('config_show_content', 1) ? 'checked' : '') : (old('config_show_content', $data['page']['config']['show_content']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Tags</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_tags" value="1"
                                {{ !isset($data['page']) ? (old('config_show_tags') ? 'checked' : '') : (old('config_show_tags', $data['page']['config']['show_tags']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Cover</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_cover" value="1"
                                {{ !isset($data['page']) ? (old('config_show_cover', 1) ? 'checked' : '') : (old('config_show_cover', $data['page']['config']['show_cover']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Banner</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_banner" value="1"
                                {{ !isset($data['page']) ? (old('config_show_banner', 1) ? 'checked' : '') : (old('config_show_banner', $data['page']['config']['show_banner']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Create Child</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_create_child" value="1"
                                {{ !isset($data['page']) ? (old('config_create_child') ? 'checked' : '') : (old('config_create_child', $data['page']['config']['create_child']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Detail Child</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_detail_child" value="1"
                                {{ !isset($data['page']) ? (old('config_detail_child') ? 'checked' : '') : (old('config_detail_child', $data['page']['config']['detail_child']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Paginate Child</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_paginate_child" value="1"
                                {{ !isset($data['page']) ? (old('config_paginate_child') ? 'checked' : '') : (old('config_paginate_child', $data['page']['config']['paginate_child']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Media</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_media" value="1"
                                {{ !isset($data['page']) ? (old('config_show_media') ? 'checked' : '') : (old('config_show_media', $data['page']['config']['show_media']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Full Action Media</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_action_media" value="1"
                                {{ !isset($data['page']) ? (old('config_action_media') ? 'checked' : '') : (old('config_action_media', $data['page']['config']['action_media']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Paginate Media</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_paginate_media" value="1"
                                {{ !isset($data['page']) ? (old('config_paginate_media') ? 'checked' : '') : (old('config_paginate_media', $data['page']['config']['paginate_media']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Custom Field</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_custom_field" value="1"
                                {{ !isset($data['page']) ? (old('config_show_custom_field') ? 'checked' : '') : (old('config_show_custom_field', $data['page']['config']['show_custom_field']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Child Limit</label>
                            <input type="number" class="form-control" name="config_child_limit"
                                 value="{{ !isset($data['page']) ? old('config_child_limit', 0) : old('config_child_limit', $data['page']['config']['child_limit']) }}">
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Media Limit</label>
                            <input type="number" class="form-control" name="config_media_limit"
                                 value="{{ !isset($data['page']) ? old('config_media_limit', 0) : old('config_media_limit', $data['page']['config']['media_limit']) }}">
                        </div>
                        <div class="form-group col-md-4 hide-form">
                            <label class="form-label">Child Order By</label>
                            <div class="input-group">
                                <select class="form-control show-tick" name="config_child_order_by" data-style="btn-default">
                                    @foreach (config('cms.module.ordering.by') as $key => $value)
                                        <option value="{{ $key }}" {{ !isset($data['page']) ? (old('config_child_order_by', 'position') == ''.$key.'' ? 'selected' : '') : (old('config_child_order_by', $data['page']['config']['child_order_by']) == ''.$key.'' ? 'selected' : '') }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                <select class="form-control show-tick" name="config_child_order_type" data-style="btn-default">
                                    @foreach (config('cms.module.ordering.type') as $key => $value)
                                        <option value="{{ $key }}" {{ !isset($data['page']) ? (old('config_child_order_type', 'ASC') == ''.$key.'' ? 'selected' : '') : (old('config_child_order_type', $data['page']['config']['child_order_type']) == ''.$key.'' ? 'selected' : '') }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                @if (Auth::user()->hasRole('developer|super') || isset($data['page']) && $data['page']['config']['show_custom_field'] == true && !empty($data['page']['custom_fields']))
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
                            @if (isset($data['page']) && !empty($data['page']['custom_fields']))
                                @foreach ($data['page']['custom_fields'] as $key => $val)
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
<script src="{{ env('TINY_MCE_API_KEY') }}" referrerpolicy="origin"></script>

<script src="{{ asset('assets/backend/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/bootstrap-tagsinput/bootstrap-tagsinput.js') }}"></script>
@endsection

@section('jsbody')
<script>
    //select2
    $(function () {
        $('.select2').select2();
    });

    //custom field
    $(function()  {

        @if(isset($data['page']) && !empty($data['page']['custom_fields']))
            var no = {{ count($data['page']['custom_fields']) }};
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
