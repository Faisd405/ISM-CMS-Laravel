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
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('master/template.label.name') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control text-bolder @error('name') is-invalid @enderror" name="name" 
                                value="{{ !isset($data['template']) ? old('name') : old('name', $data['template']['name']) }}" 
                                placeholder="@lang('master/template.placeholder.name')" autofocus>
                            @include('components.field-error', ['field' => 'name'])
                        </div>
                    </div>
                    @if (!isset($data['template']))
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('master/template.label.module') <i class="text-danger">*</i></label>
                        </div>
                        <div class="col-md-10">
                            <select id="module" class="select2 show-tick @error('module') is-invalid @enderror" name="module" data-style="btn-default">
                                <option value="" disabled selected>@lang('global.select')</option>
                                @foreach (config('cms.module.master.template.mod') as $key => $val)
                                <option value="{{ $key }}">{{ Str::replace('_', ' ', Str::upper($key)) }}</option>
                                @endforeach
                            </select>
                            @include('components.field-error', ['field' => 'module'])
                        </div>
                    </div>
                    <div class="form-group row" id="template-type">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('global.type') <i class="text-danger">*</i></label>
                        </div>
                        <div class="col-md-10">
                            <select id="type" class="form-control" name="type" data-style="btn-default">
    
                            </select>
                            @include('components.field-error', ['field' => 'type'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('master/template.label.filename') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('filename') is-invalid @enderror" name="filename" value="{{ old('filename') }}" 
                                placeholder="@lang('master/template.placeholder.filename')">
                            @include('components.field-error', ['field' => 'filename'])
                            <small class="form-text">@lang('global.lower_case')</small>
                        </div>
                    </div>
                    @endif
                    <div class="form-group row hide-form">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('global.locked')</label>
                        </div>
                        <div class="col-md-10">
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="locked" value="1"
                                {{ !isset($data['template']) ? (old('locked') ? 'checked' : '') : (old('locked', $data['template']['locked']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                            <small class="form-text">@lang('global.locked_info')</small>
                        </div>
                    </div>
                    <div class="form-group row" style="display: none;">
                        <label class="col-form-label col-sm-2 text-sm-right">Content Template</label>
                        <div class="col-sm-10">
                            <textarea class="my-code-area" rows="10" style="width: 100%" name="content_template">{!! !isset($data['template']) ? old('content_template') : old('content_template', $data['template']['content_template']) !!}</textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer justify-content-center">
                    <div class="box-btn">
                        <button class="btn btn-main w-icon" type="submit" name="action" value="back" title="{{ isset($data['template']) ? __('global.save_change') : __('global.save') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['template']) ? __('global.save_change') : __('global.save') }}</span>
                        </button>
                        <button class="btn btn-success w-icon" type="submit" name="action" value="exit" title="{{ isset($data['template']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['template']) ? __('global.save_change_exit') : __('global.save_exit') }}</span>
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

@section('scripts')
<script src="{{ asset('assets/backend/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('assets/backend/jquery-ace/ace/ace.js') }}"></script>
<script src="{{ asset('assets/backend/jquery-ace/ace/theme-monokai.js') }}"></script>
<script src="{{ asset('assets/backend/jquery-ace/ace/mode-html.js') }}"></script>
<script src="{{ asset('assets/backend/jquery-ace/jquery-ace.min.js') }}"></script>
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

    $('.my-code-area').ace({ theme: 'monokai', lang: 'html' });
</script>

@if(!Auth::user()->hasRole('developer|super'))
<script>
    $('.hide-form').hide();
</script>
@endif
@endsection