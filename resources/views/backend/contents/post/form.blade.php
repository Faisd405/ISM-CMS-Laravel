@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/pages/account.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/bootstrap-tagsinput/bootstrap-tagsinput.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/bootstrap-material-datetimepicker/bootstrap-material-datetimepicker.css') }}">
<script src="{{ asset('assets/backend/admin.js') }}"></script>
<script src="{{ asset('assets/backend/wysiwyg/tinymce.min.js') }}"></script>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-9 col-md-9">

        <div class="card">
            <h6 class="card-header">
                @lang('global.form_attr', [
                    'attribute' => __('module/content.post.caption')
                ])
            </h6>
            <div class="card-header">
                <span class="text-muted">
                    {{ Str::upper(__('module/content.section.caption')) }} : <b class="text-primary">{{ $data['section']->fieldLang('name') }}</b>
                </span>
            </div>
            <form action="{{ !isset($data['post']) ? route('content.post.store', array_merge(['sectionId' => $data['section']['id']], $queryParam)) : 
                route('content.post.update', array_merge(['sectionId' => $data['section']['id'], 'id' => $data['post']['id']], $queryParam)) }}" method="POST">
                @csrf
                @isset($data['post'])
                    @method('PUT')
                @endisset

                {{-- MAIN --}}
                @if (config('cms.module.feature.language.multiple') == true)
                <div class="list-group list-group-flush account-settings-links flex-row">
                    @foreach ($data['languages'] as $lang)
                    <a class="list-group-item list-group-item-action {{ $lang['iso_codes'] == config('cms.module.feature.language.default') ? 'active' : '' }}" 
                        data-toggle="list" href="#{{ $lang['iso_codes'] }}">
                        {!! $lang['name'] !!}
                    </a>
                    @endforeach
                </div>
                @endif
                <div class="tab-content">
                    @foreach ($data['languages'] as $lang)
                    <div class="tab-pane fade {{ $lang['iso_codes'] == config('cms.module.feature.language.default') ? 'show active' : '' }}" id="{{ $lang['iso_codes'] }}">
                        <div class="card-body pb-2">
        
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/content.post.label.field1') <i class="text-danger">*</i></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control mb-1 {{ !isset($data['post']) ? 'gen_slug' : '' }} @error('title_'.$lang['iso_codes']) is-invalid @enderror" lang="{{ $lang['iso_codes'] }}" 
                                        name="title_{{ $lang['iso_codes'] }}" 
                                        value="{{ !isset($data['post']) ? old('title_'.$lang['iso_codes']) : old('title_'.$lang['iso_codes'], $data['post']->fieldLang('title', $lang['iso_codes'])) }}" 
                                        placeholder="@lang('module/content.post.placeholder.field1')">
                                    @include('components.field-error', ['field' => 'title_'.$lang['iso_codes']])
                                </div>
                            </div>
                            <div class="form-group row {{ isset($data['post']) && $data['post']['config']['show_intro'] == false ? 'hide-form' : '' }}">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/content.post.label.field3')</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control tiny-mce" name="intro_{{ $lang['iso_codes'] }}">{!! !isset($data['post']) ? old('intro_'.$lang['iso_codes']) : old('intro_'.$lang['iso_codes'], $data['post']->fieldLang('intro', $lang['iso_codes'])) !!}</textarea>
                                </div>
                            </div>
                            <div class="form-group row {{ isset($data['post']) && $data['post']['config']['show_intro'] == false ? 'hide-form' : '' }}">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/content.post.label.field4')</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control tiny-mce" name="content_{{ $lang['iso_codes'] }}">{!! !isset($data['post']) ? old('content_'.$lang['iso_codes']) : old('content_'.$lang['iso_codes'], $data['post']->fieldLang('content', $lang['iso_codes'])) !!}</textarea>
                                </div>
                            </div>
        
                        </div>
                    </div>
                    @endforeach
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">@lang('module/content.post.label.field2') <i class="text-danger">*</i></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control slug_spot @error('slug') is-invalid @enderror" lang="{{ App::getLocale() }}" name="slug"
                                    value="{{ !isset($data['post']) ? old('slug') : old('slug', $data['post']['slug']) }}" placeholder="{{ url('/') }}/{{ $data['section']['slug'] }}/url">
                                @include('components.field-error', ['field' => 'slug'])
                            </div>
                        </div>
                        <div class="form-group row {{ Auth::user()->hasRole('developer|super') || config('cms.module.content.category.active') == true && $data['section']['config']['show_category'] == true ? '' : 'hide-form' }}">
                            <label class="col-form-label col-sm-2 text-sm-right">@lang('module/content.category.caption')</label>
                            <div class="col-sm-10">
                                <select class="select2 show-tick" name="category_id[]" data-style="btn-default" {{ $data['section']['config']['multiple_category'] == true ? 'multiple' : '' }}>
                                    <option value=" " selected disabled></option>
                                    @foreach ($data['categories'] as $cat)
                                        <option value="{{ $cat['id'] }}" {{ isset($data['post']) && !empty($data['post']['category_id']) ? (in_array($cat['id'], $data['post']['category_id']) ? 'selected' : '') : '' }}>
                                            {{ $cat->fieldLang('name') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row {{ Auth::user()->hasRole('developer|super') || config('cms.module.master.tags.active') == true && $data['section']['config']['show_tags'] ? '' : 'hide-form' }}">
                            <label class="col-form-label col-sm-2 text-sm-right">@lang('master/tags.caption')</label>
                            <div class="col-sm-10">
                                <input class="form-control tags-input mb-1" id="suggest_tags" name="tags" value="{{ !isset($data['tags']) ? old('tags') : old('tags', $data['tags']) }}" placeholder="">
                                <small class="form-text text-muted">@lang('global.separated_comma')</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" name="action" value="back" title="{{ isset($data['post']) ? __('global.save_change') : __('global.save') }}">
                        <i class="las la-save"></i> {{ isset($data['post']) ? __('global.save_change') : __('global.save') }}
                    </button>&nbsp;&nbsp;
                    <button type="submit" class="btn btn-danger" name="action" value="exit" title="{{ isset($data['post']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                        <i class="las la-save"></i> {{ isset($data['post']) ? __('global.save_change_exit') : __('global.save_exit') }}
                    </button>&nbsp;&nbsp;
                    <button type="reset" class="btn btn-secondary" title="{{ __('global.reset') }}">
                    <i class="las la-redo-alt"></i> {{ __('global.reset') }}
                    </button>
                </div>

                @if (!empty($data['section']['addon_fields']))
                {{-- ADDON FIELD --}}
                <hr class="m-0">
                <div class="card-body">
                    <h6 class="font-weight-bold text-primary mb-4">ADDON FIELD</h6>
                    @isset($data['post'])
                        @include('backend.contents.post.addon-field', ['section' => $data['section'], 'post' => $data['post']])
                    @else
                        @include('backend.contents.post.addon-field', ['section' => $data['section']])
                    @endisset
                </div>
                @endif

                {{-- SEO --}}
                <hr class="m-0">
                <div class="card-body">
                    <h6 class="font-weight-bold text-primary mb-4">SEO</h6>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.meta_title')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control mb-1" name="meta_title" value="{{ !isset($data['post']) ? old('meta_title') : old('meta_title', $data['post']['seo']['title']) }}" placeholder="@lang('global.meta_title')">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.meta_description')</label>
                        <div class="col-sm-10">
                            <textarea class="form-control mb-1" name="meta_description" placeholder="@lang('global.meta_description')">{{ !isset($data['post']) ? old('meta_description') : old('meta_description', $data['post']['seo']['description'])  }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.meta_keywords')</label>
                        <div class="col-sm-10">
                            <input class="form-control tags-input mb-1" name="meta_keywords" value="{{ !isset($data['post']) ? old('meta_keywords') : old('meta_keywords', $data['post']['seo']['keywords'])  }}" placeholder="">
                            <small class="form-text text-muted">@lang('global.separated_comma')</small>
                        </div>
                    </div>
                </div>

                {{-- SETTING --}}
                <hr class="m-0">
                <div class="card-body">
                    <h6 class="font-weight-bold text-primary mb-4">SETTING</h6>
                    <div class="form-group row">
                        <div class="form-group col-md-6">
                            <label class="form-label">@lang('global.status')</label>
                            <select class="form-control show-tick" name="publish" data-style="btn-default">
                                @foreach (__('global.label.publish') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['post']) ? (old('publish') == ''.$key.'' ? 'selected' : '') : (old('publish', $data['post']['publish']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">@lang('global.public')</label>
                            <select class="form-control show-tick" name="public" data-style="btn-default">
                                @foreach (__('global.label.optional') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['post']) ? (old('public') == ''.$key.'' ? 'selected' : '') : (old('public', $data['post']['public']) == ''.$key.'' ? 'selected' : '') }}>
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
                                    <option value="{{ $template['id'] }}" {{ !isset($data['post']) ? (old('template_id') == $template['id'] ? 'selected' : '') : (old('template_id', $data['post']['template_id']) == $template['id'] ? 'selected' : '') }}>
                                        {{ $template['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-12 {{ isset($data['post']) && $data['post']['config']['show_cover'] == false ? 'hide-form' : '' }}">
                            <label class="form-label">@lang('global.cover')</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" id="image1" aria-label="Image" aria-describedby="button-image" name="cover_file"
                                        value="{{ !isset($data['post']) ? old('cover_file') : old('cover_file', $data['post']['cover']['filepath']) }}" placeholder="@lang('global.browse') file...">
                                <div class="input-group-append" title="browse file">
                                    <button class="btn btn-primary file-name" id="button-image" type="button"><i class="las la-image"></i>&nbsp; @lang('global.browse')</button>
                                </div>
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control" name="cover_title" placeholder="@lang('global.title')"
                                    value="{{ !isset($data['post']) ? old('cover_title') : old('cover_title', $data['post']['cover']['title']) }}">
                                <input type="text" class="form-control" name="cover_alt" placeholder="@lang('global.alt')"
                                    value="{{ !isset($data['post']) ? old('cover_alt') : old('cover_alt', $data['post']['cover']['alt']) }}">
                            </div>
                        </div>
                        <div class="form-group col-md-12 {{ isset($data['post']) && $data['post']['config']['show_banner'] == false ? 'hide-form' : '' }}">
                            <label class="form-label">@lang('global.banner')</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" id="image2" aria-label="Image2" aria-describedby="button-image2" name="banner_file"
                                    value="{{ !isset($data['post']) ? old('banner_file') : old('banner_file', $data['post']['banner']['filepath']) }}" placeholder="@lang('global.browse') file...">
                                <div class="input-group-append" title="browse file">
                                    <button class="btn btn-primary file-name" id="button-image2" type="button"><i class="las la-image"></i>&nbsp; @lang('global.browse')</button>
                                </div>
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control" name="banner_title" placeholder="@lang('global.title')"
                                    value="{{ !isset($data['post']) ? old('banner_title') : old('banner_title', $data['post']['banner']['title']) }}">
                                <input type="text" class="form-control" name="banner_alt" placeholder="@lang('global.alt')"
                                    value="{{ !isset($data['post']) ? old('banner_alt') : old('banner_alt', $data['post']['banner']['alt']) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-light m-0">
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label class="form-label">@lang('global.locked')</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="locked" value="1"
                                {{ !isset($data['post']) ? (old('locked') ? 'checked' : '') : (old('locked', $data['post']['locked']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                            <small class="form-text text-muted">@lang('global.locked_info')</small>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="form-label">@lang('global.detail')</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="detail" value="1"
                                    {{ !isset($data['post']) ? (old('detail', $data['section']['config']['detail_post']) ? 'checked' : '') : (old('detail', $data['post']['detail']) ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                            <small class="form-text text-muted">@lang('global.detail_info')</small>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="form-label">Show Intro</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_intro" value="1"
                                {{ !isset($data['post']) ? (old('config_show_intro') ? 'checked' : 'checked') : (old('config_show_intro', $data['post']['config']['show_intro']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="form-label">Show Content</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_content" value="1"
                                {{ !isset($data['post']) ? (old('config_show_content') ? 'checked' : 'checked') : (old('config_show_content', $data['post']['config']['show_content']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="form-label">Show Cover</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_cover" value="1"
                                {{ !isset($data['post']) ? (old('config_show_cover') ? 'checked' : 'checked') : (old('config_show_cover', $data['post']['config']['show_cover']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="form-label">Show Banner</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_banner" value="1"
                                {{ !isset($data['post']) ? (old('config_show_banner') ? 'checked' : 'checked') : (old('config_show_banner', $data['post']['config']['show_banner']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="form-label">Show Media</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_media" value="1"
                                {{ !isset($data['post']) ? (old('config_show_media') ? 'checked' : '') : (old('config_show_media', $data['post']['config']['show_media']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Full Action Media</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_action_media" value="1"
                                {{ !isset($data['post']) ? (old('config_action_media') ? 'checked' : '') : (old('config_action_media', $data['post']['config']['action_media']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Paginate Media</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_paginate_media" value="1"
                                {{ !isset($data['post']) ? (old('config_paginate_media') ? 'checked' : '') : (old('config_paginate_media', $data['post']['config']['paginate_media']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Custom Field</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_custom_field" value="1"
                                {{ !isset($data['post']) ? (old('config_show_custom_field') ? 'checked' : '') : (old('config_show_custom_field', $data['post']['config']['show_custom_field']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Media Limit</label>
                            <input type="number" class="form-control" name="config_media_limit"
                                 value="{{ !isset($data['post']) ? old('config_media_limit', 0) : old('config_media_limit', $data['post']['config']['media_limit']) }}">
                        </div>
                    </div>
                </div>

                @if (Auth::user()->hasRole('developer|super') || isset($data['post']) && $data['post']['config']['show_custom_field'] == true && !empty($data['post']['custom_fields']))
                {{-- CUSTOM FIELD --}}
                <hr class="m-0">
                <div class="table-responsive text-center">
                    <table class="table card-table table-bordered">
                        <thead class="text-center">
                            @role('developer|super')
                            <tr>
                                <td colspan="3" class="text-center">
                                    <button id="add_field" type="button" class="btn btn-success icon-btn-only-sm btn-sm">
                                        <i class="las la-plus"></i> Field
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
                            @if (isset($data['post']) && !empty($data['post']['custom_fields']))
                                @foreach ($data['post']['custom_fields'] as $key => $val)
                                <tr class="num-list" id="delete-{{ $key }}">
                                    <td>
                                        <input type="text" class="form-control" name="cf_name[]" placeholder="name" 
                                            value="{{ $key }}" {{ !Auth::user()->hasRole('developer|super') ? 'readonly' : '' }}>
                                    </td>
                                    <td>
                                        <textarea class="form-control" name="cf_value[]" placeholder="value">{{ $val }}</textarea>
                                    </td>
                                    @role('developer|super')
                                    <td style="width: 30px;">
                                        <button type="button" class="btn icon-btn btn-sm btn-danger" id="remove_field" data-id="{{ $key }}"><i class="las la-times"></i></button>
                                    </td>
                                    @endrole
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                @endif

            </form>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/backend/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/bootstrap-tagsinput/bootstrap-tagsinput.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/bootstrap-material-datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
@endsection

@section('jsbody')
<script>

    //select2
    $(function () {
        $('.select2').select2();
    });

    //tags
    $('.tags-input').tagsinput({ tagClass: 'badge badge-primary' });

    //datepicker
    $( ".datepicker" ).datepicker({
        format: 'yyyy-mm-dd',
        todayHighlight: true,
    });

    //datetime
    $('.datetime-picker').bootstrapMaterialDatePicker({
        date: true,
        shortTime: false,
        format: 'YYYY-MM-DD HH:mm'
    });

    $('#enable_start').click(function() {
        if ($('#enable_start').prop('checked') == false) {
            var valEnd = "{{ now()->format('Y-m-d H:i') }}";
            $('#start_date').val(valEnd);
        } else {
            $('#start_date').val('');
        }
    });

    $('#enable_end').click(function() {
        if ($('#enable_end').prop('checked') == false) {
            var valEnd = "{{ now()->addMonth(1)->format('Y-m-d H:i') }}";
            $('#end_date').val(valEnd);
        } else {
            $('#end_date').val('');
        }
    });

    //custom field
    $(function()  {

        @if(isset($data['post']) && !empty($data['post']['custom_fields']))
            var no = {{ count($data['post']['custom_fields']) }};
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
                        <button type="button" class="btn icon-btn btn-sm btn-danger" id="remove_field" data-id="`+no+`"><i class="las la-times"></i></button>
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