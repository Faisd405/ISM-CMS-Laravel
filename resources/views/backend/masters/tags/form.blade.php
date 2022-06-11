@extends('layouts.backend.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-8 col-md-8">

        <div class="card">
            <h6 class="card-header">
                @lang('global.form_attr', [
                    'attribute' => __('master/tags.caption')
                ])
            </h6>
            <form action="{{ !isset($data['tag']) ? route('tags.store', $queryParam) : 
                route('tags.update', array_merge(['id' => $data['tag']['id']], $queryParam)) }}" method="POST">
                @csrf
                @isset ($data['tag'])
                    @method('PUT')
                @endisset
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('master/tags.label.field1') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" 
                            value="{{ !isset($data['tag']) ? old('name') : old('name', $data['tag']['name']) }}" 
                            placeholder="@lang('master/tags.placeholder.field1')" autofocus>
                            @include('components.field-error', ['field' => 'name'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('master/tags.label.field2')</label>
                        <div class="col-sm-10">
                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" placeholder="@lang('master/tags.placeholder.field4')">{{ !isset($data['tag']) ? old('description') : old('description', $data['tag']['description']) }}</textarea>
                        @include('components.field-error', ['field' => 'description'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                          <label class="col-form-label text-sm-right">@lang('master/tags.label.field3')</label>
                        </div>
                        <div class="col-md-10">
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="flags" value="1"
                                {{ !isset($data['tag']) ? (old('flags') ? 'checked' : 'checked') : (old('flags', $data['tag']['flags']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.flags.1')</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                          <label class="col-form-label text-sm-right">@lang('master/tags.label.field4')</label>
                        </div>
                        <div class="col-md-10">
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="standar" value="1"
                                {{ !isset($data['tag']) ? (old('standar') ? 'checked' : '') : (old('standar', $data['tag']['standar']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" name="action" value="back" title="{{ isset($data['tag']) ? __('global.save_change') : __('global.save') }}">
                        <i class="las la-save"></i> {{ isset($data['tag']) ? __('global.save_change') : __('global.save') }}
                    </button>&nbsp;&nbsp;
                    <button type="submit" class="btn btn-danger" name="action" value="exit" title="{{ isset($data['tag']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                        <i class="las la-save"></i> {{ isset($data['tag']) ? __('global.save_change_exit') : __('global.save_exit') }}
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