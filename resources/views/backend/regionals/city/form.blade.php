@extends('layouts.backend.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-8 col-md-8">

        <div class="card">
            <div class="card-header">
                <span class="text-muted">{{ Str::upper(__('module/regional.province.caption')) }} : <b class="text-primary">{{ $data['province']['name'] }}</b></span>
            </div>
            <h6 class="card-header">
                @lang('global.form_attr', [
                    'attribute' => __('module/regional.city.caption')
                ])
            </h6>
            <form action="{{ !isset($data['city']) ? route('city.store', array_merge(['provinceCode' => $data['province']['code']], $queryParam)) : 
                route('city.update', array_merge(['provinceCode' => $data['province']['code'], 'id' => $data['city']['id']], $queryParam)) }}" method="POST">
                @csrf
                @isset ($data['city'])
                    @method('PUT')
                @endisset
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/regional.city.label.field1') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="number" class="form-control @error('code') is-invalid @enderror" name="code" 
                            value="{{ !isset($data['city']) ? old('code') : old('code', $data['city']['code']) }}" 
                            placeholder="@lang('module/regional.city.placeholder.field1')" autofocus>
                            @include('components.field-error', ['field' => 'code'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/regional.city.label.field2') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" 
                            value="{{ !isset($data['city']) ? old('name') : old('name', $data['city']['name']) }}" 
                            placeholder="@lang('module/regional.city.placeholder.field2')">
                            @include('components.field-error', ['field' => 'name'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/regional.city.label.field3') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('latitude') is-invalid @enderror" name="latitude" 
                            value="{{ !isset($data['city']) ? old('latitude') : old('latitude', $data['city']['latitude']) }}" 
                            placeholder="@lang('module/regional.city.placeholder.field3')">
                            @include('components.field-error', ['field' => 'latitude'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/regional.city.label.field4') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('longitude') is-invalid @enderror" name="longitude" 
                            value="{{ !isset($data['city']) ? old('longitude') : old('longitude', $data['city']['longitude']) }}" 
                            placeholder="@lang('module/regional.city.placeholder.field4')">
                            @include('components.field-error', ['field' => 'longitude'])
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" name="action" value="back" title="{{ isset($data['city']) ? __('global.save_change') : __('global.save') }}">
                        <i class="las la-save"></i> {{ isset($data['city']) ? __('global.save_change') : __('global.save') }}
                    </button>&nbsp;&nbsp;
                    <button type="submit" class="btn btn-danger" name="action" value="exit" title="{{ isset($data['city']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                        <i class="las la-save"></i> {{ isset($data['city']) ? __('global.save_change_exit') : __('global.save_exit') }}
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