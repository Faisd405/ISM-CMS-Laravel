@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/pages/account.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-9 col-md-9">

        <form action="{{ !isset($data['file']) ? route('gallery.file.store', array_merge(['albumId' => $data['album']['id']], $queryParam)) :
            route('gallery.file.update', array_merge(['albumId' => $data['album']['id'], 'id' => $data['file']['id']], $queryParam)) }}" method="POST"
                enctype="multipart/form-data">
            @csrf
            @isset($data['file'])
                @method('PUT')
                <input type="hidden" name="type" value="{{ $data['file']['type'] }}">
                <input type="hidden" name="image_type" value="{{ $data['file']['image_type'] }}">
                <input type="hidden" name="video_type" value="{{ $data['file']['video_type'] }}">
            @endisset

            @include('components.alert-error')

            <div class="card">
                <h5 class="card-header my-2">
                    @lang('global.form_attr', [
                        'attribute' => __('module/gallery.file.caption')
                    ])
                </h5>
                <hr class="border-light m-0">
                <div class="card-header m-0">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <span>{{ Str::upper(__('module/gallery.album.caption')) }}</span>
                        </li>
                        <li class="breadcrumb-item active">
                            <b class="text-main">{{ $data['album']->fieldLang('name') }}</b>
                        </li>
                    </ol>
                </div>
                <hr class="border-light m-0">
                <div class="card-body">
                    {{-- type --}}
                    @if (!isset($data['file']))
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.type') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <select id="type" class="form-control show-tick @error('type') is-invalid @enderror" name="type" data-style="btn-default">
                                <option value=" " selected disabled>@lang('global.select')</option>
                                @foreach (config('cms.module.gallery.file.type') as $key => $value)
                                    @if ($key == 0 && $data['album']['config']['type_image'] == true)
                                    <option value="{{ $key }}">
                                        {{ $value }}
                                    </option>
                                    @endif
                                    @if ($key == 1 && $data['album']['config']['type_video'] == true)
                                    <option value="{{ $key }}">
                                        {{ $value }}
                                    </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('type')
                            <label class="error jquery-validation-error small form-text invalid-feedback" style="display: inline-block; color:red;">{!! $message !!}</label>
                            @enderror
                        </div>
                    </div>
                    @else
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.type') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control text-bolder" value="{{ config('cms.module.gallery.file.type.'.$data['file']['type']) }}" readonly>
                        </div>
                    </div>
                    @endif

                    @if (!isset($data['file']))
                    {{-- image type --}}
                    <div class="form-group row" id="image-type-form">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/gallery.file.label.image')  <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <select id="image-type" class="form-control show-tick @error('image_type') is-invalid @enderror" name="image_type" data-style="btn-default">
                                <option value=" " selected disabled>@lang('global.select')</option>
                                @foreach (config('cms.module.gallery.file.type_image') as $key => $value)
                                    <option value="{{ $key }}">
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('image_type')
                            <label class="error jquery-validation-error small form-text invalid-feedback" style="display: inline-block; color:red;">{!! $message !!}</label>
                            @enderror
                        </div>
                    </div>
                    {{-- video type --}}
                    <div class="form-group row" id="video-type-form">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/gallery.file.label.video')  <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <select id="video-type" class="form-control show-tick @error('video_type') is-invalid @enderror" name="video_type" data-style="btn-default">
                                <option value=" " selected disabled>@lang('global.select')</option>
                                @foreach (config('cms.module.gallery.file.type_video') as $key => $value)
                                    <option value="{{ $key }}">
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('video_type')
                            <label class="error jquery-validation-error small form-text invalid-feedback" style="display: inline-block; color:red;">{!! $message !!}</label>
                            @enderror
                        </div>
                    </div>
                    @elseif ($data['file']['type'] == '0')
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/gallery.file.label.image')  <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control text-bolder" value="{{ config('cms.module.gallery.file.type_image.'.$data['file']['image_type']) }}" readonly>
                        </div>
                    </div>
                    @elseif ($data['file']['type'] == '1')
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/gallery.file.label.video')  <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control text-bolder" value="{{ config('cms.module.gallery.file.type_video.'.$data['file']['video_type']) }}" readonly>
                        </div>
                    </div>
                    @endif
                    @if (!isset($data['file']) || isset($data['file']) && $data['file']['image_type'] == '0')
                    <div class="form-group row" id="image-upload">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('module/gallery.file.label.file')</label>
                        </div>
                        <div class="col-md-10">
                            @if (isset($data['file']))
                            <input type="hidden" name="old_file" value="{{ $data['file']['file'] }}">
                            @endif
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFileImage" lang="en" name="file_image">
                                <label class="custom-file-label btn-main" for="customFileImage">
                                    @lang('global.browse')
                                </label>
                            </div>
                            @include('components.field-error', ['field' => 'file_image'])
                            <small class="form-text">
                                Allowed : <strong>{{ Str::upper(config('cms.files.gallery.mimes')) }}</strong>.
                                Pixel : <strong>{{ config('cms.files.gallery.pixel') }}</strong>.
                                Max Size : <strong>{{ config('cms.files.gallery.size') }}</strong>
                            </small>
                        </div>
                    </div>
                    @endif
                    @if (!isset($data['file']) || isset($data['file']) && $data['file']['image_type'] == '1')
                    <div class="form-group row" id="image-fileman">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/configuration.filemanager.caption')</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control text-bolder @error('filemanager') is-invalid @enderror" id="image1" aria-label="Image" aria-describedby="button-image" name="filemanager"
                                        value="{{ isset($data['file']) ? old('filemanager', $data['file']['file']) : '' }}" placeholder="@lang('global.browse') file...">
                                <div class="input-group-append" title="browse file">
                                    <button class="btn btn-main file-name w-icon" id="button-image" type="button"><i class="fi fi-rr-folder"></i>&nbsp; @lang('global.browse')</button>
                                </div>
                            </div>
                            @include('components.field-error', ['field' => 'filemanager'])
                        </div>
                    </div>
                    @endif
                    @if (!isset($data['file']) || isset($data['file']) && $data['file']['image_type'] == '2')
                    <div class="form-group row" id="image-url">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/gallery.file.label.url_image')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control text-bolder @error('file_url') is-invalid @enderror" name="file_url" placeholder=""
                                value="{{ isset($data['file']) ? old('file_url', $data['file']['file']) : '' }}">
                            @include('components.field-error', ['field' => 'file_url'])
                        </div>
                    </div>
                    @endif

                    @if (!isset($data['file']) || isset($data['file']) && $data['file']['video_type'] == '0')
                    <div id="video-upload">
                        @if (isset($data['file']))
                        <input type="hidden" name="old_file" value="{{ $data['file']['file'] }}">
                        @endif
                        <div class="form-group row">
                            <div class="col-md-2 text-md-right">
                                <label class="col-form-label text-sm-right">@lang('module/gallery.file.label.file')</label>
                            </div>
                            <div class="col-md-10">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="customFileVideo" lang="en" name="file_video">
                                    <label class="custom-file-label btn-main" for="customFileVideo">
                                        @lang('global.browse')
                                    </label>
                                </div>
                                @include('components.field-error', ['field' => 'file_video'])
                                <small class="form-text">
                                    Allowed : <strong>{{ Str::upper(config('cms.files.gallery.mimes_video')) }}</strong>.
                                    Pixel : <strong>{{ config('cms.files.gallery.pixel') }}</strong>.
                                    Max Size : <strong>{{ config('cms.files.gallery.size') }}</strong>
                                </small>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if (!isset($data['file']) || isset($data['file']) && $data['file']['video_type'] == '1')
                    <div class="form-group row" id="video-youtube">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/gallery.file.label.youtube_id')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control text-bolder @error('file_youtube') is-invalid @enderror" name="file_youtube" placeholder=""
                                value="{{ isset($data['file']) ? old('file_youtube', $data['file']['file']) : '' }}">
                            <small class="form-text">https://www.youtube.com/watch?v=<strong>hZK640cDj2s</strong></small>
                            @include('components.field-error', ['field' => 'file_youtube'])
                        </div>
                    </div>
                    @endif
                    @if (!isset($data['file']) || isset($data['file']) && $data['file']['type'] == '1')
                    @if (isset($data['file']))
                    <input type="hidden" name="old_thumbnail" value="{{ $data['file']['thumbnail'] }}">
                    @endif
                    <div class="form-group row" id="video-thumbnail">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('module/gallery.file.label.thumbnail')</label>
                        </div>
                        <div class="col-md-10">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFileThumbnail" lang="en" name="thumbnail">
                                <label class="custom-file-label btn-main" for="customFileThumbnail">
                                    @lang('global.browse')
                                </label>
                            </div>
                            @include('components.field-error', ['field' => 'thumbnail'])
                            <small class="form-text">
                                Allowed : <strong>{{ Str::upper(config('cms.files.gallery.thumbnail.mimes')) }}</strong>.
                                Pixel : <strong>{{ config('cms.files.gallery.thumbnail.pixel') }}</strong>.
                                Max Size : <strong>{{ config('cms.files.gallery.thumbnail.size') }}</strong>
                            </small>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-footer justify-content-center">
                    <div class="box-btn">
                        <button class="btn btn-main w-icon" type="submit" name="action" value="back" title="{{ isset($data['file']) ? __('global.save_change') : __('global.save') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['file']) ? __('global.save_change') : __('global.save') }}</span>
                        </button>
                        <button class="btn btn-success w-icon" type="submit" name="action" value="exit" title="{{ isset($data['file']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['file']) ? __('global.save_change_exit') : __('global.save_exit') }}</span>
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
                            <div class="form-group row {{ isset($data['file']) && $data['file']['config']['show_title'] == false ? 'hide-form' : '' }}">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/gallery.file.label.title') <i class="text-danger">*</i></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control text-bolder @error('title_'.$lang['iso_codes']) is-invalid @enderror" lang="{{ $lang['iso_codes'] }}"
                                        name="title_{{ $lang['iso_codes'] }}"
                                        value="{{ !isset($data['file']) ? old('title_'.$lang['iso_codes']) : old('title_'.$lang['iso_codes'], $data['file']->fieldLang('title', $lang['iso_codes'])) }}"
                                        placeholder="@lang('module/gallery.file.placeholder.title')">
                                    @include('components.field-error', ['field' => 'title_'.$lang['iso_codes']])
                                </div>
                            </div>
                            <div class="form-group row {{ isset($data['file']) && $data['file']['config']['show_description'] == false ? 'hide-form' : '' }}">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/gallery.file.label.description')</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control tiny-mce" name="description_{{ $lang['iso_codes'] }}">{!! !isset($data['file']) ? old('description_'.$lang['iso_codes']) : old('description_'.$lang['iso_codes'], $data['file']->fieldLang('description', $lang['iso_codes'])) !!}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card">
                <h6 class="card-header text-main">
                    SETTING
                </h6>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="form-label">@lang('global.status')</label>
                            <select class="form-control show-tick" name="publish" data-style="btn-default">
                                @foreach (__('global.label.publish') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['file']) ? (old('publish') == ''.$key.'' ? 'selected' : '') : (old('publish', $data['file']['publish']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">@lang('global.public')</label>
                            <select class="form-control show-tick" name="public" data-style="btn-default">
                                @foreach (__('global.label.optional') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['file']) ? (old('public') == ''.$key.'' ? 'selected' : '') : (old('public', $data['file']['public']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="border-light m-0 hide-form">
                <div class="card-body hide-form">
                    <div class="form-row">
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">@lang('global.locked')</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="locked" value="1"
                                {{ !isset($data['file']) ? (old('locked') ? 'checked' : '') : (old('locked', $data['file']['locked']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                            <small class="form-text">@lang('global.locked_info')</small>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Title</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_title" value="1"
                                {{ !isset($data['file']) ? (old('config_show_title', 1) ? 'checked' : '') : (old('config_show_title', $data['file']['config']['show_title']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Description</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_description" value="1"
                                {{ !isset($data['file']) ? (old('config_show_description', 1) ? 'checked' : '') : (old('config_show_description', $data['file']['config']['show_description']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Custom Field</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_custom_field" value="1"
                                {{ !isset($data['file']) ? (old('config_show_custom_field') ? 'checked' : '') : (old('config_show_custom_field', $data['file']['config']['show_custom_field']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                    </div>
                </div>

                @if (Auth::user()->hasRole('developer|super') || isset($data['file']) && $data['file']['config']['show_custom_field'] == true && !empty($data['file']['custom_fields']))
                {{-- CUSTOM FIELD --}}
                <hr class="border-light m-0">
                <div class="table-responsive text-center">
                    <table class="table card-table">
                        <thead class="text-center">
                            @role('developer|super')
                            <tr>
                                <td colspan="3" class="text-center">
                                    <button id="add_field" type="button" class="btn btn-success btn-sm w-icon">
                                        <i class="fi fi-rr-add"></i> <span>Field</span>
                                    </button>
                                </td>
                            </tr>
                            @endrole
                            <tr>
                                <th>NAME</th>
                                <th>VALUE</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="list_field">
                            @if (isset($data['file']) && !empty($data['file']['custom_fields']))
                                @foreach ($data['file']['custom_fields'] as $key => $val)
                                <tr class="num-list" id="delete-{{ $key }}">
                                    <td>
                                        <input type="text" class="form-control text-bolder" name="cf_name[]" placeholder="name"
                                            value="{{ $key }}" {{ !Auth::user()->hasRole('developer|super') ? 'readonly' : '' }}>
                                    </td>
                                    <td>
                                        <textarea class="form-control text-bolder" name="cf_value[]" placeholder="value">{{ $val }}</textarea>
                                    </td>
                                    @role('developer|super')
                                    <td style="width: 30px;">
                                        <button type="button" class="btn icon-btn btn-sm btn-danger" id="remove_field" data-id="{{ $key }}"><i class="fi fi-rr-cross-small"></i></button>
                                    </td>
                                    @endrole
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

        </form>

    </div>
</div>
@endsection

@section('scripts')
<script src="{{ env('TINY_MCE_API_KEY') }}" referrerpolicy="origin"></script>

@endsection

@section('jsbody')
@if (!isset($data['file']))
<script>
    //type
    $('#image-type-form').hide();
    $('#video-type-form').hide();
    $('#video-thumbnail').hide();

    $('#type').on('change', function() {
        $('#image-type').prop('selectedIndex', ' ');
        $('#video-type').prop('selectedIndex', ' ');
        if (this.value == '0') {
            $('#image-type-form').show();
            $('#video-type-form').hide();
            $('#video-thumbnail').hide();

            $('#video-upload').hide();
            $('#video-youtube').hide();
        }

        if (this.value == '1') {
            $('#image-type-form').hide();
            $('#video-type-form').show();

            $('#image-upload').hide();
            $('#image-fileman').hide();
            $('#image-url').hide();
        }
    });

    //image type
    $('#image-upload').hide();
    $('#image-fileman').hide();
    $('#image-url').hide();

    $('#image-type').on('change', function() {

        if (this.value == '0') {
            $('#image-upload').show();
            $('#image-fileman').hide();
            $('#image-url').hide();
        }

        if (this.value == '1') {
            $('#image-upload').hide();
            $('#image-fileman').show();
            $('#image-url').hide();
        }

        if (this.value == '2') {
            $('#image-upload').hide();
            $('#image-fileman').hide();
            $('#image-url').show();
        }
    });

    //video type
    $('#video-upload').hide();
    $('#video-youtube').hide();

    $('#video-type').on('change', function() {

        if (this.value == '0') {
            $('#video-upload').show();
            $('#video-youtube').hide();
            $('#video-thumbnail').show();
        }

        if (this.value == '1') {
            $('#video-upload').hide();
            $('#video-youtube').show();
            $('#video-thumbnail').show();
        }
    });
</script>
@endif

<script>
    // FILE BROWSE
    $(".custom-file-input").on("change", function () {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });

    //custom field
    $(function()  {

        @if(isset($data['file']) && !empty($data['file']['custom_fields']))
            var no = {{ count($data['file']['custom_fields']) }};
        @else
            var no = 1;
        @endif
        $("#add_field").click(function() {
            $("#list_field").append(`
                <tr class="num-list" id="delete-`+no+`">
                    <td>
                        <input type="text" class="form-control text-bolder" name="cf_name[]" placeholder="name">
                    </td>
                    <td>
                        <textarea class="form-control text-bolder" name="cf_value[]" placeholder="value"></textarea>
                    </td>
                    <td style="width: 30px;">
                        <button type="button" class="btn icon-btn btn-sm btn-danger" id="remove_field" data-id="`+no+`"><i class="fi fi-rr-cross-small"></i></button>
                    </td>
                </tr>
            `);

            var noOfColumns = $('.num-list').length;
            var maxNum = 10;
            if (noOfColumns < maxNum) {
                $("#add_field").show();
            } else {
                $("#add_field").hide();
            }

            no++;
        });

    });

    //remove custom field
    $(document).on('click', '#remove_field', function() {
        var id = $(this).attr("data-id");
        $("#delete-"+id).remove();
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
