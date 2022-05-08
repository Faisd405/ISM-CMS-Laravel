@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/pages/account.css') }}">
<script src="{{ asset('assets/backend/wysiwyg/tinymce.min.js') }}"></script>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-9 col-md-9">

        <div class="card">
            <h6 class="card-header">
                @lang('global.form_attr', [
                    'attribute' => __('module/link.media.caption')
                ])
            </h6>
            <form action="{{ !isset($data['media']) ? route('link.media.store', ['categoryId' => $data['category']['id']]) : 
                route('link.media.update', ['categoryId' => $data['category']['id'], 'id' => $data['media']['id']]) }}" method="POST" 
                    enctype="multipart/form-data">
                @csrf
                @isset($data['media'])
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
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/link.media.label.field1') <i class="text-danger">*</i></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control mb-1 @error('title_'.$lang['iso_codes']) is-invalid @enderror" lang="{{ $lang['iso_codes'] }}" 
                                        name="title_{{ $lang['iso_codes'] }}" 
                                        value="{{ !isset($data['media']) ? old('title_'.$lang['iso_codes']) : old('title_'.$lang['iso_codes'], $data['media']->fieldLang('title', $lang['iso_codes'])) }}" 
                                        placeholder="@lang('module/link.media.placeholder.field1')">
                                    @include('components.field-error', ['field' => 'title_'.$lang['iso_codes']])
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/link.media.label.field2')</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control tiny-mce" name="description_{{ $lang['iso_codes'] }}">{!! !isset($data['media']) ? old('description_'.$lang['iso_codes']) : old('description_'.$lang['iso_codes'], $data['media']->fieldLang('description', $lang['iso_codes'])) !!}</textarea>
                                </div>
                            </div>
        
                        </div>
                    </div>
                    @endforeach
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">@lang('module/link.media.label.field3') <i class="text-danger">*</i></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control @error('url') is-invalid @enderror" name="url"
                                    value="{{ !isset($data['media']) ? old('url') : old('url', $data['media']['url']) }}" placeholder="">
                                @include('components.field-error', ['field' => 'url'])
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">Is Embed</label>
                            </div>
                            <div class="col-md-10">
                                <label class="switcher switcher-success">
                                    <input type="checkbox" class="switcher-input" name="is_embed" value="1" 
                                        {{ !isset($data['media']) ? (old('is_embed', 0) ? 'checked' : '') : (old('is_embed', $data['media']['config']['is_embed']) ? 'checked' : '') }}>
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
                                    <option value="{{ $key }}" {{ !isset($data['media']) ? (old('publish') == ''.$key.'' ? 'selected' : '') : (old('publish', $data['media']['publish']) == ''.$key.'' ? 'selected' : '') }}>
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
                                    <option value="{{ $key }}" {{ !isset($data['media']) ? (old('public') == ''.$key.'' ? 'selected' : '') : (old('public', $data['media']['public']) == ''.$key.'' ? 'selected' : '') }}>
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
                                    <option value="{{ $key }}" {{ !isset($data['media']) ? (old('locked') == ''.$key.'' ? 'selected' : '') : (old('locked', $data['media']['locked']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.cover')</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control" id="image1" aria-label="Image" aria-describedby="button-image" name="cover_file"
                                        value="{{ !isset($data['media']) ? old('cover_file') : old('cover_file', $data['media']['cover']['filepath']) }}">
                                <div class="input-group-append" title="browse file">
                                    <button class="btn btn-primary file-name" id="button-image" type="button"><i class="las la-image"></i>&nbsp; @lang('global.browse')</button>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="@lang('global.title')" name="cover_title" value="{{ !isset($data['media']) ? old('cover_title') : old('cover_title', $data['media']['cover']['title']) }}">
                                </div>
                                <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="@lang('global.alt')" name="cover_alt" value="{{ !isset($data['media']) ? old('cover_alt') : old('cover_alt', $data['media']['cover']['alt']) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.banner')</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control" id="image2" aria-label="Image2" aria-describedby="button-image2" name="banner_file"
                                        value="{{ !isset($data['media']) ? old('banner_file') : old('banner_file', $data['media']['banner']['filepath']) }}">
                                <div class="input-group-append" title="browse file">
                                    <button class="btn btn-primary file-name" id="button-image2" type="button"><i class="las la-image"></i>&nbsp; @lang('global.browse')</button>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="@lang('global.title')" name="banner_title" value="{{ !isset($data['media']) ? old('banner_title') : old('banner_title', $data['media']['banner']['title']) }}">
                                </div>
                                <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="@lang('global.alt')" name="banner_alt" value="{{ !isset($data['media']) ? old('banner_alt') : old('banner_alt', $data['media']['banner']['alt']) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.hide') Field</label>
                        <div class="col-sm-10">
                            <div>
                                <label class="form-check form-check-inline">
                                  <input class="form-check-input" type="checkbox" name="hide_description" value="1" 
                                  {{ !isset($data['media']) ? (old('hide_description') ? 'checked' : '') : (old('hide_description', $data['media']['config']['hide_description']) ? 'checked' : '') }}>
                                  <span class="form-check-label">
                                    @lang('module/link.media.label.field2')
                                  </span>
                                </label>
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="hide_cover" value="1" 
                                    {{ !isset($data['media']) ? (old('hide_cover') ? 'checked' : '') : (old('hide_cover', $data['media']['config']['hide_cover']) ? 'checked' : '') }}>
                                    <span class="form-check-label">
                                      @lang('global.cover')
                                    </span>
                                  </label>
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="hide_banner" value="1" 
                                    {{ !isset($data['media']) ? (old('hide_banner') ? 'checked' : '') : (old('hide_banner', $data['media']['config']['hide_banner']) ? 'checked' : '') }}>
                                    <span class="form-check-label">
                                        @lang('global.banner')
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" name="action" value="back" title="{{ isset($data['media']) ? __('global.save_change') : __('global.save') }}">
                        <i class="las la-save"></i> {{ isset($data['media']) ? __('global.save_change') : __('global.save') }}
                    </button>&nbsp;&nbsp;
                    <button type="submit" class="btn btn-danger" name="action" value="exit" title="{{ isset($data['media']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                        <i class="las la-save"></i> {{ isset($data['media']) ? __('global.save_change_exit') : __('global.save_exit') }}
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
@include('includes.button-fm')
@include('includes.tinymce-fm')
@endsection