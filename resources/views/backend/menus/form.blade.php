@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/pages/account.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-9 col-md-9">

        @if (isset($data['parent']))
        <div class="alert alert-dark alert-dismissible fade show">
            <i class="las la-thumbtack"></i> Under <strong>" {!! $data['parent']->fieldLang('title') !!} "</strong>
        </div>
        @endif

        <div class="card">
            <h6 class="card-header">
                @lang('global.form_attr', [
                    'attribute' => __('module/menu.caption')
                ])
            </h6>
            <form action="{{ !isset($data['menu']) ? route('menu.store', ['categoryId' => $data['category']['id'], 'parent' => Request::get('parent')]) : 
                route('menu.update', ['categoryId' => $data['category']['id'], 'id' => $data['menu']['id']]) }}" method="POST">
                @csrf
                @isset($data['menu'])
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
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/menu.label.field1') <i class="text-danger">*</i></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control mb-1 @error('title_'.$lang['iso_codes']) is-invalid @enderror" lang="{{ $lang['iso_codes'] }}" 
                                        name="title_{{ $lang['iso_codes'] }}" 
                                        value="{{ !isset($data['menu']) ? old('title_'.$lang['iso_codes']) : old('title_'.$lang['iso_codes'], $data['menu']->fieldLang('title', $lang['iso_codes'])) }}" 
                                        placeholder="@lang('module/menu.placeholder.field1')">
                                    @include('components.field-error', ['field' => 'title_'.$lang['iso_codes']])
                                </div>
                            </div>
        
                        </div>
                    </div>
                    @endforeach
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">@lang('module/menu.label.field3') <i class="text-danger">*</i></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control mb-1 @error('url') is-invalid @enderror" name="url" 
                                    value="{{ !isset($data['menu']) ? old('url') : old('url', $data['menu']['config']['url']) }}" 
                                    placeholder="@lang('module/menu.placeholder.field3')">
                                @include('components.field-error', ['field' => 'url'])
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('module/menu.label.target_blank')</label>
                            </div>
                            <div class="col-md-10">
                                <label class="switcher switcher-success">
                                    <input type="checkbox" class="switcher-input" name="target_blank" value="1" 
                                        {{ !isset($data['menu']) ? (old('target_blank', 0) ? 'checked' : '') : (old('target_blank', $data['menu']['config']['target_blank']) ? 'checked' : '') }}>
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
                            <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('module/menu.label.field6')</label>
                            </div>
                            <div class="col-md-10">
                                <label class="switcher switcher-success">
                                    <input type="checkbox" class="switcher-input" name="edit_public_menu" value="1" 
                                        {{ !isset($data['menu']) ? (old('edit_public_menu', 0) ? 'checked' : '') : (old('edit_public_menu', $data['menu']['config']['edit_public_menu']) ? 'checked' : '') }}>
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
                            <label class="col-form-label col-sm-2 text-sm-right">@lang('module/menu.label.icon')</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control mb-1 @error('icon') is-invalid @enderror" name="icon" 
                                    value="{{ !isset($data['menu']) ? old('icon') : old('icon', $data['menu']['config']['icon']) }}" 
                                    placeholder="@lang('module/menu.label.icon')">
                                <small class="text-muted">
                                    Icon Refence :
                                    <ul>
                                        @foreach (config('cms.setting.icon_refernces') as $icon)
                                        <li><a href="{{ $icon }}" target="_blank">{{ $icon }} <i class="las la-external-link-alt"></i></a></li>
                                        @endforeach
                                    </ul>
                                </small>
                                @include('components.field-error', ['field' => 'icon'])
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" name="action" value="back" title="{{ isset($data['menu']) ? __('global.save_change') : __('global.save') }}">
                        <i class="las la-save"></i> {{ isset($data['menu']) ? __('global.save_change') : __('global.save') }}
                    </button>&nbsp;&nbsp;
                    <button type="submit" class="btn btn-danger" name="action" value="exit" title="{{ isset($data['menu']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                        <i class="las la-save"></i> {{ isset($data['menu']) ? __('global.save_change_exit') : __('global.save_exit') }}
                    </button>&nbsp;&nbsp;
                    <button type="reset" class="btn btn-secondary" title="{{ __('global.reset') }}">
                    <i class="las la-redo-alt"></i> {{ __('global.reset') }}
                    </button>
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
                                    <option value="{{ $key }}" {{ !isset($data['menu']) ? (old('publish') == ''.$key.'' ? 'selected' : '') : (old('publish', $data['menu']['publish']) == ''.$key.'' ? 'selected' : '') }}>
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
                                    <option value="{{ $key }}" {{ !isset($data['menu']) ? (old('public') == ''.$key.'' ? 'selected' : '') : (old('public', $data['menu']['public']) == ''.$key.'' ? 'selected' : '') }}>
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
                                    <option value="{{ $key }}" {{ !isset($data['menu']) ? (old('locked') == ''.$key.'' ? 'selected' : '') : (old('locked', $data['menu']['locked']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('jsbody')
@if (!Auth::user()->hasRole('super'))
<script>
    //hide form yang tidak diperlukan
    $('.hd').hide();
</script>
@endif
@endsection