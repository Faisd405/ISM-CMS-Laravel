@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/select2/select2.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-8 col-md-8">

        <div class="card">
            <h6 class="card-header">
                @lang('global.form_attr', [
                    'attribute' => __('module/url.caption')
                ])
            </h6>
            <form action="{{ !isset($data['url']) ? route('url.store', $queryParam) : 
                route('url.update', array_merge(['id' => $data['url']['id']], $queryParam)) }}" method="POST">
                @csrf
                @isset ($data['url'])
                    @method('PUT')
                @endisset

                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/url.label.slug') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control text-bolder @error('slug') is-invalid @enderror" name="slug" 
                                value="{{ !isset($data['url']) ? old('slug') : old('slug', $data['url']['slug']) }}" 
                                placeholder="@lang('module/url.placeholder.slug')" autofocus>
                            @include('components.field-error', ['field' => 'slug'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                        <label class="col-form-label text-sm-right">@lang('module/url.label.module')</label>
                        </div>
                        <div class="col-md-10">
                        <select id="module" class="select2 show-tick @error('module') is-invalid @enderror" name="module" data-style="btn-default">
                            <option value="" disabled selected>@lang('global.select')</option>
                            @foreach (config('cms.module.url.mod') as $val)
                            <option value="{{ $val }}" {{ !isset($data['url']) ? (old('module') == $val ? 'selected' : '') : (old('module', $data['url']['module']) == $val ? 'selected' : '') }}>{{ Str::replace('_', ' ', Str::upper($val)) }}</option>
                            @endforeach
                        </select>
                        @include('components.field-error', ['field' => 'module'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/url.label.url_id')</label>
                        <div class="col-sm-10">
                        <input type="number" class="form-control text-bolder @error('urlable_id') is-invalid @enderror" name="urlable_id" 
                            value="{{ !isset($data['url']) ? old('urlable_id') : old('urlable_id', $data['url']['urlable_id']) }}" 
                            placeholder="@lang('module/url.placeholder.url_id')">
                            @include('components.field-error', ['field' => 'urlable_id'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/url.label.url_type')</label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control text-bolder @error('urlable_type') is-invalid @enderror" name="urlable_type" 
                            value="{{ !isset($data['url']) ? old('urlable_type') : old('urlable_type', $data['url']['urlable_type']) }}" 
                            placeholder="@lang('module/url.placeholder.url_type')">
                            @include('components.field-error', ['field' => 'urlable_type'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                          <label class="col-form-label text-sm-right">@lang('global.locked')</label>
                        </div>
                        <div class="col-md-10">
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="locked" value="1"
                                {{ !isset($data['registration']) ? (old('locked') ? 'checked' : '') : (old('locked', $data['registration']['locked']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                            <small class="form-text text-muted">@lang('global.locked_info')</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer justify-content-center">
                    <div class="box-btn">
                        <button class="btn btn-main w-icon" type="submit" name="action" value="back" title="{{ isset($data['url']) ? __('global.save_change') : __('global.save') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['url']) ? __('global.save_change') : __('global.save') }}</span>
                        </button>
                        <button class="btn btn-success w-icon" type="submit" name="action" value="exit" title="{{ isset($data['url']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['url']) ? __('global.save_change_exit') : __('global.save_exit') }}</span>
                        </button>
                        <button type="reset" class="btn btn-default w-icon" title="{{ __('global.reset') }}">
                            <i class="fi fi-rr-refresh"></i>
                            <span>{{ __('global.reset') }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

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
@endsection