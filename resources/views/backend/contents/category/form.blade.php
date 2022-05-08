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

        <div class="card">
            <h6 class="card-header">
                @lang('global.form_attr', [
                    'attribute' => __('module/content.category.caption')
                ])
            </h6>
            <form action="{{ !isset($data['category']) ? route('content.category.store', ['sectionId' => $data['section']['id']]) : 
                route('content.category.update', ['sectionId' => $data['section']['id'], 'id' => $data['category']['id']]) }}" method="POST">
                @csrf
                @isset($data['category'])
                    @method('PUT')
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
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/content.category.label.field1') <i class="text-danger">*</i></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control mb-1 gen_slug @error('name_'.$lang['iso_codes']) is-invalid @enderror" lang="{{ $lang['iso_codes'] }}" 
                                        name="name_{{ $lang['iso_codes'] }}" 
                                        value="{{ !isset($data['category']) ? old('name_'.$lang['iso_codes']) : old('name_'.$lang['iso_codes'], $data['category']->fieldLang('name', $lang['iso_codes'])) }}" 
                                        placeholder="@lang('module/content.category.placeholder.field1')">
                                    @include('components.field-error', ['field' => 'name_'.$lang['iso_codes']])
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/content.category.label.field3')</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control tiny-mce" name="description_{{ $lang['iso_codes'] }}">{!! !isset($data['category']) ? old('description_'.$lang['iso_codes']) : old('description_'.$lang['iso_codes'], $data['category']->fieldLang('description', $lang['iso_codes'])) !!}</textarea>
                                </div>
                            </div>
        
                        </div>
                    </div>
                    @endforeach
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">@lang('module/content.category.label.field2') <i class="text-danger">*</i></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control slug_spot @error('slug') is-invalid @enderror" lang="{{ App::getLocale() }}" name="slug"
                                    value="{{ !isset($data['category']) ? old('slug') : old('slug', $data['category']['slug']) }}" placeholder="{{ url('/') }}/{{ $data['section']['slug'] }}/cat/SLUG">
                                @include('components.field-error', ['field' => 'slug'])
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" name="action" value="back" title="{{ isset($data['category']) ? __('global.save_change') : __('global.save') }}">
                        <i class="las la-save"></i> {{ isset($data['category']) ? __('global.save_change') : __('global.save') }}
                    </button>&nbsp;&nbsp;
                    <button type="submit" class="btn btn-danger" name="action" value="exit" title="{{ isset($data['category']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                        <i class="las la-save"></i> {{ isset($data['category']) ? __('global.save_change_exit') : __('global.save_exit') }}
                    </button>&nbsp;&nbsp;
                    <button type="reset" class="btn btn-secondary" title="{{ __('global.reset') }}">
                    <i class="las la-redo-alt"></i> {{ __('global.reset') }}
                    </button>
                </div>

                {{-- POST SETTING --}}
                <hr class="m-0">
                <div class="card-body">
                    <h6 class="font-weight-semibold mb-4">POST SETTING</h6>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">Post perpage</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control mb-1 @error('post_perpage') is-invalid @enderror" name="post_perpage" 
                                value="{{ !isset($data['category']) ? old('post_perpage') : old('post_perpage', $data['category']['post_perpage']) }}" 
                                placeholder="limit list post">
                            @include('components.field-error', ['field' => 'post_perpage'])
                        </div>
                    </div>
                </div>

                {{-- SEO --}}
                <hr class="m-0">
                <div class="card-body">
                    <h6 class="font-weight-semibold mb-4">SEO</h6>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.meta_title')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control mb-1" name="meta_title" value="{{ !isset($data['category']) ? old('meta_title') : old('meta_title', $data['category']['seo']['title']) }}" placeholder="@lang('global.meta_title')">

                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.meta_description')</label>
                        <div class="col-sm-10">
                            <textarea class="form-control mb-1" name="meta_description" placeholder="@lang('global.meta_description')">{{ !isset($data['category']) ? old('meta_description') : old('meta_description', $data['category']['seo']['description'])  }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.meta_keywords')</label>
                        <div class="col-sm-10">
                            <input class="form-control tags-input mb-1" name="meta_keywords" value="{{ !isset($data['category']) ? old('meta_keywords') : old('meta_keywords', $data['category']['seo']['keywords'])  }}" placeholder="">
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
                                    <option value="{{ $key }}" {{ !isset($data['category']) ? (old('publish') == ''.$key.'' ? 'selected' : '') : (old('publish', $data['category']['publish']) == ''.$key.'' ? 'selected' : '') }}>
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
                                    <option value="{{ $key }}" {{ !isset($data['category']) ? (old('public') == ''.$key.'' ? 'selected' : '') : (old('public', $data['category']['public']) == ''.$key.'' ? 'selected' : '') }}>
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
                                    <option value="{{ $key }}" {{ !isset($data['category']) ? (old('locked') == ''.$key.'' ? 'selected' : '') : (old('locked', $data['category']['locked']) == ''.$key.'' ? 'selected' : '') }}>
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
                                    {{ !isset($data['category']) ? (old('is_detail', 1) ? 'checked' : '') : (old('is_detail', $data['category']['config']['is_detail']) ? 'checked' : '') }}>
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
                                @foreach ($data['templates'] as $tmpList)
                                    <option value="{{ $tmpList['id'] }}" {{ !isset($data['category']) ? (old('template_id') == $tmpList['id'] ? 'selected' : '') : (old('template_id', $data['category']['template_id']) == $tmpList['id'] ? 'selected' : '') }}>
                                        {{ $tmpList['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @else
                    <input type="hidden" name="template_id" value="{{ !isset($data['category']) ? null : $data['category']['template_id'] }}">
                    @endrole
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.banner')</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" class="form-control" id="image1" aria-label="Image" aria-describedby="button-image" name="banner_file"
                                        value="{{ !isset($data['category']) ? old('banner_file') : old('banner_file', $data['category']['banner']['filepath']) }}">
                                <div class="input-group-append" title="browse file">
                                    <button class="btn btn-primary file-name" id="button-image" type="button"><i class="las la-image"></i>&nbsp; @lang('global.browse')</button>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="@lang('global.title')" name="banner_title" value="{{ !isset($data['category']) ? old('banner_title') : old('banner_title', $data['category']['banner']['title']) }}">
                                </div>
                                <div class="col-sm-6">
                                <input type="text" class="form-control" placeholder="@lang('global.alt')" name="banner_alt" value="{{ !isset($data['category']) ? old('banner_alt') : old('banner_alt', $data['category']['banner']['alt']) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.hide') Field</label>
                        <div class="col-sm-10">
                            <div>
                                <label class="form-check form-check-inline">
                                  <input class="form-check-input" type="checkbox" name="hide_description" value="1" 
                                  {{ !isset($data['category']) ? (old('hide_description') ? 'checked' : '') : (old('hide_description', $data['category']['config']['hide_description']) ? 'checked' : '') }}>
                                  <span class="form-check-label">
                                    @lang('module/content.category.label.field3')
                                  </span>
                                </label>
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="hide_banner" value="1" 
                                    {{ !isset($data['category']) ? (old('hide_banner') ? 'checked' : '') : (old('hide_banner', $data['category']['config']['hide_banner']) ? 'checked' : '') }}>
                                    <span class="form-check-label">
                                      @lang('global.banner')
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CUSTOM FIELD --}}
                <hr class="m-0">
                <div class="table-responsive text-center">
                    <table class="table card-table table-bordered">
                        @role('super')
                        <thead>
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
                            @if (isset($data['category']) && !empty($data['category']['custom_fields']))
                                @foreach ($data['category']['custom_fields'] as $key => $val)
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

        @if(isset($data['category']) && !empty($data['category']['custom_fields']))
            var no = {{ count($data['category']['custom_fields']) }};
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