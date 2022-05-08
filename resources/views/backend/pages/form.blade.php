@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/pages/account.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/bootstrap-tagsinput/bootstrap-tagsinput.css') }}">
<script src="{{ asset('assets/backend/admin.js') }}"></script>
<script src="{{ asset('assets/backend/wysiwyg/tinymce.min.js') }}"></script>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-9 col-md-9">

        @if (isset($data['parent']))
        <div class="alert alert-dark alert-dismissible fade show">
            <i class="las la-thumbtack"></i> Under <strong>" {!! $data['parent']->fieldLang('title') !!} "</strong>
        </div>
        @endif

        <div class="card">
            <h6 class="card-header">
                @lang('global.form_attr', [
                    'attribute' => __('module/page.caption')
                ])
            </h6>
            <form action="{{ !isset($data['page']) ? route('page.store', ['parent' => Request::get('parent')]) : 
                route('page.update', ['id' => $data['page']['id']]) }}" method="POST">
                @csrf
                @isset($data['page'])
                    @method('PUT')
                    <input type="hidden" name="index_url_id" value="{{ $data['page']['indexing']['id'] }}">
                @endisset

                {{-- MAIN --}}
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
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/page.label.field1') <i class="text-danger">*</i></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control mb-1 gen_slug @error('title_'.$lang['iso_codes']) is-invalid @enderror" lang="{{ $lang['iso_codes'] }}" 
                                        name="title_{{ $lang['iso_codes'] }}" 
                                        value="{{ !isset($data['page']) ? old('title_'.$lang['iso_codes']) : old('title_'.$lang['iso_codes'], $data['page']->fieldLang('title', $lang['iso_codes'])) }}" 
                                        placeholder="@lang('module/page.placeholder.field1')">
                                    @include('components.field-error', ['field' => 'title_'.$lang['iso_codes']])
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/page.label.field3')</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control tiny-mce" name="intro_{{ $lang['iso_codes'] }}">{!! !isset($data['page']) ? old('intro_'.$lang['iso_codes']) : old('intro_'.$lang['iso_codes'], $data['page']->fieldLang('intro', $lang['iso_codes'])) !!}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/page.label.field4')</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control tiny-mce" name="content_{{ $lang['iso_codes'] }}">{!! !isset($data['page']) ? old('content_'.$lang['iso_codes']) : old('content_'.$lang['iso_codes'], $data['page']->fieldLang('content', $lang['iso_codes'])) !!}</textarea>
                                </div>
                            </div>
        
                        </div>
                    </div>
                    @endforeach
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">@lang('module/page.label.field2') <i class="text-danger">*</i></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control slug_spot @error('slug') is-invalid @enderror" lang="{{ App::getLocale() }}" name="slug"
                                    value="{{ !isset($data['page']) ? old('slug') : old('slug', $data['page']['slug']) }}" placeholder="{{ url('/') }}/SLUG">
                                @include('components.field-error', ['field' => 'slug'])
                            </div>
                        </div>
                        @if (config('cms.module.master.tags.active') == true)
                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">@lang('master/tags.caption')</label>
                            <div class="col-sm-10">
                                <input class="form-control tags-input mb-1" id="suggest_tags" name="tags" value="{{ !isset($data['tags']) ? old('tags') : old('tags', $data['tags']) }}" placeholder="">
                                <small class="text-muted">@lang('global.separated_comma')</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" name="action" value="back" title="{{ isset($data['page']) ? __('global.save_change') : __('global.save') }}">
                        <i class="las la-save"></i> {{ isset($data['page']) ? __('global.save_change') : __('global.save') }}
                    </button>&nbsp;&nbsp;
                    <button type="submit" class="btn btn-danger" name="action" value="exit" title="{{ isset($data['page']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                        <i class="las la-save"></i> {{ isset($data['page']) ? __('global.save_change_exit') : __('global.save_exit') }}
                    </button>&nbsp;&nbsp;
                    <button type="reset" class="btn btn-secondary" title="{{ __('global.reset') }}">
                    <i class="las la-redo-alt"></i> {{ __('global.reset') }}
                    </button>
                </div>

                {{-- SEO --}}
                <hr class="m-0">
                <div class="card-body">
                    <h6 class="font-weight-semibold mb-4">SEO</h6>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.meta_title')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control mb-1" name="meta_title" value="{{ !isset($data['page']) ? old('meta_title') : old('meta_title', $data['page']['seo']['title']) }}" placeholder="@lang('global.meta_title')">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.meta_description')</label>
                        <div class="col-sm-10">
                            <textarea class="form-control mb-1" name="meta_description" placeholder="@lang('global.meta_description')">{{ !isset($data['page']) ? old('meta_description') : old('meta_description', $data['page']['seo']['description'])  }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.meta_keywords')</label>
                        <div class="col-sm-10">
                            <input class="form-control tags-input mb-1" name="meta_keywords" value="{{ !isset($data['page']) ? old('meta_keywords') : old('meta_keywords', $data['page']['seo']['keywords'])  }}" placeholder="">
                            <small class="text-muted">@lang('global.separated_comma')</small>
                        </div>
                    </div>
                </div>

                {{-- SETTING --}}
                <hr class="m-0">
                <div class="card-body">
                    <h6 class="font-weight-semibold mb-4">SETTING</h6>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.status')</label>
                        <div class="col-sm-10">
                            <select class="form-control show-tick" name="publish" data-style="btn-default">
                                @foreach (__('global.label.publish') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['page']) ? (old('publish') == ''.$key.'' ? 'selected' : '') : (old('publish', $data['page']['publish']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.public')</label>
                        <div class="col-sm-10">
                            <select class="form-control show-tick" name="public" data-style="btn-default">
                                @foreach (__('global.label.optional') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['page']) ? (old('public') == ''.$key.'' ? 'selected' : '') : (old('public', $data['page']['public']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.locked')</label>
                        <div class="col-sm-10">
                            <select class="form-control show-tick" name="locked" data-style="btn-default">
                                @foreach (__('global.label.optional') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['page']) ? (old('locked') == ''.$key.'' ? 'selected' : '') : (old('locked', $data['page']['locked']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                        <label class="col-form-label text-sm-right">@lang('global.detail')</label>
                        </div>
                        <div class="col-md-10">
                            <label class="switcher switcher-success">
                                <input type="checkbox" class="switcher-input" name="is_detail" value="1" 
                                    {{ !isset($data['page']) ? (old('is_detail', 1) ? 'checked' : '') : (old('is_detail', $data['page']['config']['is_detail']) ? 'checked' : '') }}>
                                <span class="switcher-indicator">
                                <span class="switcher-yes">
                                    <span class="ion ion-md-checkmark"></span>
                                </span>
                                <span class="switcher-no">
                                    <span class="ion ion-md-close"></span>
                                </span>
                                </span>
                            </label>
                        </div>
                    </div>
                    @role('super')
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.template')</label>
                        <div class="col-sm-10">
                            <select class="select2 show-tick" name="template_id" data-style="btn-default">
                                <option value=" " selected>DEFAULT</option>
                                @foreach ($data['templates'] as $template)
                                    <option value="{{ $template['id'] }}" {{ !isset($data['page']) ? (old('template_id') == $template['id'] ? 'selected' : '') : (old('template_id', $data['page']['template_id']) == $template['id'] ? 'selected' : '') }}>
                                        {{ $template['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @else
                    <input type="hidden" name="template_id" value="{{ !isset($data['page']) ? null : $data['page']['template_id'] }}">
                    @endrole
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.cover')</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control" id="image1" aria-label="Image" aria-describedby="button-image" name="cover_file"
                                        value="{{ !isset($data['page']) ? old('cover_file') : old('cover_file', $data['page']['cover']['filepath']) }}">
                                <div class="input-group-append" title="browse file">
                                    <button class="btn btn-primary file-name" id="button-image" type="button"><i class="las la-image"></i>&nbsp; @lang('global.browse')</button>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="@lang('global.title')" name="cover_title" value="{{ !isset($data['page']) ? old('cover_title') : old('cover_title', $data['page']['cover']['title']) }}">
                                </div>
                                <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="@lang('global.alt')" name="cover_alt" value="{{ !isset($data['page']) ? old('cover_alt') : old('cover_alt', $data['page']['cover']['alt']) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.banner')</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control" id="image2" aria-label="Image2" aria-describedby="button-image2" name="banner_file"
                                        value="{{ !isset($data['page']) ? old('banner_file') : old('banner_file', $data['page']['banner']['filepath']) }}">
                                <div class="input-group-append" title="browse file">
                                    <button class="btn btn-primary file-name" id="button-image2" type="button"><i class="las la-image"></i>&nbsp; @lang('global.browse')</button>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="@lang('global.title')" name="banner_title" value="{{ !isset($data['page']) ? old('banner_title') : old('banner_title', $data['page']['banner']['title']) }}">
                                </div>
                                <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="@lang('global.alt')" name="banner_alt" value="{{ !isset($data['page']) ? old('banner_alt') : old('banner_alt', $data['page']['banner']['alt']) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.hide') Field</label>
                        <div class="col-sm-10">
                            <div>
                                <label class="form-check form-check-inline">
                                  <input class="form-check-input" type="checkbox" name="hide_intro" value="1" 
                                  {{ !isset($data['page']) ? (old('hide_intro') ? 'checked' : '') : (old('hide_intro', $data['page']['config']['hide_intro']) ? 'checked' : '') }}>
                                  <span class="form-check-label">
                                    @lang('module/page.label.field3')
                                  </span>
                                </label>
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="hide_tags" value="1" 
                                    {{ !isset($data['page']) ? (old('hide_tags') ? 'checked' : '') : (old('hide_tags', $data['page']['config']['hide_tags']) ? 'checked' : '') }}>
                                    <span class="form-check-label">
                                      @lang('master/tags.caption')
                                    </span>
                                  </label>
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="hide_cover" value="1" 
                                    {{ !isset($data['page']) ? (old('hide_cover') ? 'checked' : '') : (old('hide_cover', $data['page']['config']['hide_cover']) ? 'checked' : '') }}>
                                    <span class="form-check-label">
                                      @lang('global.cover')
                                    </span>
                                </label>
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="hide_banner" value="1" 
                                    {{ !isset($data['page']) ? (old('hide_banner') ? 'checked' : '') : (old('hide_banner', $data['page']['config']['hide_banner']) ? 'checked' : '') }}>
                                    <span class="form-check-label">
                                      @lang('global.banner')
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                @if (!isset($data['page']) && Auth::user()->hasRole('super') || isset($data['page']))
                {{-- CUSTOM FIELD --}}
                <hr class="m-0">
                <div class="table-responsive text-center">
                    <table class="table card-table table-bordered">
                        @role('super')
                        <thead class="text-center">
                            <tr>
                                <td colspan="3" class="text-center">
                                    <button id="add_field" type="button" class="btn btn-success icon-btn-only-sm btn-sm">
                                        <i class="las la-plus"></i> Field
                                    </button>
                                </td>
                            </tr>
                        </thead>
                        @endrole
                        <tbody id="list_field">
                            @if (isset($data['page']) && !empty($data['page']['custom_fields']))
                                @foreach ($data['page']['custom_fields'] as $key => $val)
                                <tr class="num-list" id="delete-{{ $key }}">
                                    <td>
                                        <input type="text" class="form-control" name="cf_name[]" placeholder="name" 
                                            value="{{ $key }}" {{ !Auth::user()->hasRole('super') ? 'readonly' : '' }}>
                                    </td>
                                    <td>
                                        <textarea class="form-control" name="cf_value[]" placeholder="value">{{ $val }}</textarea>
                                    </td>
                                    @role('super')
                                    <td style="width: 30px;">
                                        <button type="button" class="btn icon-btn btn-sm btn-danger" id="remove_field" data-id="{{ $key }}"><i class="las la-times"></i></button>
                                    </td>
                                    @endrole
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                @endif

            </form>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/backend/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/bootstrap-tagsinput/bootstrap-tagsinput.js') }}"></script>
@endsection

@section('jsbody')
<script>
    //select2
    $(function () {
        $('.select2').select2();
    });

    //tags
    $('.tags-input').tagsinput({ tagClass: 'badge badge-primary' });

    //custom field
    $(function()  {

        @if(isset($data['page']) && !empty($data['page']['custom_fields']))
            var no = {{ count($data['page']['custom_fields']) }};
        @else
            var no = 1;
        @endif
        $("#add_field").click(function() {
            $("#list_field").append(`
                <tr class="num-list" id="delete-`+no+`">
                    <td>
                        <input type="text" class="form-control" name="cf_name[]" placeholder="name">
                    </td>
                    <td>
                        <textarea class="form-control" name="cf_value[]" placeholder="value"></textarea>
                    </td>
                    <td style="width: 30px;">
                        <button type="button" class="btn icon-btn btn-sm btn-danger" id="remove_field" data-id="`+no+`"><i class="las la-times"></i></button>
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

@if (!Auth::user()->hasRole('super'))
<script>
    //hide form yang tidak diperlukan
    $('.hd').hide();
</script>
@endif

@include('includes.button-fm')
@include('includes.tinymce-fm')
@endsection