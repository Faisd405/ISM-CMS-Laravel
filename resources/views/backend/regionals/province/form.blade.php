@extends('layouts.backend.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-8 col-md-8">

        <div class="card">
            <h6 class="card-header">
                @lang('global.form_attr', [
                    'attribute' => __('module/regional.province.caption')
                ])
            </h6>
            <form action="{{ !isset($data['province']) ? route('province.store', $queryParam) : 
                route('province.update', array_merge(['id' => $data['province']['id']], $queryParam)) }}" method="POST">
                @csrf
                @isset ($data['province'])
                    @method('PUT')
                @endisset
                
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/regional.province.label.code') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="number" class="form-control text-bolder @error('code') is-invalid @enderror" name="code" 
                            value="{{ !isset($data['province']) ? old('code') : old('code', $data['province']['code']) }}" 
                            placeholder="@lang('module/regional.province.placeholder.code')" autofocus>
                            @include('components.field-error', ['field' => 'code'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/regional.province.label.name') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control text-bolder @error('name') is-invalid @enderror" name="name" 
                            value="{{ !isset($data['province']) ? old('name') : old('name', $data['province']['name']) }}" 
                            placeholder="@lang('module/regional.province.placeholder.name')">
                            @include('components.field-error', ['field' => 'name'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/regional.province.label.latitude') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control text-bolder @error('latitude') is-invalid @enderror" name="latitude" 
                            value="{{ !isset($data['province']) ? old('latitude') : old('latitude', $data['province']['latitude']) }}" 
                            placeholder="@lang('module/regional.province.placeholder.latitude')">
                            @include('components.field-error', ['field' => 'latitude'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/regional.province.label.longitude') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control text-bolder @error('longitude') is-invalid @enderror" name="longitude" 
                            value="{{ !isset($data['province']) ? old('longitude') : old('longitude', $data['province']['longitude']) }}" 
                            placeholder="@lang('module/regional.province.placeholder.longitude')">
                            @include('components.field-error', ['field' => 'longitude'])
                        </div>
                    </div>
                    <div class="form-group row hide-form">
                        <div class="col-md-2 text-md-right">
                          <label class="col-form-label text-sm-right">@lang('global.locked')</label>
                        </div>
                        <div class="col-md-10">
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="locked" value="1"
                                {{ !isset($data['province']) ? (old('locked') ? 'checked' : '') : (old('locked', $data['province']['locked']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                            <small class="form-text">@lang('global.locked_info')</small>
                        </div>
                    </div>
                </div>

                <div class="card-footer justify-content-center">
                    <div class="box-btn">
                        <button class="btn btn-main w-icon" type="submit" name="action" value="back" title="{{ isset($data['province']) ? __('global.save_change') : __('global.save') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['province']) ? __('global.save_change') : __('global.save') }}</span>
                        </button>
                        <button class="btn btn-success w-icon" type="submit" name="action" value="exit" title="{{ isset($data['province']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['province']) ? __('global.save_change_exit') : __('global.save_exit') }}</span>
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

@section('jsbody')
@if(!Auth::user()->hasRole('developer|super'))
<script>
    $('.hide-form').hide();
</script>
@endif
@endsection