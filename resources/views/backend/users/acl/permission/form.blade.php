@extends('layouts.backend.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-8 col-md-8">

        <div class="card">
            <h6 class="card-header">
                @lang('global.form_attr', [
                    'attribute' => __('module/user.permission.caption')
                ])
            </h6>
            @isset($data['parent'])
            <hr class="border-light m-0">
            <div class="card-header">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <span>{{ Str::upper(__('module/user.permission.label.parent')) }}</span>
                    </li>
                    <li class="breadcrumb-item active">
                        <b class="text-main">{!! Str::replace('_', ' ', Str::upper($data['parent']['name'])) !!}</b>
                    </li>
                </ol>
            </div>
            <hr class="border-light m-0">
            @endisset
            <form action="{{ !isset($data['permission']) ? route('permission.store', $queryParam) : 
                route('permission.update', $data['permission']['id']) }}" method="POST">
                @csrf
                @isset ($data['permission'])
                    @method('PUT')
                @endisset

                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/user.permission.label.name') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control text-bolder @error('name') is-invalid @enderror" name="name" 
                                value="{{ !isset($data['permission']) ? old('name') : old('name', $data['permission']['name']) }}" 
                                placeholder="@lang('module/user.permission.placeholder.name')" autofocus>
                            @include('components.field-error', ['field' => 'name'])
                            <small class="form-text">@lang('global.lower_case')</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                          <label class="col-form-label text-sm-right">@lang('global.locked')</label>
                        </div>
                        <div class="col-md-10">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" id="locked" type="checkbox" name="locked" value="1"
                                    {{ !isset($data['permission']) ? (old('locked') ? 'checked' : '') : (old('locked', $data['permission']['locked']) == 1 ? 'checked' : '') }}>
                                <label class="custom-control-label" for="locked"></label>
                            </div>
                            <small class="form-text">@lang('global.locked_info')</small>
                        </div>
                    </div>
                </div>

                <div class="card-footer justify-content-center">
                    <div class="box-btn">
                        <button class="btn btn-main w-icon" type="submit" name="action" value="back" title="{{ isset($data['permission']) ? __('global.save_change') : __('global.save') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['permission']) ? __('global.save_change') : __('global.save') }}</span>
                        </button>
                        <button class="btn btn-success w-icon" type="submit" name="action" value="exit" title="{{ isset($data['permission']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['permission']) ? __('global.save_change_exit') : __('global.save_exit') }}</span>
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