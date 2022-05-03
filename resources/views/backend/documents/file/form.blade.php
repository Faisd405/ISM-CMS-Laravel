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
                    'attribute' => __('module/document.file.caption')
                ])
            </h6>
            <form action="{{ !isset($data['file']) ? route('document.file.store', ['categoryId' => $data['category']['id']]) : 
                route('document.file.update', ['categoryId' => $data['category']['id'], 'id' => $data['file']['id']]) }}" method="POST" 
                    enctype="multipart/form-data">
                @csrf
                @isset($data['file'])
                    @method('PUT')
                    <input type="hidden" name="type" value="{{ $data['file']['type'] }}">
                @endisset

                {{-- FILE --}}
                <div class="card-body">
                    
                    @if (!isset($data['file']))
                    {{-- type --}}
                    <div class="form-group row" id="file-type-form">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.type')  <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <select id="file-type" class="form-control show-tick @error('type') is-invalid @enderror" name="type" data-style="btn-default">
                                <option value=" " selected disabled>@lang('global.select')</option>
                                @foreach (__('module/document.file.type') as $key => $value)
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
                    @endif
                    @if (!isset($data['file']) || isset($data['file']) && $data['file']['type'] == '0')
                    <div class="form-group row" id="file-upload">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('module/document.file.label.field3')</label>
                        </div>
                        <div class="col-md-10">
                            <label class="custom-file-label" for="file-0"></label>
                            @if (isset($data['file']))
                            <input type="hidden" name="old_file" value="{{ $data['file']['file'] }}">
                            @endif
                            <input class="form-control custom-file-input file @error('file_document') is-invalid @enderror" type="file" id="file-0" lang="en" name="file_document" placeholder="">
                            @include('components.field-error', ['field' => 'file_document'])
                            [<span class="text-muted">Type of file : <strong>{{ Str::upper(config('cms.files.document.mimes')) }}</strong></span>] - 
                            [<span class="text-muted">Max Size : <strong>{{ config('cms.files.document.size') }}</strong></span>]
                        </div>
                    </div>
                    @endif
                    @if (!isset($data['file']) || isset($data['file']) && $data['file']['type'] == '1')
                    <div class="form-group row" id="file-fileman">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/configuration.filemanager.caption')</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control @error('filemanager') is-invalid @enderror" id="image1" aria-label="Image" aria-describedby="button-image" name="filemanager"
                                        value="{{ isset($data['file']) ? old('filemanager', $data['file']['file']) : '' }}">
                                <div class="input-group-append" title="browse file">
                                    <button class="btn btn-primary file-name" id="button-image" type="button"><i class="las la-image"></i>&nbsp; @lang('global.browse')</button>
                                </div>
                            </div>
                            @include('components.field-error', ['field' => 'filemanager'])
                        </div>
                    </div>
                    @endif
                    @if (!isset($data['file']) || isset($data['file']) && $data['file']['type'] == '2')
                    <div class="form-group row" id="file-url">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/document.file.label.field4')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('file_url') is-invalid @enderror" name="file_url" placeholder=""
                                value="{{ isset($data['file']) ? old('file_url', $data['file']['file']) : '' }}">
                            @include('components.field-error', ['field' => 'file_url'])
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
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/document.file.label.field1') <i class="text-danger">*</i></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control mb-1 @error('title_'.$lang['iso_codes']) is-invalid @enderror" lang="{{ $lang['iso_codes'] }}" 
                                        name="title_{{ $lang['iso_codes'] }}" 
                                        value="{{ !isset($data['file']) ? old('title_'.$lang['iso_codes']) : old('title_'.$lang['iso_codes'], $data['file']->fieldLang('title', $lang['iso_codes'])) }}" 
                                        placeholder="@lang('module/document.file.placeholder.field1')">
                                    @include('components.field-error', ['field' => 'title_'.$lang['iso_codes']])
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/document.file.label.field2')</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control tiny-mce" name="description_{{ $lang['iso_codes'] }}">{!! !isset($data['file']) ? old('description_'.$lang['iso_codes']) : old('description_'.$lang['iso_codes'], $data['file']->fieldLang('description', $lang['iso_codes'])) !!}</textarea>
                                </div>
                            </div>
        
                        </div>
                    </div>
                    @endforeach
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
                                    <option value="{{ $key }}" {{ !isset($data['file']) ? (old('publish') == ''.$key.'' ? 'selected' : '') : (old('publish', $data['file']['publish']) == ''.$key.'' ? 'selected' : '') }}>
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
                                    <option value="{{ $key }}" {{ !isset($data['file']) ? (old('public') == ''.$key.'' ? 'selected' : '') : (old('public', $data['file']['public']) == ''.$key.'' ? 'selected' : '') }}>
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
                                    <option value="{{ $key }}" {{ !isset($data['file']) ? (old('locked') == ''.$key.'' ? 'selected' : '') : (old('locked', $data['file']['locked']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- @else
                    <input type="hidden" name="locked" value="{{ !isset($data['file']) ? 0 : $data['file']['locked'] }}">
                    @endrole --}}
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.cover')</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control" id="image1" aria-label="Image" aria-describedby="button-image" name="cover_file"
                                        value="{{ !isset($data['file']) ? old('cover_file') : old('cover_file', $data['file']['cover']['filepath']) }}">
                                <div class="input-group-append" title="browse file">
                                    <button class="btn btn-primary file-name" id="button-image" type="button"><i class="las la-image"></i>&nbsp; @lang('global.browse')</button>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="@lang('global.title')" name="cover_title" value="{{ !isset($data['file']) ? old('cover_title') : old('cover_title', $data['file']['cover']['title']) }}">
                                </div>
                                <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="@lang('global.alt')" name="cover_alt" value="{{ !isset($data['file']) ? old('cover_alt') : old('cover_alt', $data['file']['cover']['alt']) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.hide') Field</label>
                        <div class="col-sm-10">
                            <div>
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="hide_title" value="1" 
                                    {{ !isset($data['file']) ? (old('hide_title') ? 'checked' : '') : (old('hide_title', $data['file']['config']['hide_title']) ? 'checked' : '') }}>
                                    <span class="form-check-label">
                                      @lang('module/document.file.label.field1')
                                    </span>
                                  </label>
                                <label class="form-check form-check-inline">
                                  <input class="form-check-input" type="checkbox" name="hide_description" value="1" 
                                  {{ !isset($data['file']) ? (old('hide_description') ? 'checked' : '') : (old('hide_description', $data['file']['config']['hide_description']) ? 'checked' : '') }}>
                                  <span class="form-check-label">
                                    @lang('module/document.file.label.field2')
                                  </span>
                                </label>
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="hide_cover" value="1" 
                                    {{ !isset($data['file']) ? (old('hide_cover') ? 'checked' : '') : (old('hide_cover', $data['file']['config']['hide_cover']) ? 'checked' : '') }}>
                                    <span class="form-check-label">
                                      @lang('global.cover')
                                    </span>
                                  </label>
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
            </form>
        </div>
    </div>
</div>
@endsection

@section('jsbody')
@if (!isset($data['file']))
<script>
    //type
    $('#file-upload').hide();
    $('#file-fileman').hide();
    $('#file-url').hide();

    $('#file-type').on('change', function() {

        if (this.value == '0') {
            $('#file-upload').show();
            $('#file-fileman').hide();
            $('#file-url').hide();
        }

        if (this.value == '1') {
            $('#file-upload').hide();
            $('#file-fileman').show();
            $('#file-url').hide();
        }

        if (this.value == '2') {
            $('#file-upload').hide();
            $('#file-fileman').hide();
            $('#file-url').show();
        }
    });
</script>    
@endif

@include('includes.button-fm')
@include('includes.tinymce-fm')
@endsection