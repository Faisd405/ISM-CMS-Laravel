@extends('layouts.backend.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-8 col-md-8">

        <form action="{{ !isset($data['language']) ? route('language.store', $queryParam) : 
            route('language.update', array_merge(['id' => $data['language']['id']], $queryParam)) }}" method="POST">
            @csrf
            @isset ($data['language'])
                @method('PUT')
                <input type="hidden" name="old_iso" value="{{ $data['language']['iso_codes'] }}">
            @endisset

            <div class="card">
                <h5 class="card-header my-2">
                    @lang('global.form_attr', [
                        'attribute' => __('feature/language.caption')
                    ])
                </h5>
                <hr class="border-light m-0">
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/language.label.iso_code') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('iso_codes') is-invalid @enderror" name="iso_codes" 
                            value="{{ !isset($data['language']) ? old('iso_codes') : old('iso_codes', $data['language']['iso_codes']) }}" 
                            placeholder="@lang('feature/language.placeholder.iso_code')" 
                            {{ isset($data['language']) ? ($data['language']['locked'] == 1 ? 'readonly' : '') : '' }} autofocus>
                            @include('components.field-error', ['field' => 'iso_codes'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">Fallback Locale</label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('fallback_locale') is-invalid @enderror" name="fallback_locale" 
                            value="{{ !isset($data['language']) ? old('fallback_locale') : old('fallback_locale', $data['language']['fallback_locale']) }}" 
                            placeholder="Fallback Locale">
                            @include('components.field-error', ['field' => 'fallback_locale'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">Faker Locale</label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('faker_locale') is-invalid @enderror" name="faker_locale" 
                            value="{{ !isset($data['language']) ? old('faker_locale') : old('faker_locale', $data['language']['faker_locale']) }}" 
                            placeholder="Faker Locale">
                            @include('components.field-error', ['field' => 'faker_locale'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/language.label.name') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" 
                            value="{{ !isset($data['language']) ? old('name') : old('name', $data['language']['name']) }}" 
                            placeholder="@lang('feature/language.placeholder.name')">
                            @include('components.field-error', ['field' => 'name'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/language.label.code')</label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('code') is-invalid @enderror" name="code" 
                            value="{{ !isset($data['language']) ? old('code') : old('code', $data['language']['code']) }}" 
                            placeholder="@lang('feature/language.placeholder.code')">
                            @include('components.field-error', ['field' => 'code'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/language.label.description')</label>
                        <div class="col-sm-10">
                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" placeholder="@lang('feature/language.placeholder.description')">{{ !isset($data['language']) ? old('description') : old('description', $data['language']['description']) }}</textarea>
                        @include('components.field-error', ['field' => 'description'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/language.label.time_zone')</label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('time_zone') is-invalid @enderror" name="time_zone" 
                            value="{{ !isset($data['language']) ? old('time_zone') : old('time_zone', $data['language']['time_zone']) }}" 
                            placeholder="@lang('feature/language.placeholder.time_zone')">
                            @include('components.field-error', ['field' => 'time_zone'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/language.label.gmt')</label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('gmt') is-invalid @enderror" name="gmt" 
                            value="{{ !isset($data['language']) ? old('gmt') : old('gmt', $data['language']['gmt']) }}" 
                            placeholder="@lang('feature/language.placeholder.gmt')">
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
                    <div class="form-group row hide-form">
                        <div class="col-md-2 text-md-right">
                        <label class="col-form-label text-sm-right">@lang('global.locked')</label>
                        </div>
                        <div class="col-md-10">
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="locked" value="1"
                                {{ !isset($data['language']) ? (old('locked') ? 'checked' : '') : (old('locked', $data['language']['locked']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                            <small class="form-text text-muted">@lang('global.locked_info')</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer justify-content-center">
                    <div class="box-btn">
                        <button class="btn btn-main w-icon" type="submit" name="action" value="back" title="{{ isset($data['language']) ? __('global.save_change') : __('global.save') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['language']) ? __('global.save_change') : __('global.save') }}</span>
                        </button>
                        <button class="btn btn-success w-icon" type="submit" name="action" value="exit" title="{{ isset($data['language']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['language']) ? __('global.save_change_exit') : __('global.save_exit') }}</span>
                        </button>
                        <button type="reset" class="btn btn-default w-icon" title="{{ __('global.reset') }}">
                            <i class="fi fi-rr-refresh"></i>
                            <span>{{ __('global.reset') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>
@endsection

@section('jsbody')
@if(!Auth::user()->hasRole('developer|super'))
<script>
    $('.hide-form').hide();
</script>
@endif
@endsection