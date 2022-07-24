@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/pages/account.css') }}">
<script src="{{ asset('assets/backend/wysiwyg/tinymce.min.js') }}"></script>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-9 col-md-9">

        @include('components.alert-error')
        <div class="card">
            <h6 class="card-header">
                @lang('global.form_attr', [
                    'attribute' => __('module/banner.file.caption')
                ])
            </h6>
            <div class="card-header">
                <span class="text-muted">
                    {{ Str::upper(__('module/banner.file.caption')) }} : <b class="text-primary">{{ $data['banner']->fieldLang('name') }}</b>
                </span>
            </div>
            <form action="{{ !isset($data['file']) ? route('banner.file.store', array_merge(['bannerId' => $data['banner']['id']], $queryParam)) : 
                route('banner.file.update', array_merge(['bannerId' => $data['banner']['id'], 'id' => $data['file']['id']], $queryParam)) }}" method="POST" 
                    enctype="multipart/form-data">
                @csrf
                @isset($data['file'])
                    @method('PUT')
                    <input type="hidden" name="type" value="{{ $data['file']['type'] }}">
                    <input type="hidden" name="image_type" value="{{ $data['file']['image_type'] }}">
                    <input type="hidden" name="video_type" value="{{ $data['file']['video_type'] }}">
                @endisset

                {{-- FILE --}}
                <div class="card-body">
                    {{-- type --}}
                    @if (!isset($data['file']))
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.type') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <select id="type" class="form-control show-tick @error('type') is-invalid @enderror" name="type" data-style="btn-default">
                                <option value=" " selected disabled>@lang('global.select')</option>
                                @foreach (config('cms.module.banner.file.type') as $key => $value)
                                    @if ($key == 0 && $data['banner']['config']['type_image'] == true)
                                    <option value="{{ $key }}">
                                        {{ $value }}
                                    </option>
                                    @endif
                                    @if ($key == 1 && $data['banner']['config']['type_video'] == true)
                                    <option value="{{ $key }}">
                                        {{ $value }}
                                    </option>
                                    @endif
                                    @if ($key == 2 && $data['banner']['config']['type_text'] == true)
                                    <option value="{{ $key }}">
                                        {{ $value }}
                                    </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('type')
                            <label class="error jquery-validation-error small form-text invalid-feedback" style="display: inline-block; color:red;">{!! $message !!}</label>
                            @enderror
                        </div>
                    </div>
                    @else
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.type') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{ config('cms.module.banner.file.type.'.$data['file']['type']) }}" readonly>
                        </div>
                    </div>
                    @endif
                    
                    @if (!isset($data['file']))
                    {{-- image type --}}
                    <div class="form-group row" id="image-type-form">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/banner.file.label.image')  <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <select id="image-type" class="form-control show-tick @error('image_type') is-invalid @enderror" name="image_type" data-style="btn-default">
                                <option value=" " selected disabled>@lang('global.select')</option>
                                @foreach (config('cms.module.banner.file.type_image') as $key => $value)
                                    <option value="{{ $key }}">
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('image_type')
                            <label class="error jquery-validation-error small form-text invalid-feedback" style="display: inline-block; color:red;">{!! $message !!}</label>
                            @enderror
                        </div>
                    </div>
                    {{-- video type --}}
                    <div class="form-group row" id="video-type-form">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/banner.file.label.video')  <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <select id="video-type" class="form-control show-tick @error('video_type') is-invalid @enderror" name="video_type" data-style="btn-default">
                                <option value=" " selected disabled>@lang('global.select')</option>
                                @foreach (config('cms.module.banner.file.type_video') as $key => $value)
                                    <option value="{{ $key }}">
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('video_type')
                            <label class="error jquery-validation-error small form-text invalid-feedback" style="display: inline-block; color:red;">{!! $message !!}</label>
                            @enderror
                        </div>
                    </div>
                    @elseif ($data['file']['type'] == '0')
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/banner.file.label.image')  <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{ config('cms.module.banner.file.type_image.'.$data['file']['image_type']) }}" readonly>
                        </div>
                    </div>
                    @elseif ($data['file']['type'] == '1')
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/banner.file.label.video')  <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{ config('cms.module.banner.file.type_video.'.$data['file']['video_type']) }}" readonly>
                        </div>
                    </div>
                    @endif
                    @if (!isset($data['file']) || isset($data['file']) && $data['file']['image_type'] == '0')
                    <div class="form-group row" id="image-upload">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('module/banner.file.label.field3')</label>
                        </div>
                        <div class="col-md-10">
                            <label class="custom-file-label" for="file-0"></label>
                            @if (isset($data['file']))
                            <input type="hidden" name="old_file" value="{{ $data['file']['file'] }}">
                            @endif
                            <input class="form-control custom-file-input file @error('file_image') is-invalid @enderror" type="file" id="file-0" lang="en" name="file_image" placeholder="">
                            @include('components.field-error', ['field' => 'file_image'])
                            [<span class="text-muted">Type of file : <strong>{{ Str::upper(config('cms.files.banner.mimes')) }}</strong></span>] - 
                            [<span class="text-muted">Pixel : <strong>{{ config('cms.files.banner.pixel') }}</strong></span>] - 
                            [<span class="text-muted">Max Size : <strong>{{ config('cms.files.banner.size') }}</strong></span>]
                        </div>
                    </div>
                    @endif
                    @if (!isset($data['file']) || isset($data['file']) && $data['file']['image_type'] == '1')
                    <div class="form-group row" id="image-fileman">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/configuration.filemanager.caption')</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control @error('filemanager') is-invalid @enderror" id="image1" aria-label="Image" aria-describedby="button-image" name="filemanager"
                                        value="{{ isset($data['file']) ? old('filemanager', $data['file']['file']) : '' }}" placeholder="@lang('global.browse') file...">
                                <div class="input-group-append" title="browse file">
                                    <button class="btn btn-primary file-name" id="button-image" type="button"><i class="las la-image"></i>&nbsp; @lang('global.browse')</button>
                                </div>
                            </div>
                            @include('components.field-error', ['field' => 'filemanager'])
                        </div>
                    </div>
                    @endif
                    @if (!isset($data['file']) || isset($data['file']) && $data['file']['image_type'] == '2')
                    <div class="form-group row" id="image-url">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/banner.file.label.field6')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('file_url') is-invalid @enderror" name="file_url" placeholder=""
                                value="{{ isset($data['file']) ? old('file_url', $data['file']['file']) : '' }}">
                            @include('components.field-error', ['field' => 'file_url'])
                        </div>
                    </div>
                    @endif

                    @if (!isset($data['file']) || isset($data['file']) && $data['file']['video_type'] == '0')
                    <div id="video-upload">
                        @if (isset($data['file']))
                        <input type="hidden" name="old_file" value="{{ $data['file']['file'] }}">
                        @endif
                        <div class="form-group row">
                            <div class="col-md-2 text-md-right">
                                <label class="col-form-label text-sm-right">@lang('module/banner.file.label.field3')</label>
                            </div>
                            <div class="col-md-10">
                                <label class="custom-file-label" for="file-1"></label>
                                <input class="form-control custom-file-input file @error('file_video') is-invalid @enderror" type="file" id="file-1" lang="en" name="file_video" placeholder="">
                                @include('components.field-error', ['field' => 'file_video'])
                                [<span class="text-muted">Type of file : <strong>{{ Str::upper(config('cms.files.banner.mimes_video')) }}</strong></span>] - 
                                [<span class="text-muted">Pixel : <strong>{{ config('cms.files.banner.pixel') }}</strong></span>] - 
                                [<span class="text-muted">Max Size : <strong>{{ config('cms.files.banner.size') }}</strong></span>]
                            </div>
                        </div>
                    </div>
                    @endif
                    @if (!isset($data['file']) || isset($data['file']) && $data['file']['video_type'] == '1')
                    <div class="form-group row" id="video-youtube">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/banner.file.label.field5')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('file_youtube') is-invalid @enderror" name="file_youtube" placeholder="" 
                                value="{{ isset($data['file']) ? old('file_youtube', $data['file']['file']) : '' }}">
                            <small class="form-text text-muted">https://www.youtube.com/watch?v=<strong>hZK640cDj2s</strong></small>
                            @include('components.field-error', ['field' => 'file_youtube'])
                        </div>
                    </div>
                    @endif
                    @if (!isset($data['file']) || isset($data['file']) && $data['file']['type'] == '1')
                    @if (isset($data['file']))
                    <input type="hidden" name="old_thumbnail" value="{{ $data['file']['thumbnail'] }}">
                    @endif
                    <div class="form-group row" id="video-thumbnail">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('module/banner.file.label.field4')</label>
                        </div>
                        <div class="col-md-10">
                            <label class="custom-file-label" for="file-2"></label>
                            <input class="form-control custom-file-input file @error('thumbnail') is-invalid @enderror" type="file" id="file-2" lang="en" name="thumbnail" placeholder="">
                            @include('components.field-error', ['field' => 'thumbnail'])
                            [<span class="text-muted">Type of file : <strong>{{ Str::upper(config('cms.files.banner.thumbnail.mimes')) }}</strong></span>] - 
                            [<span class="text-muted">Pixel : <strong>{{ config('cms.files.banner.thumbnail.pixel') }}</strong></span>] - 
                            [<span class="text-muted">Max Size : <strong>{{ config('cms.files.banner.thumbnail.size') }}</strong></span>]
                        </div>
                    </div>
                    @endif
                </div>
                <hr class="m-0">

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
        
                            <div class="form-group row {{ isset($data['file']) && $data['file']['config']['show_title'] == false ? 'hide-form' : '' }}">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/banner.file.label.field1') <i class="text-danger">*</i></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control mb-1 @error('title_'.$lang['iso_codes']) is-invalid @enderror" lang="{{ $lang['iso_codes'] }}" 
                                        name="title_{{ $lang['iso_codes'] }}" 
                                        value="{{ !isset($data['file']) ? old('title_'.$lang['iso_codes']) : old('title_'.$lang['iso_codes'], $data['file']->fieldLang('title', $lang['iso_codes'])) }}" 
                                        placeholder="@lang('module/banner.file.placeholder.field1')">
                                    @include('components.field-error', ['field' => 'title_'.$lang['iso_codes']])
                                </div>
                            </div>
                            <div class="form-group row {{ isset($data['file']) && $data['file']['config']['show_description'] == false ? 'hide-form' : '' }}">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/banner.file.label.field2')</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control tiny-mce" name="description_{{ $lang['iso_codes'] }}">{!! !isset($data['file']) ? old('description_'.$lang['iso_codes']) : old('description_'.$lang['iso_codes'], $data['file']->fieldLang('description', $lang['iso_codes'])) !!}</textarea>
                                </div>
                            </div>
        
                        </div>
                    </div>
                    @endforeach
                    <div class="card-body {{ isset($data['file']) && $data['file']['config']['show_url'] == false ? 'hide-form' : '' }}">
                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">@lang('module/banner.file.label.url')</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control @error('url') is-invalid @enderror" name="url"
                                    value="{{ !isset($data['file']) ? old('url') : old('url', $data['file']['url']) }}" placeholder="Url...">
                                @include('components.field-error', ['field' => 'url'])
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" name="action" value="back" title="{{ isset($data['file']) ? __('global.save_change') : __('global.save') }}">
                        <i class="las la-save"></i> {{ isset($data['file']) ? __('global.save_change') : __('global.save') }}
                    </button>&nbsp;&nbsp;
                    <button type="submit" class="btn btn-danger" name="action" value="exit" title="{{ isset($data['file']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                        <i class="las la-save"></i> {{ isset($data['file']) ? __('global.save_change_exit') : __('global.save_exit') }}
                    </button>&nbsp;&nbsp;
                    <button type="reset" class="btn btn-secondary" title="{{ __('global.reset') }}">
                    <i class="las la-redo-alt"></i> {{ __('global.reset') }}
                    </button>
                </div>

                {{-- SETTING --}}
                <hr class="m-0">
                <div class="card-body">
                    <h6 class="font-weight-bold text-primary mb-4">SETTING</h6>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="form-label">@lang('global.status')</label>
                            <select class="form-control show-tick" name="publish" data-style="btn-default">
                                @foreach (__('global.label.publish') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['file']) ? (old('publish') == ''.$key.'' ? 'selected' : '') : (old('publish', $data['file']['publish']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">@lang('global.public')</label>
                            <select class="form-control show-tick" name="public" data-style="btn-default">
                                @foreach (__('global.label.optional') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['file']) ? (old('public') == ''.$key.'' ? 'selected' : '') : (old('public', $data['file']['public']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
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
                                {{ !isset($data['file']) ? (old('locked') ? 'checked' : '') : (old('locked', $data['file']['locked']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                            <small class="form-text text-muted">@lang('global.locked_info')</small>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="form-label">Show Title</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_title" value="1"
                                {{ !isset($data['file']) ? (old('config_show_title', 1) ? 'checked' : '') : (old('config_show_title', $data['file']['config']['show_title']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="form-label">Show Description</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_description" value="1"
                                {{ !isset($data['file']) ? (old('config_show_description', 1) ? 'checked' : '') : (old('config_show_description', $data['file']['config']['show_description']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show URL</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_url" value="1"
                                {{ !isset($data['file']) ? (old('config_show_url', 1) ? 'checked' : '') : (old('config_show_url', $data['file']['config']['show_url']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Custom Field</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_custom_field" value="1"
                                {{ !isset($data['file']) ? (old('config_show_custom_field') ? 'checked' : '') : (old('config_show_custom_field', $data['file']['config']['show_custom_field']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                    </div>
                </div>

                @if (Auth::user()->hasRole('developer|super') || isset($data['file']) && $data['file']['config']['show_custom_field'] == true && !empty($data['file']['custom_fields']))
                {{-- CUSTOM FIELD --}}
                <hr class="m-0">
                <div class="table-responsive text-center">
                    <table class="table card-table table-bordered">
                        <thead>
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
                            @if (isset($data['file']) && !empty($data['file']['custom_fields']))
                                @foreach ($data['file']['custom_fields'] as $key => $val)
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

@section('jsbody')
@if (!isset($data['file']))
<script>
    //type
    $('#image-type-form').hide();
    $('#video-type-form').hide();
    
    $('#type').on('change', function() {
        $('#image-type').prop('selectedIndex', ' ');
        $('#video-type').prop('selectedIndex', ' ');
        if (this.value == '0') {
            $('#image-type-form').show();
            $('#video-type-form').hide();

            $('#video-upload').hide();
            $('#video-youtube').hide();
            $('#video-thumbnail').hide();
        }

        if (this.value == '1') {
            $('#image-type-form').hide();
            $('#video-type-form').show();

            $('#image-upload').hide();
            $('#image-fileman').hide();
            $('#image-url').hide();
        }

        if (this.value == '2') {
            $('#image-type-form').hide();
            $('#video-type-form').hide();

            $('#video-upload').hide();
            $('#video-youtube').hide();
            $('#video-thumbnail').hide();

            $('#image-upload').hide();
            $('#image-fileman').hide();
            $('#image-url').hide();
        }
    });

    //image type
    $('#image-upload').hide();
    $('#image-fileman').hide();
    $('#image-url').hide();

    $('#image-type').on('change', function() {

        if (this.value == '0') {
            $('#image-upload').show();
            $('#image-fileman').hide();
            $('#image-url').hide();
        }

        if (this.value == '1') {
            $('#image-upload').hide();
            $('#image-fileman').show();
            $('#image-url').hide();
        }

        if (this.value == '2') {
            $('#image-upload').hide();
            $('#image-fileman').hide();
            $('#image-url').show();
        }
    });

    //video type
    $('#video-upload').hide();
    $('#video-youtube').hide();
    $('#video-thumbnail').hide();

    $('#video-type').on('change', function() {

        if (this.value == '0') {
            $('#video-upload').show();
            $('#video-youtube').hide();
            $('#video-thumbnail').show();
        }

        if (this.value == '1') {
            $('#video-upload').hide();
            $('#video-youtube').show();
            $('#video-thumbnail').show();
        }
    });
</script>    
@endif

<script>
    //custom field
    $(function()  {

        @if(isset($data['file']) && !empty($data['file']['custom_fields']))
            var no = {{ count($data['file']['custom_fields']) }};
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