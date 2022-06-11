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
            <div class="card-header">
                <span class="text-muted">
                    {{ Str::upper(__('module/banner.category.caption')) }} : <b class="text-primary">{{ $data['category']->fieldLang('name') }}</b>
                </span>
            </div>
            <h6 class="card-header">
                @lang('global.form_attr', [
                    'attribute' => __('module/banner.caption')
                ])
            </h6>
            <form action="{{ !isset($data['banner']) ? route('banner.store', array_merge(['categoryId' => $data['category']['id']], $queryParam)) : 
                route('banner.update', array_merge(['categoryId' => $data['category']['id'], 'id' => $data['banner']['id']], $queryParam)) }}" method="POST" 
                    enctype="multipart/form-data">
                @csrf
                @isset($data['banner'])
                    @method('PUT')
                    <input type="hidden" name="type" value="{{ $data['banner']['type'] }}">
                    <input type="hidden" name="image_type" value="{{ $data['banner']['image_type'] }}">
                    <input type="hidden" name="video_type" value="{{ $data['banner']['video_type'] }}">
                @endisset

                {{-- FILE --}}
                <div class="card-body">
                    {{-- type --}}
                    @if (!isset($data['banner']))
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.type') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <select id="type" class="form-control show-tick @error('type') is-invalid @enderror" name="type" data-style="btn-default">
                                <option value=" " selected disabled>@lang('global.select')</option>
                                @foreach (__('module/banner.type') as $key => $value)
                                    <option value="{{ $key }}">
                                        {{ $value }}
                                    </option>
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
                            <input type="text" class="form-control" value="{{ __('module/banner.type.'.$data['banner']['type']) }}" readonly>
                        </div>
                    </div>
                    @endif
                    
                    @if (!isset($data['banner']))
                    {{-- image type --}}
                    <div class="form-group row" id="image-type-form">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/banner.label.image')  <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <select id="image-type" class="form-control show-tick @error('image_type') is-invalid @enderror" name="image_type" data-style="btn-default">
                                <option value=" " selected disabled>@lang('global.select')</option>
                                @foreach (__('module/banner.type_image') as $key => $value)
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
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/banner.label.video')  <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <select id="video-type" class="form-control show-tick @error('video_type') is-invalid @enderror" name="video_type" data-style="btn-default">
                                <option value=" " selected disabled>@lang('global.select')</option>
                                @foreach (__('module/banner.type_video') as $key => $value)
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
                    @elseif ($data['banner']['type'] == '0')
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/banner.label.image')  <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{ __('module/banner.type_image.'.$data['banner']['image_type']) }}" readonly>
                        </div>
                    </div>
                    @elseif ($data['banner']['type'] == '1')
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/banner.label.video')  <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{ __('module/banner.type_video.'.$data['banner']['video_type']) }}" readonly>
                        </div>
                    </div>
                    @endif
                    @if (!isset($data['banner']) || isset($data['banner']) && $data['banner']['image_type'] == '0')
                    <div class="form-group row" id="image-upload">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('module/banner.label.field3')</label>
                        </div>
                        <div class="col-md-10">
                            <label class="custom-file-label" for="file-0"></label>
                            @if (isset($data['banner']))
                            <input type="hidden" name="old_file" value="{{ $data['banner']['file'] }}">
                            @endif
                            <input class="form-control custom-file-input file @error('file_image') is-invalid @enderror" type="file" id="file-0" lang="en" name="file_image" placeholder="">
                            @include('components.field-error', ['field' => 'file_image'])
                            [<span class="text-muted">Type of file : <strong>{{ Str::upper(config('cms.files.banner.mimes')) }}</strong></span>] - 
                            [<span class="text-muted">Pixel : <strong>{{ config('cms.files.banner.pixel') }}</strong></span>] - 
                            [<span class="text-muted">Max Size : <strong>{{ config('cms.files.banner.size') }}</strong></span>]
                        </div>
                    </div>
                    @endif
                    @if (!isset($data['banner']) || isset($data['banner']) && $data['banner']['image_type'] == '1')
                    <div class="form-group row" id="image-fileman">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/configuration.filemanager.caption')</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control @error('filemanager') is-invalid @enderror" id="image1" aria-label="Image" aria-describedby="button-image" name="filemanager"
                                        value="{{ isset($data['banner']) ? old('filemanager', $data['banner']['file']) : '' }}">
                                <div class="input-group-append" title="browse file">
                                    <button class="btn btn-primary file-name" id="button-image" type="button"><i class="las la-image"></i>&nbsp; @lang('global.browse')</button>
                                </div>
                            </div>
                            @include('components.field-error', ['field' => 'filemanager'])
                        </div>
                    </div>
                    @endif
                    @if (!isset($data['banner']) || isset($data['banner']) && $data['banner']['image_type'] == '2')
                    <div class="form-group row" id="image-url">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/banner.label.field6')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('file_url') is-invalid @enderror" name="file_url" placeholder=""
                                value="{{ isset($data['banner']) ? old('file_url', $data['banner']['file']) : '' }}">
                            @include('components.field-error', ['field' => 'file_url'])
                        </div>
                    </div>
                    @endif

                    @if (!isset($data['banner']) || isset($data['banner']) && $data['banner']['video_type'] == '0')
                    <div id="video-upload">
                        @if (isset($data['banner']))
                        <input type="hidden" name="old_file" value="{{ $data['banner']['file'] }}">
                        <input type="hidden" name="old_thumbnail" value="{{ $data['banner']['thumbnail'] }}">
                        @endif
                        <div class="form-group row">
                            <div class="col-md-2 text-md-right">
                                <label class="col-form-label text-sm-right">@lang('module/banner.label.field3')</label>
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
                        <div class="form-group row">
                            <div class="col-md-2 text-md-right">
                                <label class="col-form-label text-sm-right">@lang('module/banner.label.field4')</label>
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
                    </div>
                    @endif
                    @if (!isset($data['banner']) || isset($data['banner']) && $data['banner']['video_type'] == '1')
                    <div class="form-group row" id="video-youtube">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/banner.label.field5')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('file_youtube') is-invalid @enderror" name="file_youtube" placeholder="" 
                                value="{{ isset($data['banner']) ? old('file_youtube', $data['banner']['file']) : '' }}">
                            <small class="text-muted">https://www.youtube.com/watch?v=<strong>hZK640cDj2s</strong></small>
                            @include('components.field-error', ['field' => 'file_youtube'])
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
        
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/banner.label.field1') <i class="text-danger">*</i></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control mb-1 @error('title_'.$lang['iso_codes']) is-invalid @enderror" lang="{{ $lang['iso_codes'] }}" 
                                        name="title_{{ $lang['iso_codes'] }}" 
                                        value="{{ !isset($data['banner']) ? old('title_'.$lang['iso_codes']) : old('title_'.$lang['iso_codes'], $data['banner']->fieldLang('title', $lang['iso_codes'])) }}" 
                                        placeholder="@lang('module/banner.placeholder.field1')">
                                    @include('components.field-error', ['field' => 'title_'.$lang['iso_codes']])
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/banner.label.field2')</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control tiny-mce" name="description_{{ $lang['iso_codes'] }}">{!! !isset($data['banner']) ? old('description_'.$lang['iso_codes']) : old('description_'.$lang['iso_codes'], $data['banner']->fieldLang('description', $lang['iso_codes'])) !!}</textarea>
                                </div>
                            </div>
        
                        </div>
                    </div>
                    @endforeach
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">@lang('module/banner.label.url')</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control @error('url') is-invalid @enderror" name="url"
                                    value="{{ !isset($data['banner']) ? old('url') : old('url', $data['banner']['url']) }}" placeholder="">
                                @include('components.field-error', ['field' => 'url'])
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SETTING --}}
                <hr class="m-0">
                <div class="card-body">
                    <h6 class="font-weight-semibold mb-4">SETTING</h6>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.status')</label>
                        <div class="col-sm-10">
                            <select class="form-control show-tick" name="publish" data-style="btn-default">
                                @foreach (__('global.label.publish') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['banner']) ? (old('publish') == ''.$key.'' ? 'selected' : '') : (old('publish', $data['banner']['publish']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.public')</label>
                        <div class="col-sm-10">
                            <select class="form-control show-tick" name="public" data-style="btn-default">
                                @foreach (__('global.label.optional') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['banner']) ? (old('public') == ''.$key.'' ? 'selected' : '') : (old('public', $data['banner']['public']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.locked')</label>
                        <div class="col-sm-10">
                            <select class="form-control show-tick" name="locked" data-style="btn-default">
                                @foreach (__('global.label.optional') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['banner']) ? (old('locked') == ''.$key.'' ? 'selected' : '') : (old('locked', $data['banner']['locked']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.hide') Field</label>
                        <div class="col-sm-10">
                            <div>
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="hide_title" value="1" 
                                    {{ !isset($data['banner']) ? (old('hide_title') ? 'checked' : '') : (old('hide_title', $data['banner']['config']['hide_title']) ? 'checked' : '') }}>
                                    <span class="form-check-label">
                                      @lang('module/banner.label.field1')
                                    </span>
                                  </label>
                                <label class="form-check form-check-inline">
                                  <input class="form-check-input" type="checkbox" name="hide_description" value="1" 
                                  {{ !isset($data['banner']) ? (old('hide_description') ? 'checked' : '') : (old('hide_description', $data['banner']['config']['hide_description']) ? 'checked' : '') }}>
                                  <span class="form-check-label">
                                    @lang('module/banner.label.field2')
                                  </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" name="action" value="back" title="{{ isset($data['banner']) ? __('global.save_change') : __('global.save') }}">
                        <i class="las la-save"></i> {{ isset($data['banner']) ? __('global.save_change') : __('global.save') }}
                    </button>&nbsp;&nbsp;
                    <button type="submit" class="btn btn-danger" name="action" value="exit" title="{{ isset($data['banner']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                        <i class="las la-save"></i> {{ isset($data['banner']) ? __('global.save_change_exit') : __('global.save_exit') }}
                    </button>&nbsp;&nbsp;
                    <button type="reset" class="btn btn-secondary" title="{{ __('global.reset') }}">
                    <i class="las la-redo-alt"></i> {{ __('global.reset') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('jsbody')
@if (!isset($data['banner']))
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

    $('#video-type').on('change', function() {

        if (this.value == '0') {
            $('#video-upload').show();
            $('#video-youtube').hide();
        }

        if (this.value == '1') {
            $('#video-upload').hide();
            $('#video-youtube').show();
        }
    });
</script>    
@endif

@include('includes.button-fm')
@include('includes.tinymce-fm')
@endsection