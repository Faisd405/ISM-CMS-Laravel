@extends('layouts.backend.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-8 col-md-8">

        <form action="{{ !isset($data['district']) ? route('district.store', array_merge(['provinceCode' => $data['city']['province_code'], 'cityCode' => $data['city']['code']], $queryParam)) : 
            route('district.update', array_merge(['provinceCode' => $data['city']['province_code'], 'cityCode' => $data['city']['code'], 'id' => $data['district']['id']], $queryParam)) }}" method="POST">
            @csrf
            @isset ($data['district'])
                @method('PUT')
            @endisset

            <div class="card">
                <h5 class="card-header my-2">
                    @lang('global.form_attr', [
                        'attribute' => __('module/regional.district.caption')
                    ])
                </h5>
                <hr class="border-light m-0">
                <div class="card-header">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <span>{{ Str::upper(__('module/regional.province.caption')) }}</span>
                        </li>
                        <li class="breadcrumb-item">
                            <span>{{ $data['city']['province']['name'] }}</span>
                        </li>
                        <li class="breadcrumb-item active">
                            <b class="text-main">{{ $data['city']['name'] }}</b>
                        </li>
                    </ol>
                </div>
                <hr class="border-light m-0">
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/regional.district.label.code') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="number" class="form-control text-bolder @error('code') is-invalid @enderror" name="code" 
                            value="{{ !isset($data['district']) ? old('code') : old('code', $data['district']['code']) }}" 
                            placeholder="@lang('module/regional.district.placeholder.code')" autofocus>
                            @include('components.field-error', ['field' => 'code'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/regional.district.label.name') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control text-bolder @error('name') is-invalid @enderror" name="name" 
                            value="{{ !isset($data['district']) ? old('name') : old('name', $data['district']['name']) }}" 
                            placeholder="@lang('module/regional.district.placeholder.name')">
                            @include('components.field-error', ['field' => 'name'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/regional.district.label.latitude') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control text-bolder @error('latitude') is-invalid @enderror" name="latitude" 
                            value="{{ !isset($data['district']) ? old('latitude') : old('latitude', $data['district']['latitude']) }}" 
                            placeholder="@lang('module/regional.district.placeholder.latitude')">
                            @include('components.field-error', ['field' => 'latitude'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/regional.district.label.longitude') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control text-bolder @error('longitude') is-invalid @enderror" name="longitude" 
                            value="{{ !isset($data['district']) ? old('longitude') : old('longitude', $data['district']['longitude']) }}" 
                            placeholder="@lang('module/regional.district.placeholder.longitude')">
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
                                {{ !isset($data['district']) ? (old('locked') ? 'checked' : '') : (old('locked', $data['district']['locked']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                            <small class="form-text">@lang('global.locked_info')</small>
                        </div>
                    </div>
                </div>

                <div class="card-footer justify-content-center">
                    <div class="box-btn">
                        <button class="btn btn-main w-icon" type="submit" name="action" value="back" title="{{ isset($data['district']) ? __('global.save_change') : __('global.save') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['district']) ? __('global.save_change') : __('global.save') }}</span>
                        </button>
                        <button class="btn btn-success w-icon" type="submit" name="action" value="exit" title="{{ isset($data['district']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['district']) ? __('global.save_change_exit') : __('global.save_exit') }}</span>
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