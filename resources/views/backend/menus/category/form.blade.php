@extends('layouts.backend.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-8 col-md-8">

        <div class="card">
            <h6 class="card-header">
                @lang('global.form_attr', [
                    'attribute' => __('module/menu.category.caption')
                ])
            </h6>
            <form action="{{ !isset($data['category']) ? route('menu.category.store') : route('menu.category.update', ['id' => $data['category']['id']]) }}" method="POST">
                @csrf
                @isset ($data['category'])
                    @method('PUT')
                @endisset
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/menu.category.label.field1') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" 
                            value="{{ !isset($data['category']) ? old('name') : old('name', $data['category']['name']) }}" 
                            placeholder="@lang('module/menu.category.placeholder.field1')">
                            @include('components.field-error', ['field' => 'name'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                          <label class="col-form-label text-sm-right">@lang('global.status')</label>
                        </div>
                        <div class="col-md-10">
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="active" value="1"
                                {{ !isset($data['category']) ? (old('active') ? 'checked' : 'checked') : (old('active', $data['category']['active']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.active.1')</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.locked')</label>
                        <div class="col-sm-10">
                            <select class="form-control show-tick" name="locked" data-style="btn-default">
                                @foreach (__('global.label.optional') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['category']) ? (old('locked') == ''.$key.'' ? 'selected' : '') : (old('locked', $data['category']['locked']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" name="action" value="back" title="{{ isset($data['category']) ? __('global.save_change') : __('global.save') }}">
                        <i class="las la-save"></i> {{ isset($data['category']) ? __('global.save_change') : __('global.save') }}
                    </button>&nbsp;&nbsp;
                    <button type="submit" class="btn btn-danger" name="action" value="exit" title="{{ isset($data['category']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                        <i class="las la-save"></i> {{ isset($data['category']) ? __('global.save_change_exit') : __('global.save_exit') }}
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