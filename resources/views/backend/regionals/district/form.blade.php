@extends('layouts.backend.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-8 col-md-8">

        <div class="card">
            <h6 class="card-header">
                @lang('global.form_attr', [
                    'attribute' => __('module/regional.district.caption')
                ])
            </h6>
            <div class="card-header">
                <span class="text-muted">
                    {{ Str::upper(__('module/regional.province.caption')) }} : <b class="text-primary">{{ $data['city']['province']['name'] }}</b>
                    <i class="las la-angle-right"></i>
                    {{ Str::upper(__('module/regional.city.caption')) }} : <b class="text-primary">{{ $data['city']['name'] }}</b>
                </span>
            </div>
            <form action="{{ !isset($data['district']) ? route('district.store', array_merge(['provinceCode' => $data['city']['province_code'], 'cityCode' => $data['city']['code']], $queryParam)) : 
                route('district.update', array_merge(['provinceCode' => $data['city']['province_code'], 'cityCode' => $data['city']['code'], 'id' => $data['district']['id']], $queryParam)) }}" method="POST">
                @csrf
                @isset ($data['district'])
                    @method('PUT')
                @endisset
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/regional.district.label.field1') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="number" class="form-control @error('code') is-invalid @enderror" name="code" 
                            value="{{ !isset($data['district']) ? old('code') : old('code', $data['district']['code']) }}" 
                            placeholder="@lang('module/regional.district.placeholder.field1')" autofocus>
                            @include('components.field-error', ['field' => 'code'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/regional.district.label.field2') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" 
                            value="{{ !isset($data['district']) ? old('name') : old('name', $data['district']['name']) }}" 
                            placeholder="@lang('module/regional.district.placeholder.field2')">
                            @include('components.field-error', ['field' => 'name'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/regional.district.label.field3') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('latitude') is-invalid @enderror" name="latitude" 
                            value="{{ !isset($data['district']) ? old('latitude') : old('latitude', $data['district']['latitude']) }}" 
                            placeholder="@lang('module/regional.district.placeholder.field3')">
                            @include('components.field-error', ['field' => 'latitude'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/regional.district.label.field4') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('longitude') is-invalid @enderror" name="longitude" 
                            value="{{ !isset($data['district']) ? old('longitude') : old('longitude', $data['district']['longitude']) }}" 
                            placeholder="@lang('module/regional.district.placeholder.field4')">
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
                            <small class="form-text text-muted">@lang('global.locked_info')</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" name="action" value="back" title="{{ isset($data['district']) ? __('global.save_change') : __('global.save') }}">
                        <i class="las la-save"></i> {{ isset($data['district']) ? __('global.save_change') : __('global.save') }}
                    </button>&nbsp;&nbsp;
                    <button type="submit" class="btn btn-danger" name="action" value="exit" title="{{ isset($data['district']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                        <i class="las la-save"></i> {{ isset($data['district']) ? __('global.save_change_exit') : __('global.save_exit') }}
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

@section('jsbody')
@if(!Auth::user()->hasRole('developer|super'))
<script>
  $('.hide-form').hide();
</script>
@endif
@endsection