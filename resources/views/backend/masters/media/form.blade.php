@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/pages/account.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-9 col-md-9">

        <form action="{{ !isset($data['media']) ? route('media.store', $data['params']) :
            route('media.update', $data['param_id']) }}" method="POST">
            @csrf
            @isset($data['media'])
                @method('PUT')
            @endisset

            <div class="card">
                <h5 class="card-header my-2">
                    @lang('global.form_attr', [
                        'attribute' => __('master/media.caption')
                    ])
                </h5>
                <hr class="border-light m-0">
                <div class="card-header">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <span>{{ Str::upper(Str::replace('_', ' ', Request::segment(4))) }}</span>
                        </li>
                        <li class="breadcrumb-item active">
                            <b class="text-main">{{ $data['module']['title'][App::getLocale()] }}</b>
                        </li>
                    </ol>
                </div>
                <hr class="border-light m-0">

                {{-- MEDIA --}}
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('master/media.label.from_youtube')</label>
                        <div class="col-sm-10">
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" id="is_youtube" name="is_youtube" value="1" {{ isset($data['media']) ? ($data['media']['is_youtube'] == 1 ? 'checked' : '') : '' }}>
                                <span class="custom-control-label ml-4">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row" id="youtube_id">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('master/media.label.youtube_id') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control text-bolder @error('youtube_id') is-invalid @enderror" name="youtube_id"
                                value="{{ !isset($data['media']) ? old('youtube_id') : old('youtube_id', $data['media']['youtube_id']) }}" placeholder="@lang('master/media.label.youtube_id')">
                            <small class="text-muted">https://www.youtube.com/watch?v=<strong>hZK640cDj2s</strong></small>
                            @include('components.field-error', ['field' => 'youtube_id'])
                        </div>
                    </div>
                    <div id="files">
                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">@lang('master/media.label.file') <i class="text-danger">*</i></label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="text" class="form-control @error('filename') is-invalid @enderror" id="image1" aria-label="Image" aria-describedby="button-image" name="filename"
                                            value="{{!isset($data['media']) ? old('filename') : old('filename', $data['media']['filepath']['filename']) }}" placeholder="@lang('global.browse') file...">
                                    <div class="input-group-append" title="browse file">
                                        <button class="btn btn-main w-icon file-name" id="button-image" type="button"><i class="fi fi-rr-folder"></i>&nbsp; @lang('global.browse')</button>
                                    </div>
                                    @include('components.field-error', ['field' => 'filename'])
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">@lang('master/media.label.thumbnail')</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="image2" aria-label="Image2" aria-describedby="button-image2" name="thumbnail"
                                            value="{{!isset($data['media']) ? old('thumbnail') : old('thumbnail', $data['media']['filepath']['thumbnail']) }}" placeholder="@lang('global.browse') file...">
                                    <div class="input-group-append" title="browse thumbnail">
                                        <button class="btn btn-main w-icon file-name" id="button-image2" type="button"><i class="fi fi-rr-picture"></i>&nbsp; @lang('global.browse')</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.locked')</label>
                        <div class="col-sm-10">
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="locked" value="1"
                                {{ !isset($data['media']) ? (old('locked') ? 'checked' : '') : (old('locked', $data['media']['locked']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                            <small class="form-text">@lang('global.locked_info')</small>
                        </div>
                    </div>
                </div>

                <div class="card-footer justify-content-center">
                    <div class="box-btn">
                        <button class="btn btn-main w-icon" type="submit" name="action" value="back" title="{{ isset($data['city']) ? __('global.save_change') : __('global.save') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['city']) ? __('global.save_change') : __('global.save') }}</span>
                        </button>
                        <button class="btn btn-success w-icon" type="submit" name="action" value="exit" title="{{ isset($data['city']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['city']) ? __('global.save_change_exit') : __('global.save_exit') }}</span>
                        </button>
                        <button type="reset" class="btn btn-default w-icon" title="{{ __('global.reset') }}">
                            <i class="fi fi-rr-refresh"></i>
                            <span>{{ __('global.reset') }}</span>
                        </button>
                    </div>
                </div>

            </div>

            @if (config('cms.module.feature.language.multiple') == true)
            <ul class="nav nav-tabs mb-4">
                @foreach ($data['languages'] as $lang)
                <li class="nav-item">
                    <a class="nav-link{{ $lang['iso_codes'] == config('app.fallback_locale') ? ' active' : '' }}"
                        data-toggle="tab" href="#{{ $lang['iso_codes'] }}">
                        {!! $lang['name'] !!}
                    </a>
                </li>
                @endforeach
            </ul>
            @endif

            <div class="card">
                <div class="tab-content">
                    @foreach ($data['languages'] as $lang)
                    <div class="tab-pane fade{{ $lang['iso_codes'] == config('app.fallback_locale') ? ' show active' : '' }}" id="{{ $lang['iso_codes'] }}">
                        <div class="card-header d-flex justify-content-center">
                            <span class="font-weight-semibold">
                                @lang('global.language') : <b class="text-main">{{ $lang['name'] }}</b>
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('master/media.label.title')</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control text-bolder @error('title_'.$lang['iso_codes']) is-invalid @enderror" lang="{{ $lang['iso_codes'] }}"
                                        name="title_{{ $lang['iso_codes'] }}"
                                        value="{{ !isset($data['media']) ? old('title_'.$lang['iso_codes']) : old('title_'.$lang['iso_codes'], $data['media']->fieldLang('title', $lang['iso_codes'])) }}"
                                        placeholder="@lang('master/media.label.title')">
                                    @include('components.field-error', ['field' => 'title_'.$lang['iso_codes']])
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('master/media.label.description')</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control tiny-mce" name="description_{{ $lang['iso_codes'] }}">{!! !isset($data['media']) ? old('description_'.$lang['iso_codes']) : old('description_'.$lang['iso_codes'], $data['media']->fieldLang('description', $lang['iso_codes'])) !!}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </form>

    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.tiny.cloud/1/9p772cxf3cqe1smwkua8bcgyf2lf2sa9ak2cm6tunijg1zr9/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

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

@if (!Auth::user()->hasRole('developer|super'))
<script>
    $('.hide-form').hide();
</script>
@endif

@include('includes.button-fm')
@include('includes.tinymce-fm')
@endsection
