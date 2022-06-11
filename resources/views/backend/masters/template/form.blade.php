@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/select2/select2.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-8 col-md-8">

        <div class="card">
            <h6 class="card-header">
                @lang('global.form_attr', [
                    'attribute' => __('master/template.caption')
                ])
            </h6>
            <form action="{{ !isset($data['template']) ? route('template.store', $queryParam) : 
                route('template.update', array_merge(['id' => $data['template']['id']], $queryParam)) }}" method="POST">
                @csrf
                @isset ($data['template'])
                    @method('PUT')
                @endisset
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('master/template.label.field1') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" 
                            value="{{ !isset($data['template']) ? old('name') : old('name', $data['template']['name']) }}" 
                            placeholder="@lang('master/template.placeholder.field1')" autofocus>
                            @include('components.field-error', ['field' => 'name'])
                        </div>
                    </div>
                    @if (!isset($data['template']))
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                        <label class="col-form-label text-sm-right">@lang('master/template.label.field2') <i class="text-danger">*</i></label>
                        </div>
                        <div class="col-md-10">
                        <select id="module" class="select2 show-tick @error('module') is-invalid @enderror" name="module" data-style="btn-default">
                            <option value="" disabled selected>@lang('global.select')</option>
                            @foreach (config('cms.module.master.template.mod') as $key => $val)
                            <option value="{{ $key }}">{{ Str::replace('_', ' ', Str::upper($key)) }}</option>
                            @endforeach
                        </select>
                        @error('module')
                        <label class="error jquery-validation-error small form-text invalid-feedback" style="display: inline-block; color:red;">{!! $message !!}</label>
                        @enderror
                        </div>
                    </div>
                    <div class="form-group row" id="template-type">
                        <div class="col-md-2 text-md-right">
                        <label class="col-form-label text-sm-right">@lang('global.type') <i class="text-danger">*</i></label>
                        </div>
                        <div class="col-md-10">
                            <select id="type" class="custom-select" name="type" data-style="btn-default">
    
                            </select>
                            @error('type')
                            <label class="error jquery-validation-error small form-text invalid-feedback" style="display: inline-block; color:red;">{!! $message !!}</label>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('master/template.label.field4') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('filename') is-invalid @enderror" name="filename" value="{{ old('filename') }}" 
                            placeholder="@lang('master/template.placeholder.field3')">
                        @include('components.field-error', ['field' => 'filename'])
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" name="action" value="back" title="{{ isset($data['template']) ? __('global.save_change') : __('global.save') }}">
                        <i class="las la-save"></i> {{ isset($data['template']) ? __('global.save_change') : __('global.save') }}
                    </button>&nbsp;&nbsp;
                    <button type="submit" class="btn btn-danger" name="action" value="exit" title="{{ isset($data['template']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                        <i class="las la-save"></i> {{ isset($data['template']) ? __('global.save_change_exit') : __('global.save_exit') }}
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

@section('scripts')
<script src="{{ asset('assets/backend/vendor/libs/select2/select2.js') }}"></script>
@endsection

@section('jsbody')
<script>
    $('#template-type').hide();
    $('#module').on('change', function() {
        
        $('#template-type').show();
        $(".type-val").remove();
        if (this.value == 'content_section' || this.value == 'gallery_category') {
            $("#type").append(`
                <option value="1" class="type-val">List View</option>
                <option value="2" class="type-val">Detail View</option>
            `);
        } else {
            $("#type").append(`
                <option value="0" class="type-val">Custom View</option>
            `);
        }

    });

    $(function () {
        $('.select2').select2();
    });
</script>
@endsection