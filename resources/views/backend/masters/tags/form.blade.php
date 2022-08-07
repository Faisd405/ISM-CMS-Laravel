@extends('layouts.backend.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-8 col-md-8">

        <form action="{{ !isset($data['tag']) ? route('tags.store', $queryParam) : 
            route('tags.update', array_merge(['id' => $data['tag']['id']], $queryParam)) }}" method="POST">
            @csrf
            @isset ($data['tag'])
                @method('PUT')
            @endisset
            <div class="card">
                <h5 class="card-header my-2">
                    @lang('global.form_attr', [
                        'attribute' => __('master/tags.caption')
                    ])
                </h5>
                <hr class="border-light m-0">
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('master/tags.label.name') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control text-bolder @error('name') is-invalid @enderror" name="name" 
                            value="{{ !isset($data['tag']) ? old('name') : old('name', $data['tag']['name']) }}" 
                            placeholder="@lang('master/tags.placeholder.name')" autofocus>
                            @include('components.field-error', ['field' => 'name'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('master/tags.label.description')</label>
                        <div class="col-sm-10">
                        <textarea class="form-control text-bolder @error('description') is-invalid @enderror" name="description" placeholder="@lang('master/tags.placeholder.description')">{{ !isset($data['tag']) ? old('description') : old('description', $data['tag']['description']) }}</textarea>
                        @include('components.field-error', ['field' => 'description'])
                        </div>
                    </div>
                    <div class="form-group row hide-form">
                        <div class="col-md-2 text-md-right">
                        <label class="col-form-label text-sm-right">@lang('master/tags.label.flags')</label>
                        </div>
                        <div class="col-md-10">
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="flags" value="1"
                                {{ !isset($data['tag']) ? (old('flags') ? 'checked' : 'checked') : (old('flags', $data['tag']['flags']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.flags.1')</span>
                            </label>
                            <small class="form-text">@lang('master/tags.placeholder.flags')</small>
                        </div>
                    </div>
                    <div class="form-group row hide-form">
                        <div class="col-md-2 text-md-right">
                        <label class="col-form-label text-sm-right">@lang('master/tags.label.standar')</label>
                        </div>
                        <div class="col-md-10">
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="standar" value="1"
                                {{ !isset($data['tag']) ? (old('standar') ? 'checked' : '') : (old('standar', $data['tag']['standar']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                            <small class="form-text">@lang('master/tags.placeholder.standar')</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer justify-content-center">
                    <div class="box-btn">
                        <button class="btn btn-main w-icon" type="submit" name="action" value="back" title="{{ isset($data['tag']) ? __('global.save_change') : __('global.save') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['tag']) ? __('global.save_change') : __('global.save') }}</span>
                        </button>
                        <button class="btn btn-success w-icon" type="submit" name="action" value="exit" title="{{ isset($data['tag']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['tag']) ? __('global.save_change_exit') : __('global.save_exit') }}</span>
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
@if(!Auth::user()->hasRole('developer|super|support|admin'))
<script>
    $('.hide-form').hide();
</script>
@endif
@endsection