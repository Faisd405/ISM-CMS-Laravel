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
            <form action="{{ !isset($data['post']) ? route('content.post.store', ['sectionId' => $data['section']['id']]) : 
                route('content.post.update', ['sectionId' => $data['section']['id'], 'id' => $data['post']['id']]) }}" method="POST">
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
                                    <input type="text" class="form-control mb-1 gen_slug @error('title_'.$lang['iso_codes']) is-invalid @enderror" lang="{{ $lang['iso_codes'] }}" 
                                        name="title_{{ $lang['iso_codes'] }}" 
                                        value="{{ !isset($data['post']) ? old('title_'.$lang['iso_codes']) : old('title_'.$lang['iso_codes'], $data['post']->fieldLang('title', $lang['iso_codes'])) }}" 
                                        placeholder="@lang('module/content.post.placeholder.field1')">
                                    @include('components.field-error', ['field' => 'title_'.$lang['iso_codes']])
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/content.post.label.field3')</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control tiny-mce" name="intro_{{ $lang['iso_codes'] }}">{!! !isset($data['post']) ? old('intro_'.$lang['iso_codes']) : old('intro_'.$lang['iso_codes'], $data['post']->fieldLang('intro', $lang['iso_codes'])) !!}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
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
                                    value="{{ !isset($data['post']) ? old('slug') : old('slug', $data['post']['slug']) }}" placeholder="{{ url('/') }}/{{ $data['section']['slug'] }}/SLUG">
                                @include('components.field-error', ['field' => 'slug'])
                            </div>
                        </div>
                        @if (config('cms.module.content.category.active') == true)   
                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">@lang('module/content.category.caption')</label>
                            <div class="col-sm-10">
                                <select class="select2 show-tick" name="category_id[]" data-style="btn-default" multiple>
                                    @foreach ($data['categories'] as $cat)
                                        <option value="{{ $cat['id'] }}" {{ isset($data['post']) && !empty($data['post']['category_id']) ? (in_array($cat['id'], $data['post']['category_id']) ? 'selected' : '') : '' }}>
                                            {{ $cat->fieldLang('name') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        @if (config('cms.module.master.tags.active') == true)
                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">@lang('master/tags.caption')</label>
                            <div class="col-sm-10">
                                <input class="form-control tags-input mb-1" id="suggest_tags" name="tags" value="{{ !isset($data['tags']) ? old('tags') : old('tags', $data['tags']) }}" placeholder="">
                                <small class="text-muted">@lang('global.separated_comma')</small>
                            </div>
                        </div>
                        @endif
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
                    <h6 class="font-weight-semibold mb-4">ADDON FIELD</h6>
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
                    <h6 class="font-weight-semibold mb-4">SEO</h6>
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
                            <small class="text-muted">@lang('global.separated_comma')</small>
                        </div>
                    </div>
                </div>

                {{-- POST SETTING --}}
                <hr class="m-0">
                <div class="card-body">
                    <h6 class="font-weight-semibold mb-4">POST SETTING</h6>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.status')</label>
                        <div class="col-sm-10">
                            <select class="form-control show-tick" name="publish" data-style="btn-default">
                                @foreach (__('global.label.publish') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['post']) ? (old('publish') == ''.$key.'' ? 'selected' : '') : (old('publish', $data['post']['publish']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">Post by alias</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control mb-1 @error('posted_by_alias') is-invalid @enderror" name="posted_by_alias" 
                                value="{{ !isset($data['post']) ? old('posted_by_alias') : old('posted_by_alias', $data['post']['posted_by_alias']) }}" 
                                placeholder="">
                            @include('components.field-error', ['field' => 'posted_by_alias'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('module/content.post.label.publish_time')</label>
                        </div>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input id="start_date" type="text" class="datetime-picker form-control @error('publish_time') is-invalid @enderror" name="publish_time"
                                    value="{{ !isset($data['post']) ? old('publish_time', now()->format('Y-m-d H:i')) : (!empty($data['post']['publish_time']) ? 
                                        old('publish_time', $data['post']['publish_time']->format('Y-m-d H:i')) : old('publish_time')) }}" 
                                    placeholder="">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="las la-calendar"></i></span>
                                    <span class="input-group-text">
                                        <input type="checkbox" id="enable_start" value="1">&nbsp; NULL
                                    </span>
                                </div>
                                @include('components.field-error', ['field' => 'publish_time'])
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('module/content.post.label.publish_end')</label>
                        </div>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input id="end_date" type="text" class="datetime-picker form-control @error('publish_end') is-invalid @enderror" name="publish_end"
                                    value="{{ !isset($data['post']) ? old('publish_end') : (!empty($data['post']['publish_end']) ? 
                                        old('publish_end', $data['post']['publish_end']->format('Y-m-d H:i')) : old('publish_end')) }}" 
                                    placeholder="">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="las la-calendar"></i></span>
                                    <span class="input-group-text">
                                        <input type="checkbox" id="enable_end" value="1">&nbsp; NULL
                                    </span>
                                </div>
                                @include('components.field-error', ['field' => 'publish_end'])
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SETTING --}}
                <hr class="m-0">
                <div class="card-body">
                    <h6 class="font-weight-semibold mb-4">SETTING</h6>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.public')</label>
                        <div class="col-sm-10">
                            <select class="form-control show-tick" name="public" data-style="btn-default">
                                @foreach (__('global.label.optional') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['post']) ? (old('public') == ''.$key.'' ? 'selected' : '') : (old('public', $data['post']['public']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- @role('super') --}}
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.locked')</label>
                        <div class="col-sm-10">
                            <select class="form-control show-tick" name="locked" data-style="btn-default">
                                @foreach (__('global.label.optional') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['post']) ? (old('locked') == ''.$key.'' ? 'selected' : '') : (old('locked', $data['post']['locked']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- @else
                    <input type="hidden" name="locked" value="{{ !isset($data['post']) ? 0 : $data['post']['locked'] }}">
                    @endrole --}}
                    @role('super')
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                        <label class="col-form-label text-sm-right">@lang('global.detail')</label>
                        </div>
                        <div class="col-md-10">
                            <label class="switcher switcher-success">
                                <input type="checkbox" class="switcher-input" name="is_detail" value="1" 
                                    {{ !isset($data['post']) ? (old('is_detail', 1) ? 'checked' : '') : (old('is_detail', $data['post']['config']['is_detail']) ? 'checked' : '') }}>
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
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.template')</label>
                        <div class="col-sm-10">
                            <select class="select2 show-tick" name="template_id" data-style="btn-default">
                                <option value=" " selected>DEFAULT</option>
                                @foreach ($data['templates'] as $template)
                                    <option value="{{ $template['id'] }}" {{ !isset($data['post']) ? (old('template_id') == $template['id'] ? 'selected' : '') : (old('template_id', $data['post']['template_id']) == $template['id'] ? 'selected' : '') }}>
                                        {{ $template['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @else
                    <input type="hidden" name="is_detail" value="{{ !isset($data['post']) ? 1 : $data['post']['config']['is_detail'] }}">
                    <input type="hidden" name="template_id" value="{{ !isset($data['post']) ? null : $data['post']['template_id'] }}">
                    @endrole
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.cover')</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control" id="image1" aria-label="Image" aria-describedby="button-image" name="cover_file"
                                        value="{{ !isset($data['post']) ? old('cover_file') : old('cover_file', $data['post']['cover']['filepath']) }}">
                                <div class="input-group-append" title="browse file">
                                    <button class="btn btn-primary file-name" id="button-image" type="button"><i class="las la-image"></i>&nbsp; @lang('global.browse')</button>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="@lang('global.title')" name="cover_title" value="{{ !isset($data['post']) ? old('cover_title') : old('cover_title', $data['post']['cover']['title']) }}">
                                </div>
                                <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="@lang('global.alt')" name="cover_alt" value="{{ !isset($data['post']) ? old('cover_alt') : old('cover_alt', $data['post']['cover']['alt']) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.banner')</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control" id="image2" aria-label="Image2" aria-describedby="button-image2" name="banner_file"
                                        value="{{ !isset($data['post']) ? old('banner_file') : old('banner_file', $data['post']['banner']['filepath']) }}">
                                <div class="input-group-append" title="browse file">
                                    <button class="btn btn-primary file-name" id="button-image2" type="button"><i class="las la-image"></i>&nbsp; @lang('global.browse')</button>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="@lang('global.title')" name="banner_title" value="{{ !isset($data['post']) ? old('banner_title') : old('banner_title', $data['post']['banner']['title']) }}">
                                </div>
                                <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="@lang('global.alt')" name="banner_alt" value="{{ !isset($data['post']) ? old('banner_alt') : old('banner_alt', $data['post']['banner']['alt']) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.hide') Field</label>
                        <div class="col-sm-10">
                            <div>
                                <label class="form-check form-check-inline">
                                  <input class="form-check-input" type="checkbox" name="hide_intro" value="1" 
                                  {{ !isset($data['post']) ? (old('hide_intro') ? 'checked' : '') : (old('hide_intro', $data['post']['config']['hide_intro']) ? 'checked' : '') }}>
                                  <span class="form-check-label">
                                    @lang('module/page.label.field3')
                                  </span>
                                </label>
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="hide_tags" value="1" 
                                    {{ !isset($data['post']) ? (old('hide_tags') ? 'checked' : '') : (old('hide_tags', $data['post']['config']['hide_tags']) ? 'checked' : '') }}>
                                    <span class="form-check-label">
                                      @lang('master/tags.caption')
                                    </span>
                                  </label>
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="hide_cover" value="1" 
                                    {{ !isset($data['post']) ? (old('hide_cover') ? 'checked' : '') : (old('hide_cover', $data['post']['config']['hide_cover']) ? 'checked' : '') }}>
                                    <span class="form-check-label">
                                      @lang('global.cover')
                                    </span>
                                </label>
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="hide_banner" value="1" 
                                    {{ !isset($data['post']) ? (old('hide_banner') ? 'checked' : '') : (old('hide_banner', $data['post']['config']['hide_banner']) ? 'checked' : '') }}>
                                    <span class="form-check-label">
                                      @lang('global.banner')
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                @if (!isset($data['post']) && Auth::user()->hasRole('super') || isset($data['post']))
                {{-- CUSTOM FIELD --}}
                <hr class="m-0">
                <div class="table-responsive text-center">
                    <table class="table card-table table-bordered">
                        @role('super')
                        <thead class="text-center">
                            <tr>
                                <td colspan="3" class="text-center">
                                    <button id="add_field" type="button" class="btn btn-success icon-btn-only-sm btn-sm">
                                        <i class="las la-plus"></i> Field
                                    </button>
                                </td>
                            </tr>
                        </thead>
                        @endrole
                        <tbody id="list_field">
                            @if (isset($data['post']) && !empty($data['post']['custom_fields']))
                                @foreach ($data['post']['custom_fields'] as $key => $val)
                                <tr class="num-list" id="delete-{{ $key }}">
                                    <td>
                                        <input type="text" class="form-control" name="cf_name[]" placeholder="name" 
                                            value="{{ $key }}" {{ !Auth::user()->hasRole('super') ? 'readonly' : '' }}>
                                    </td>
                                    <td>
                                        <textarea class="form-control" name="cf_value[]" placeholder="value">{{ $val }}</textarea>
                                    </td>
                                    @role('super')
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

@if (!Auth::user()->hasRole('super'))
<script>
    //hide form yang tidak diperlukan
    $('.hd').hide();
</script>
@endif

@include('includes.button-fm')
@include('includes.tinymce-fm')
@endsection