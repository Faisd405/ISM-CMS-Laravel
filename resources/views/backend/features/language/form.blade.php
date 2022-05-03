@extends('layouts.backend.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-8 col-md-8">

        <div class="card">
            <h6 class="card-header">
                @lang('global.form_attr', [
                    'attribute' => __('feature/language.caption')
                ])
            </h6>
            <form action="{{ !isset($data['language']) ? route('language.store') : route('language.update', ['id' => $data['language']['id']]) }}" method="POST">
                @csrf
                @isset ($data['language'])
                    @method('PUT')
                    <input type="hidden" name="old_iso" value="{{ $data['language']['iso_codes'] }}">
                @endisset
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/language.label.field1') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('iso_codes') is-invalid @enderror" name="iso_codes" 
                            value="{{ !isset($data['language']) ? old('iso_codes') : old('iso_codes', $data['language']['iso_codes']) }}" 
                            placeholder="@lang('feature/language.placeholder.field1')" 
                            {{ isset($data['language']) ? ($data['language']['locked'] == 1 ? 'readonly' : '') : '' }} autofocus>
                            @include('components.field-error', ['field' => 'iso_codes'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/language.label.field2') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" 
                            value="{{ !isset($data['language']) ? old('name') : old('name', $data['language']['name']) }}" 
                            placeholder="@lang('feature/language.placeholder.field2')">
                            @include('components.field-error', ['field' => 'name'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/language.label.field3')</label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('code') is-invalid @enderror" name="code" 
                            value="{{ !isset($data['language']) ? old('code') : old('code', $data['language']['code']) }}" 
                            placeholder="@lang('feature/language.placeholder.field3')">
                            @include('components.field-error', ['field' => 'code'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/language.label.field4')</label>
                        <div class="col-sm-10">
                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" placeholder="@lang('feature/language.placeholder.field4')">{{ !isset($data['language']) ? old('description') : old('description', $data['language']['description']) }}</textarea>
                        @include('components.field-error', ['field' => 'description'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/language.label.field5')</label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('time_zone') is-invalid @enderror" name="time_zone" 
                            value="{{ !isset($data['language']) ? old('time_zone') : old('time_zone', $data['language']['time_zone']) }}" 
                            placeholder="@lang('feature/language.placeholder.field5')">
                            @include('components.field-error', ['field' => 'time_zone'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/language.label.field6')</label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('gmt') is-invalid @enderror" name="gmt" 
                            value="{{ !isset($data['language']) ? old('gmt') : old('gmt', $data['language']['gmt']) }}" 
                            placeholder="@lang('feature/language.placeholder.field6')">
                            @include('components.field-error', ['field' => 'gmt'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                          <label class="col-form-label text-sm-right">@lang('global.status')</label>
                        </div>
                        <div class="col-md-10">
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="active" value="1"
                                {{ !isset($data['language']) ? (old('active') ? 'checked' : 'checked') : (old('active', $data['language']['active']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.active.1')</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" name="action" value="back" title="{{ isset($data['language']) ? __('global.save_change') : __('global.save') }}">
                        <i class="las la-save"></i> {{ isset($data['language']) ? __('global.save_change') : __('global.save') }}
                    </button>&nbsp;&nbsp;
                    <button type="submit" class="btn btn-danger" name="action" value="exit" title="{{ isset($data['language']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                        <i class="las la-save"></i> {{ isset($data['language']) ? __('global.save_change_exit') : __('global.save_exit') }}
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