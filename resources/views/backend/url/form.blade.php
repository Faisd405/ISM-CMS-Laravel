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
            <form action="{{ !isset($data['url']) ? route('url.store') : route('url.update', ['id' => $data['url']['id']]) }}" method="POST">
                @csrf
                @isset ($data['url'])
                    @method('PUT')
                @endisset
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/url.label.field1') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" name="slug" 
                            value="{{ !isset($data['url']) ? old('slug') : old('slug', $data['url']['slug']) }}" 
                            placeholder="@lang('module/url.placeholder.field1')" autofocus>
                            @include('components.field-error', ['field' => 'slug'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                        <label class="col-form-label text-sm-right">@lang('module/url.label.field2')</label>
                        </div>
                        <div class="col-md-10">
                        <select id="module" class="select2 show-tick @error('module') is-invalid @enderror" name="module" data-style="btn-default">
                            <option value="" disabled selected>@lang('global.select')</option>
                            @foreach (config('cms.module.url.mod') as $val)
                            <option value="{{ $val }}" {{ !isset($data['url']) ? (old('module') == $val ? 'selected' : '') : (old('module', $data['url']['module']) == $val ? 'selected' : '') }}>{{ Str::replace('_', ' ', Str::upper($val)) }}</option>
                            @endforeach
                        </select>
                        @error('module')
                        <label class="error jquery-validation-error small form-text invalid-feedback" style="display: inline-block; color:red;">{!! $message !!}</label>
                        @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/url.label.field3')</label>
                        <div class="col-sm-10">
                        <input type="number" class="form-control @error('urlable_id') is-invalid @enderror" name="urlable_id" 
                            value="{{ !isset($data['url']) ? old('urlable_id') : old('urlable_id', $data['url']['urlable_id']) }}" 
                            placeholder="@lang('module/url.placeholder.field3')">
                            @include('components.field-error', ['field' => 'urlable_id'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/url.label.field4')</label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('urlable_type') is-invalid @enderror" name="urlable_type" 
                            value="{{ !isset($data['url']) ? old('urlable_type') : old('urlable_type', $data['url']['urlable_type']) }}" 
                            placeholder="@lang('module/url.placeholder.field4')">
                            @include('components.field-error', ['field' => 'urlable_type'])
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" name="action" value="back" title="{{ isset($data['url']) ? __('global.save_change') : __('global.save') }}">
                        <i class="las la-save"></i> {{ isset($data['url']) ? __('global.save_change') : __('global.save') }}
                    </button>&nbsp;&nbsp;
                    <button type="submit" class="btn btn-danger" name="action" value="exit" title="{{ isset($data['url']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                        <i class="las la-save"></i> {{ isset($data['url']) ? __('global.save_change_exit') : __('global.save_exit') }}
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