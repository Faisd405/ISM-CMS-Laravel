@extends('layouts.backend.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-8 col-md-8">
        
        @isset($data['parent'])
        <div class="alert alert-dark alert-dismissible fade show">
            <i class="las la-thumbtack"></i> @lang('module/user.permission.label.field1') <strong>" {!! Str::replace('_', ' ', Str::upper($data['parent']['name'])) !!} "</strong>
        </div>
        @endisset

        <div class="card">
            <h6 class="card-header">
                @lang('global.form_attr', [
                    'attribute' => __('module/user.permission.caption')
                ])
            </h6>
            <form action="{{ !isset($data['permission']) ? route('permission.store', ['parent' => Request::get('parent')]) : 
                route('permission.update', ['id' => $data['permission']['id']]) }}" method="POST">
                @csrf
                @isset ($data['permission'])
                    @method('PUT')
                @endisset
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/user.permission.label.field2') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" 
                                value="{{ !isset($data['permission']) ? old('name') : old('name', $data['permission']['name']) }}" 
                                placeholder="@lang('module/user.permission.placeholder.field2')" autofocus>
                            <small class="text-muted">@lang('global.lower_case')</small>
                            @include('components.field-error', ['field' => 'name'])
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" name="action" value="back" title="{{ isset($data['permission']) ? __('global.save_change') : __('global.save') }}">
                        <i class="las la-save"></i> {{ isset($data['permission']) ? __('global.save_change') : __('global.save') }}
                    </button>&nbsp;&nbsp;
                    <button type="submit" class="btn btn-danger" name="action" value="exit" title="{{ isset($data['permission']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                        <i class="las la-save"></i> {{ isset($data['permission']) ? __('global.save_change_exit') : __('global.save_exit') }}
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