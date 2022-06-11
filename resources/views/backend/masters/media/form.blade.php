@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/pages/account.css') }}">
<script src="{{ asset('assets/backend/wysiwyg/tinymce.min.js') }}"></script>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-9 col-md-9">

        <div class="card">
            <div class="card-header">
                <span class="text-muted">
                    {{ Str::upper(__('master/media.caption')) }} : <b class="text-primary">{{ Str::upper(Str::replace('_', ' ', Request::segment(4))) }}</b>
                </span>
            </div>
            <h6 class="card-header">
                @lang('global.form_attr', [
                    'attribute' => __('master/media.caption')
                ])
            </h6>
            <form action="{{ !isset($data['media']) ? route('media.store', $data['params']) : 
                route('media.update', $data['param_id']) }}" method="POST">
                @csrf
                @isset($data['media'])
                    @method('PUT')
                @endisset

                {{-- MEDIA --}}
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('master/media.label.field1') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" id="is_youtube" name="is_youtube" value="1" {{ isset($data['media']) ? ($data['media']['is_youtube'] == 1 ? 'checked' : '') : '' }}>
                                <span class="custom-control-label ml-4">@lang('master/media.placeholder.field1')</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row" id="youtube_id">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('master/media.label.field3') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control mb-1 @error('youtube_id') is-invalid @enderror" name="youtube_id" 
                                value="{{ !isset($data['media']) ? old('youtube_id') : old('youtube_id', $data['media']['youtube_id']) }}" placeholder="@lang('master/media.label.field3')">
                            <small class="text-muted">https://www.youtube.com/watch?v=<strong>hZK640cDj2s</strong></small>
                            @include('components.field-error', ['field' => 'youtube_id'])
                        </div>
                    </div>
                    <div id="files">
                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">@lang('master/media.label.field2') <i class="text-danger">*</i></label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="text" class="form-control @error('filename') is-invalid @enderror" id="image1" aria-label="Image" aria-describedby="button-image" name="filename"
                                            value="{{!isset($data['media']) ? old('filename') : old('filename', $data['media']['filepath']['filename']) }}" placeholder="@lang('global.browse')">
                                    <div class="input-group-append" title="browse file">
                                        <button class="btn btn-primary file-name" id="button-image" type="button"><i class="las la-file"></i>&nbsp; @lang('global.browse')</button>
                                    </div>
                                    @include('components.field-error', ['field' => 'filename'])
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">@lang('master/media.label.field6')</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="image2" aria-label="Image2" aria-describedby="button-image2" name="thumbnail"
                                            value="{{!isset($data['media']) ? old('thumbnail') : old('thumbnail', $data['media']['filepath']['thumbnail']) }}" placeholder="@lang('global.browse')">
                                    <div class="input-group-append" title="browse thumbnail">
                                        <button class="btn btn-primary file-name" id="button-image2" type="button"><i class="las la-image"></i>&nbsp; @lang('global.browse')</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MAIN --}}
                <hr class="m-0">
                @if (config('cms.module.feature.language.multiple') == true)
                <div class="list-group list-group-flush account-settings-links flex-row">
                    @foreach ($data['languages'] as $lang)
                    <a class="list-group-item list-group-item-action {{ $lang['iso_codes'] == config('cms.module.feature.language.default') ? 'active' : '' }}" 
                        data-toggle="list" href="#{{ $lang['iso_codes'] }}">
                        {!! $lang['name'] !!}
                    </a>
                    @endforeach
                </div>
                @endif
                <div class="tab-content">
                    @foreach ($data['languages'] as $lang)
                    <div class="tab-pane fade {{ $lang['iso_codes'] == config('cms.module.feature.language.default') ? 'show active' : '' }}" id="{{ $lang['iso_codes'] }}">
                        <div class="card-body pb-2">
        
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('master/media.label.field4')</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control mb-1 gen_slug @error('title_'.$lang['iso_codes']) is-invalid @enderror" lang="{{ $lang['iso_codes'] }}" 
                                        name="title_{{ $lang['iso_codes'] }}" 
                                        value="{{ !isset($data['media']) ? old('title_'.$lang['iso_codes']) : old('title_'.$lang['iso_codes'], $data['media']->fieldLang('title', $lang['iso_codes'])) }}" 
                                        placeholder="@lang('master/media.placeholder.field4')">
                                    @include('components.field-error', ['field' => 'title_'.$lang['iso_codes']])
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('master/media.label.field5')</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control tiny-mce" name="description_{{ $lang['iso_codes'] }}">{!! !isset($data['media']) ? old('description_'.$lang['iso_codes']) : old('description_'.$lang['iso_codes'], $data['media']->fieldLang('description', $lang['iso_codes'])) !!}</textarea>
                                </div>
                            </div>
        
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" name="action" value="back" title="{{ isset($data['media']) ? __('global.save_change') : __('global.save') }}">
                        <i class="las la-save"></i> {{ isset($data['media']) ? __('global.save_change') : __('global.save') }}
                    </button>&nbsp;&nbsp;
                    <button type="submit" class="btn btn-danger" name="action" value="exit" title="{{ isset($data['media']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                        <i class="las la-save"></i> {{ isset($data['media']) ? __('global.save_change_exit') : __('global.save_exit') }}
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
<script src="{{ asset('assets/backend/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/select2/select2.js') }}"></script>
@endsection

@section('jsbody')
<script>
    //type media
    @if(isset($data['media']) && $data['media']->is_youtube == 1)
        $('#youtube_id').show();
        $('#files').hide();
    @else
        $('#youtube_id').hide();
        $('#files').show();
    @endif

    $('#is_youtube').change(function() {
        if(this.checked) {
            $('#youtube_id').show();
            $('#files').hide();
        } else {
            $('#youtube_id').hide();
            $('#files').show();
        }    
    });
</script>

@if (!Auth::user()->hasRole('super'))
<script>
    //hide form yang tidak diperlukan
    $('.hd').hide();
</script>
@endif

@include('includes.button-fm')
@include('includes.tinymce-fm')
@endsection