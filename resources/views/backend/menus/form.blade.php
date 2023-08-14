@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/pages/account.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/select2/select2.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-9 col-md-9">

        <form action="{{ !isset($data['menu']) ? route('menu.store', array_merge(['categoryId' => $data['category']['id'], 'parent' => Request::get('parent')], $queryParam)) :
            route('menu.update', array_merge(['categoryId' => $data['category']['id'], 'id' => $data['menu']['id']], $queryParam)) }}" method="POST">
            @csrf
            @isset($data['menu'])
                @method('PUT')
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
                        'attribute' => __('module/menu.caption')
                    ])
                </h5>
                <hr class="border-light m-0">
                <div class="card-header m-0">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <span>{{ Str::upper(__('module/menu.category.caption')) }}</span>
                        </li>
                        <li class="breadcrumb-item active">
                            <b class="text-main">{{ $data['category']['name'] }}</b>
                        </li>
                    </ol>
                </div>
                <hr class="border-light m-0">
                @if (isset($data['parent']))
                <div class="card-header m-0">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <span>Under</span>
                        </li>
                        <li class="breadcrumb-item active">
                            <b class="text-main">{!! $data['parent']['module_data']['title'] !!}</b>
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
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/menu.label.title') <i class="text-danger">*</i></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control text-bolder @error('title_'.$lang['iso_codes']) is-invalid @enderror" lang="{{ $lang['iso_codes'] }}"
                                        name="title_{{ $lang['iso_codes'] }}"
                                        value="{{ !isset($data['menu']) ? old('title_'.$lang['iso_codes']) : old('title_'.$lang['iso_codes'], $data['menu']->fieldLang('title', $lang['iso_codes'])) }}"
                                        placeholder="@lang('module/menu.placeholder.title')">
                                    @include('components.field-error', ['field' => 'title_'.$lang['iso_codes']])
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <hr class="border-light m-0">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                        <label class="col-form-label text-sm-right">@lang('module/menu.label.external_link')</label>
                        </div>
                        <div class="col-md-10">
                            <label class="switcher switcher-success">
                                <input id="not_from_module" type="checkbox" class="switcher-input" name="not_from_module" value="1"
                                    {{ !isset($data['menu']) ? '' : ($data['menu']['config']['not_from_module'] == 1 ? 'checked' : '') }}>
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
                    <div id="internal-link">
                        <div class="form-group row">
                            <div class="col-md-2 text-md-right">
                                <label class="col-form-label text-sm-right">@lang('module/menu.label.module') <i class="text-danger">*</i></label>
                            </div>
                            <div class="col-md-10">
                                <select id="module" class="select2 show-tick @error('module') is-invalid @enderror" name="module" data-style="btn-default">
                                    <option value="" disabled selected>@lang('global.select')</option>
                                    @foreach (config('cms.module.menu.mod') as $key => $val)
                                    <option value="{{ $val }}">{{ Str::replace('_', ' ', Str::upper($val)) }}</option>
                                    @endforeach
                                </select>
                                @include('components.field-error', ['field' => 'module'])
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-2 text-md-right">
                                <label class="col-form-label text-sm-right">@lang('module/menu.label.module_content') <i class="text-danger">*</i></label>
                            </div>
                            <div class="col-md-10">
                                @if(isset($data['menu']) && $data['menu']['config']['not_from_module'] == 0)
                                <input type="hidden" name="menuable_id" value="{{ $data['menu']['menuable_id'] }}">
                                <input id="menuable" type="text" class="form-control mb-1" value="{!! $data['menu']['module_data']['title'] !!}" readonly>
                                @endif
                                <select id="menuable_id" class="select-autocomplete show-tick @error('menuable_id') is-invalid @enderror" name="menuable_id" data-style="btn-default">
                                    <option value="" disabled selected>@lang('global.select')</option>
                                </select>
                                @include('components.field-error', ['field' => 'menuable_id'])
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" id="external-link">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/menu.label.url') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control mb-1 @error('url') is-invalid @enderror" name="url"
                                value="{{ !isset($data['menu']) ? old('url') : old('url', $data['menu']['config']['url']) }}"
                                placeholder="">
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
                    <div class="form-group row hide-form">
                        <div class="col-md-2 text-md-right">
                        <label class="col-form-label text-sm-right">@lang('module/menu.label.edit_public_menu')</label>
                        </div>
                        <div class="col-md-10">
                            <label class="switcher switcher-success">
                                <input type="checkbox" class="switcher-input" name="edit_public_menu" value="1"
                                    {{ !isset($data['menu']) ? (old('edit_public_menu', 1) ? 'checked' : '') : (old('edit_public_menu', $data['menu']['config']['edit_public_menu']) ? 'checked' : '') }}>
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
                    <div class="form-group row hide-form">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/menu.label.icon')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control mb-1 @error('icon') is-invalid @enderror" name="icon"
                                value="{{ !isset($data['menu']) ? old('icon') : old('icon', $data['menu']['config']['icon']) }}"
                                placeholder="@lang('module/menu.label.icon')">
                            <small class="text-muted">
                                Icon Refence :
                                <ul>
                                    @foreach (config('cms.setting.icon_references') as $icon)
                                    <li>
                                        <a href="{{ $icon['url'] }}" target="_blank"><span>{{ $icon['label'] }}</span> <i class="fi fi-rr-link text-bold"></i></a>
                                    </li>
                                    @endforeach
                                </ul>
                            </small>
                            @include('components.field-error', ['field' => 'icon'])
                        </div>
                    </div>
                    <div class="form-group row hide-form">
                        <div class="col-md-2 text-md-right">
                          <label class="col-form-label text-sm-right">@lang('global.locked')</label>
                        </div>
                        <div class="col-md-10">
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="locked" value="1"
                                {{ !isset($data['menu']) ? (old('locked') ? 'checked' : '') : (old('locked', $data['menu']['locked']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                            <small class="form-text">@lang('global.locked_info')</small>
                        </div>
                    </div>
                    <div class="form-group row hide-form">
                        <div class="col-md-2 text-md-right">
                          <label class="col-form-label text-sm-right">Create Child</label>
                        </div>
                        <div class="col-md-10">
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="create_child" value="1"
                                {{ !isset($data['menu']) ? (old('create_child') ? 'checked' : '') : (old('create_child', $data['menu']['config']['create_child']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row hide-form">
                        <div class="col-md-2 text-md-right">
                          <label class="col-form-label text-sm-right">Event</label>
                        </div>
                        <div class="col-md-10">
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="event" value="1"
                                {{ !isset($data['menu']) ? (old('event') ? 'checked' : '') : (old('event', $data['menu']['config']['event']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-footer justify-content-center">
                    <div class="box-btn">
                        <button class="btn btn-main w-icon" type="submit" name="action" value="back" title="{{ isset($data['menu']) ? __('global.save_change') : __('global.save') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['menu']) ? __('global.save_change') : __('global.save') }}</span>
                        </button>
                        <button class="btn btn-success w-icon" type="submit" name="action" value="exit" title="{{ isset($data['menu']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['menu']) ? __('global.save_change_exit') : __('global.save_exit') }}</span>
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
                    SETTING
                </h6>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="form-label">@lang('global.status')</label>
                            <select class="form-control show-tick" name="publish" data-style="btn-default">
                                @foreach (__('global.label.publish') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['menu']) ? (old('publish') == ''.$key.'' ? 'selected' : '') : (old('publish', $data['menu']['publish']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">@lang('global.public')</label>
                            <select class="form-control show-tick" name="public" data-style="btn-default">
                                @foreach (__('global.label.optional') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['menu']) ? (old('public') == ''.$key.'' ? 'selected' : '') : (old('public', $data['menu']['public']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

        </form>

    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/backend/vendor/libs/select2/select2.js') }}"></script>
@endsection

@section('jsbody')
<script>
    $(function () {
        $('.select2').select2();
    });
</script>

<script>
    var notFromModule = '';
    var module = null;
    @isset ($data['menu'])
        var notFromModule = "{{ $data['menu']['config']['not_from_module'] }}";
        var module = "{{ $data['menu']['module'] }}";
    @endisset

    if (notFromModule == 1) {
        $("#internal-link").hide();
        $("#external-link").show();
    } else {
        $("#internal-link").show();
        $("#external-link").hide();
    }

    $("#not_from_module").change(function() {
        if (this.checked) {
            $("#internal-link").hide();
            $("#external-link").show();
        } else {
            $("#internal-link").show();
            $("#external-link").hide();
        }
    });

    if (module != null) {
        $("#module").val(module);
        $("#menuable").show();
    }

    $("#menuable_id").hide();
    $("#module").change(function() {
        $("#menuable_id").show();
        $("#menuable").hide();
        var val = $(this).val();

        $('.select-autocomplete').select2({
            minimumInputLength: 1,
            ajax: {
                url: '/api/module/'+val,
                dataType: 'json',
                type: "GET",
                quietMillis: 50,
                data: function (params) {
                    return {
                        q: params.term,
                    }
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.data, function (item) {
                            return {
                                text: item.title,
                                id: item.id
                            }
                        })
                    };
                }
            }
        });
    });
</script>

@if (!Auth::user()->hasRole('developer|super'))
<script>
    $('.hide-form').hide();
</script>
@endif
@endsection
