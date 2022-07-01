@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/pages/account.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/bootstrap-tagsinput/bootstrap-tagsinput.css') }}">
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
                    'attribute' => __('module/event.caption')
                ])
            </h6>
            <form action="{{ !isset($data['event']) ? route('event.store', $queryParam) : 
                route('event.update', array_merge(['id' => $data['event']['id']], $queryParam)) }}" method="POST">
                @csrf
                @isset($data['event'])
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
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/event.label.field1') <i class="text-danger">*</i></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control mb-1 {{ !isset($data['event']) ? 'gen_slug' : '' }} @error('name_'.$lang['iso_codes']) is-invalid @enderror" lang="{{ $lang['iso_codes'] }}" 
                                        name="name_{{ $lang['iso_codes'] }}" 
                                        value="{{ !isset($data['event']) ? old('name_'.$lang['iso_codes']) : old('name_'.$lang['iso_codes'], $data['event']->fieldLang('name', $lang['iso_codes'])) }}" 
                                        placeholder="@lang('module/event.placeholder.field1')">
                                    @include('components.field-error', ['field' => 'name_'.$lang['iso_codes']])
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/event.label.field3') <i class="text-danger">*</i></label>
                                <div class="col-sm-10">
                                    <textarea class="form-control tiny-mce @error('description_'.$lang['iso_codes']) is-invalid @enderror" name="description_{{ $lang['iso_codes'] }}">{!! !isset($data['event']) ? old('description_'.$lang['iso_codes']) : old('description_'.$lang['iso_codes'], $data['event']->fieldLang('description', $lang['iso_codes'])) !!}</textarea>
                                    @include('components.field-error', ['field' => 'description_'.$lang['iso_codes']])
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/event.label.field4')</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control tiny-mce @error('form_description_'.$lang['iso_codes']) is-invalid @enderror" name="form_description_{{ $lang['iso_codes'] }}">{!! !isset($data['event']) ? old('form_description_'.$lang['iso_codes']) : old('form_description_'.$lang['iso_codes'], $data['event']->fieldLang('form_description', $lang['iso_codes'])) !!}</textarea>
                                    @include('components.field-error', ['field' => 'form_description_'.$lang['iso_codes']])
                                </div>
                            </div>
        
                        </div>
                    </div>
                    @endforeach
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">@lang('module/event.label.field2') <i class="text-danger">*</i></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control slug_spot @error('slug') is-invalid @enderror" lang="{{ App::getLocale() }}" name="slug"
                                    value="{{ !isset($data['event']) ? old('slug') : old('slug', $data['event']['slug']) }}" placeholder="{{ url('/') }}/event/SLUG">
                                @include('components.field-error', ['field' => 'slug'])
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" name="action" value="back" title="{{ isset($data['event']) ? __('global.save_change') : __('global.save') }}">
                        <i class="las la-save"></i> {{ isset($data['event']) ? __('global.save_change') : __('global.save') }}
                    </button>&nbsp;&nbsp;
                    <button type="submit" class="btn btn-danger" name="action" value="exit" title="{{ isset($data['event']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                        <i class="las la-save"></i> {{ isset($data['event']) ? __('global.save_change_exit') : __('global.save_exit') }}
                    </button>&nbsp;&nbsp;
                    <button type="reset" class="btn btn-secondary" title="{{ __('global.reset') }}">
                    <i class="las la-redo-alt"></i> {{ __('global.reset') }}
                    </button>
                </div>

                {{-- EVENT SETTING --}}
                <hr class="m-0">
                <div class="card-body">
                    <h6 class="font-weight-semibold mb-4">EVENT SETTING</h6>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.type')</label>
                        <div class="col-sm-10">
                            <select id="type" class="form-control show-tick" name="type" data-style="btn-default">
                                <option value=" " selected disabled>@lang('global.select')</option>
                                @foreach (__('module/event.type') as $key => $typ)type
                                    <option value="{{ $key }}" {{ !isset($data['event']) ? (old('type') == ''.$key.'' ? 'selected' : '') : (old('type', $data['event']['type']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $typ }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                            <label class="error jquery-validation-error small form-text invalid-feedback" style="display: inline-block;color:red;">{!! $message !!}</label>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/event.label.field5')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="register_code" value="{{ !isset($data['event']) ? old('register_code') : old('register_code', $data['event']['register_code']) }}" placeholder="">
                        </div>
                    </div>
                    <div class="form-group row" id="offline">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/event.label.field6')</label>
                        <div class="col-sm-10">
                            <textarea class="form-control mb-1" name="place" placeholder="@lang('module/event.label.field6')">{{ !isset($data['event']) ? old('place') : old('place', $data['event']['place'])  }}</textarea>
                        </div>
                    </div>
                    <div id="online">
                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">Meeting URL</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="meeting_url" value="{{ !isset($data['event']) ? old('meeting_url') : old('meeting_url', $data['event']['links']['meeting_url']) }}" placeholder="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">Meeting ID</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="meeting_id" value="{{ !isset($data['event']) ? old('meeting_id') : old('meeting_id', $data['event']['links']['meeting_id']) }}" placeholder="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">Meeting PASSCODE</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="meeting_passcode" value="{{ !isset($data['event']) ? old('meeting_passcode') : old('meeting_passcode', $data['event']['links']['meeting_passcode']) }}" placeholder="">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('module/event.label.field7')</label>
                        </div>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input id="start_date" type="text" class="datetime-picker form-control @error('start_date') is-invalid @enderror" name="start_date"
                                    value="{{ !isset($data['event']) ? old('start_date') : (!empty($data['event']['start_date']) ? 
                                    old('start_date', $data['event']['start_date']->format('Y-m-d H:i')) : old('start_date')) }}" 
                                    placeholder="Select date">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="las la-calendar"></i></span>
                                    <span class="input-group-text">
                                        <input type="checkbox" id="enable_start" value="1">&nbsp; NULL
                                    </span>
                                </div>
                                @include('components.field-error', ['field' => 'start_date'])
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('module/event.label.field8')</label>
                        </div>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input id="end_date" type="text" class="datetime-picker form-control @error('end_date') is-invalid @enderror" name="end_date"
                                    value="{{ !isset($data['event']) ? old('end_date') : (!empty($data['event']['end_date']) ? 
                                    old('end_date', $data['event']['end_date']->format('Y-m-d H:i')) : old('end_date')) }}" 
                                    placeholder="Select date">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="las la-calendar"></i></span>
                                    <span class="input-group-text">
                                        <input type="checkbox" id="enable_end" value="1">&nbsp; NULL
                                    </span>
                                </div>
                                @include('components.field-error', ['field' => 'end_date'])
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/event.label.field9')</label>
                        <div class="col-sm-10">
                            <input class="form-control tags-input mb-1" name="email" value="{{ isset($data['event']) && !empty($data['event']['email']) ? old('email', implode(",", $data['event']['email'])) : old('email') }}" placeholder="">
                            <small class="text-muted">@lang('global.separated_comma')</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                        <label class="col-form-label text-sm-right">@lang('module/event.label.lock_form')</label>
                        </div>
                        <div class="col-md-10">
                            <label class="switcher switcher-success">
                                <input type="checkbox" class="switcher-input" name="lock_form" value="1" 
                                    {{ !isset($data['event']) ? (old('lock_form', 1) ? 'checked' : '') : (old('lock_form', $data['event']['config']['lock_form']) ? 'checked' : '') }}>
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

                {{-- SEO --}}
                <hr class="m-0">
                <div class="card-body">
                    <h6 class="font-weight-semibold mb-4">SEO</h6>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.meta_title')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control mb-1" name="meta_title" value="{{ !isset($data['event']) ? old('meta_title') : old('meta_title', $data['event']['seo']['title']) }}" placeholder="@lang('global.meta_title')">

                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.meta_description')</label>
                        <div class="col-sm-10">
                            <textarea class="form-control mb-1" name="meta_description" placeholder="@lang('global.meta_description')">{{ !isset($data['event']) ? old('meta_description') : old('meta_description', $data['event']['seo']['description'])  }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.meta_keywords')</label>
                        <div class="col-sm-10">
                            <input class="form-control tags-input mb-1" name="meta_keywords" value="{{ !isset($data['event']) ? old('meta_keywords') : old('meta_keywords', $data['event']['seo']['keywords'])  }}" placeholder="">
                            <small class="text-muted">@lang('global.separated_comma')</small>
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
                                    <option value="{{ $key }}" {{ !isset($data['event']) ? (old('publish') == ''.$key.'' ? 'selected' : '') : (old('publish', $data['event']['publish']) == ''.$key.'' ? 'selected' : '') }}>
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
                                    <option value="{{ $key }}" {{ !isset($data['event']) ? (old('public') == ''.$key.'' ? 'selected' : '') : (old('public', $data['event']['public']) == ''.$key.'' ? 'selected' : '') }}>
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
                                    <option value="{{ $key }}" {{ !isset($data['event']) ? (old('locked') == ''.$key.'' ? 'selected' : '') : (old('locked', $data['event']['locked']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @role('super')
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                        <label class="col-form-label text-sm-right">@lang('global.detail')</label>
                        </div>
                        <div class="col-md-10">
                            <label class="switcher switcher-success">
                                <input type="checkbox" class="switcher-input" name="is_detail" value="1" 
                                    {{ !isset($data['event']) ? (old('is_detail', 1) ? 'checked' : '') : (old('is_detail', $data['event']['config']['is_detail']) ? 'checked' : '') }}>
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
                    @else
                    <input type="hidden" name="is_detail" value="{{ !isset($data['event']) ? 1 : $data['event']['config']['is_detail'] }}">
                    @endrole
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.cover')</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control" id="image1" aria-label="Image" aria-describedby="button-image" name="cover_file"
                                        value="{{ !isset($data['event']) ? old('cover_file') : old('cover_file', $data['event']['cover']['filepath']) }}">
                                <div class="input-group-append" title="browse file">
                                    <button class="btn btn-primary file-name" id="button-image" type="button"><i class="las la-image"></i>&nbsp; @lang('global.browse')</button>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="@lang('global.title')" name="cover_title" value="{{ !isset($data['event']) ? old('cover_title') : old('cover_title', $data['event']['cover']['title']) }}">
                                </div>
                                <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="@lang('global.alt')" name="cover_alt" value="{{ !isset($data['event']) ? old('cover_alt') : old('cover_alt', $data['event']['cover']['alt']) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.banner')</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control" id="image2" aria-label="Image2" aria-describedby="button-image2" name="banner_file"
                                        value="{{ !isset($data['event']) ? old('banner_file') : old('banner_file', $data['event']['banner']['filepath']) }}">
                                <div class="input-group-append" title="browse file">
                                    <button class="btn btn-primary file-name" id="button-image2" type="button"><i class="las la-image"></i>&nbsp; @lang('global.browse')</button>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="@lang('global.title')" name="banner_title" value="{{ !isset($data['event']) ? old('banner_title') : old('banner_title', $data['event']['banner']['title']) }}">
                                </div>
                                <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="@lang('global.alt')" name="banner_alt" value="{{ !isset($data['event']) ? old('banner_alt') : old('banner_alt', $data['event']['banner']['alt']) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    @role('super')
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">Content Template</label>
                        <div class="col-sm-10">
                            <textarea class="my-code-area" rows="10" style="width: 100%" name="content_template">{!! !isset($data['event']) ? old('content_template') : old('content_template', $data['event']['content_template']) !!}</textarea>
                        </div>
                    </div>
                    @else
                    <input type="hidden" name="content_template" value="{{ !isset($data['event']) ? old('content_template') : old('content_template', $data['event']['content_template']) }}">
                    @endrole
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.hide') Field</label>
                        <div class="col-sm-10">
                            <div>
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="hide_description" value="1" 
                                    {{ !isset($data['event']) ? (old('hide_description') ? 'checked' : '') : (old('hide_description', $data['event']['config']['hide_description']) ? 'checked' : '') }}>
                                    <span class="form-check-label">
                                      @lang('module/content.section.label.field3')
                                    </span>
                                </label>
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="hide_form" value="1" 
                                    {{ !isset($data['event']) ? (old('hide_form') ? 'checked' : '') : (old('hide_form', $data['event']['config']['hide_form']) ? 'checked' : '') }}>
                                    <span class="form-check-label">
                                      Form
                                    </span>
                                </label>
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="hide_cover" value="1" 
                                    {{ !isset($data['event']) ? (old('hide_cover') ? 'checked' : '') : (old('hide_cover', $data['event']['config']['hide_cover']) ? 'checked' : '') }}>
                                    <span class="form-check-label">
                                      @lang('global.cover')
                                    </span>
                                </label>
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="hide_banner" value="1" 
                                    {{ !isset($data['event']) ? (old('hide_banner') ? 'checked' : '') : (old('hide_banner', $data['event']['config']['hide_banner']) ? 'checked' : '') }}>
                                    <span class="form-check-label">
                                      @lang('global.banner')
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                @if (!isset($data['event']) && Auth::user()->hasRole('super') || isset($data['event']))
                {{-- CUSTOM FIELD --}}
                <hr class="m-0">
                <div class="table-responsive text-center">
                    <table class="table card-table table-bordered">
                        <thead>
                            @role('super')
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
                            @if (isset($data['event']) && !empty($data['event']['custom_fields']))
                                @foreach ($data['event']['custom_fields'] as $key => $val)
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
<script src="{{ asset('assets/backend/vendor/libs/bootstrap-material-datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
<script src="{{ asset('assets/backend/jquery-ace/ace/ace.js') }}"></script>
<script src="{{ asset('assets/backend/jquery-ace/ace/theme-monokai.js') }}"></script>
<script src="{{ asset('assets/backend/jquery-ace/ace/mode-html.js') }}"></script>
<script src="{{ asset('assets/backend/jquery-ace/jquery-ace.min.js') }}"></script>
@endsection

@section('jsbody')
<script>
    //select2
    $(function () {
        $('.select2').select2();
    });

    //tags
    $('.tags-input').tagsinput({ tagClass: 'badge badge-primary' });

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

        @if(isset($data['event']) && !empty($data['event']['custom_fields']))
            var no = {{ count($data['event']['custom_fields']) }};
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

    $('.my-code-area').ace({ theme: 'monokai', lang: 'html' });
</script>

@if (!Auth::user()->hasRole('super'))
<script>
    //hide form yang tidak diperlukan
    $('.hd').hide();
</script>
@endif

@if (!isset($data['event']))
<script>
    $('#offline, #online').hide();
    $('#type').change(function() {
        if ($(this).val() == '0') {
            $('#offline').show();
            $('#online').hide();
        } else {
            $('#offline').hide();
            $('#online').show();
        }
    });
</script>
@else
<script>
    var type = '{{ $data['event']['type'] }}';
    if (type == 0) {
        $('#offline').show();
        $('#online').hide();
    } else {
        $('#offline').hide();
        $('#online').show();
    }
    $('#type').change(function() {
        if ($(this).val() == '0') {
            $('#offline').show();
            $('#online').hide();
        } else {
            $('#offline').hide();
            $('#online').show();
        }
    });
</script>
@endif

@include('includes.button-fm')
@include('includes.tinymce-fm')
@endsection