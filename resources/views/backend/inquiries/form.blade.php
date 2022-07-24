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
                    'attribute' => __('module/inquiry.caption')
                ])
            </h6>
            <form action="{{ !isset($data['inquiry']) ? route('inquiry.store', $queryParam) : 
                route('inquiry.update', array_merge(['id' => $data['inquiry']['id']], $queryParam)) }}" method="POST">
                @csrf
                @isset($data['inquiry'])
                    @method('PUT')
                    <input type="hidden" name="index_url_id" value="{{ $data['inquiry']['indexing']['id'] }}">
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
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/inquiry.label.field1') <i class="text-danger">*</i></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control mb-1 {{ !isset($data['inquiry']) ? 'gen_slug' : '' }} @error('name_'.$lang['iso_codes']) is-invalid @enderror" lang="{{ $lang['iso_codes'] }}" 
                                        name="name_{{ $lang['iso_codes'] }}" 
                                        value="{{ !isset($data['inquiry']) ? old('name_'.$lang['iso_codes']) : old('name_'.$lang['iso_codes'], $data['inquiry']->fieldLang('name', $lang['iso_codes'])) }}" 
                                        placeholder="@lang('module/inquiry.placeholder.field1')">
                                    @include('components.field-error', ['field' => 'name_'.$lang['iso_codes']])
                                </div>
                            </div>
                            <div class="form-group row {{ isset($data['inquiry']) && $data['inquiry']['config']['show_body'] == false ? 'hide-form' : '' }}">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/inquiry.label.field3')</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control tiny-mce @error('body_'.$lang['iso_codes']) is-invalid @enderror" name="body_{{ $lang['iso_codes'] }}">{!! !isset($data['inquiry']) ? old('body_'.$lang['iso_codes']) : old('body_'.$lang['iso_codes'], $data['inquiry']->fieldLang('body', $lang['iso_codes'])) !!}</textarea>
                                    @include('components.field-error', ['field' => 'body_'.$lang['iso_codes']])
                                </div>
                            </div>
                            <div class="form-group row {{ isset($data['inquiry']) && $data['inquiry']['config']['show_after_body'] == false ? 'hide-form' : '' }}">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/inquiry.label.field4')</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control tiny-mce @error('after_body_'.$lang['iso_codes']) is-invalid @enderror" name="after_body_{{ $lang['iso_codes'] }}">{!! !isset($data['inquiry']) ? old('after_body_'.$lang['iso_codes']) : old('after_body_'.$lang['iso_codes'], $data['inquiry']->fieldLang('after_body', $lang['iso_codes'])) !!}</textarea>
                                    @include('components.field-error', ['field' => 'after_body_'.$lang['iso_codes']])
                                </div>
                            </div>
        
                        </div>
                    </div>
                    @endforeach
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">@lang('module/inquiry.label.field2') <i class="text-danger">*</i></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control slug_spot @error('slug') is-invalid @enderror" lang="{{ App::getLocale() }}" name="slug"
                                    value="{{ !isset($data['inquiry']) ? old('slug') : old('slug', $data['inquiry']['slug']) }}" placeholder="{{ url('/') }}/url">
                                @include('components.field-error', ['field' => 'slug'])
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" name="action" value="back" title="{{ isset($data['inquiry']) ? __('global.save_change') : __('global.save') }}">
                        <i class="las la-save"></i> {{ isset($data['inquiry']) ? __('global.save_change') : __('global.save') }}
                    </button>&nbsp;&nbsp;
                    <button type="submit" class="btn btn-danger" name="action" value="exit" title="{{ isset($data['inquiry']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                        <i class="las la-save"></i> {{ isset($data['inquiry']) ? __('global.save_change_exit') : __('global.save_exit') }}
                    </button>&nbsp;&nbsp;
                    <button type="reset" class="btn btn-secondary" title="{{ __('global.reset') }}">
                    <i class="las la-redo-alt"></i> {{ __('global.reset') }}
                    </button>
                </div>

                {{-- SEO --}}
                <hr class="m-0">
                <div class="card-body">
                    <h6 class="font-weight-bold text-primary mb-4">SEO</h6>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.meta_title')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control mb-1" name="meta_title" value="{{ !isset($data['inquiry']) ? old('meta_title') : old('meta_title', $data['inquiry']['seo']['title']) }}" placeholder="@lang('global.meta_title')">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.meta_description')</label>
                        <div class="col-sm-10">
                            <textarea class="form-control mb-1" name="meta_description" placeholder="@lang('global.meta_description')">{{ !isset($data['inquiry']) ? old('meta_description') : old('meta_description', $data['inquiry']['seo']['description'])  }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('global.meta_keywords')</label>
                        <div class="col-sm-10">
                            <input class="form-control tags-input mb-1" name="meta_keywords" value="{{ !isset($data['inquiry']) ? old('meta_keywords') : old('meta_keywords', $data['inquiry']['seo']['keywords'])  }}" placeholder="">
                            <small class="form-text text-muted">@lang('global.separated_comma')</small>
                        </div>
                    </div>
                </div>

                {{-- SETTING --}}
                <hr class="m-0">
                <div class="card-body">
                    <h6 class="font-weight-bold text-primary mb-4">SETTING</h6>
                    <div class="form-row">
                        <div class="form-group col-md-12 {{ isset($data['inquiry']) && $data['inquiry']['config']['show_form'] == false ? 'hide-form' : 'hide-form' }}">
                            <label class="form-label">@lang('module/inquiry.label.field6')</label>
                            <input class="form-control tags-input mb-1" name="email" value="{{ isset($data['inquiry']) && !empty($data['inquiry']['email']) ? old('email', implode(",", $data['inquiry']['email'])) : old('email') }}" placeholder="">
                            <small class="form-text text-muted">@lang('global.separated_comma')</small>
                        </div>
                        <div class="form-group col-md-12 {{ isset($data['inquiry']) && $data['inquiry']['config']['send_mail_sender'] == false ? 'hide-form' : 'hide-form' }}">
                            <label class="form-label">Mail Template (Sender)</label>
                            <textarea class="form-control tiny-mce @error('mail_sender_template') is-invalid @enderror" name="mail_sender_template">{!! !isset($data['inquiry']) ? old('mail_sender_template') : old('mail_sender_template', $data['inquiry']['mail_sender_template']) !!}</textarea>
                        </div>
                        <div class="form-group col-md-6 {{ isset($data['inquiry']) && $data['inquiry']['config']['show_map'] == false ? 'hide-form' : 'hide-form' }}">
                            <label class="form-label">@lang('module/inquiry.label.field8')</label>
                            <input type="text" class="form-control" name="longitude" value="{{ !isset($data['inquiry']) ? old('longitude') : old('longitude', $data['inquiry']['longitude']) }}" placeholder="">
                        </div>
                        <div class="form-group col-md-6 {{ isset($data['inquiry']) && $data['inquiry']['config']['show_map'] == false ? 'hide-form' : 'hide-form' }}">
                            <label class="form-label">@lang('module/inquiry.label.field9')</label>
                            <input type="text" class="form-control" name="latitude" value="{{ !isset($data['inquiry']) ? old('latitude') : old('latitude', $data['inquiry']['latitude']) }}" placeholder="">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">@lang('global.status')</label>
                            <select class="form-control show-tick" name="publish" data-style="btn-default">
                                @foreach (__('global.label.publish') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['inquiry']) ? (old('publish') == ''.$key.'' ? 'selected' : '') : (old('publish', $data['inquiry']['publish']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">@lang('global.public')</label>
                            <select class="form-control show-tick" name="public" data-style="btn-default">
                                @foreach (__('global.label.optional') as $key => $value)
                                    <option value="{{ $key }}" {{ !isset($data['inquiry']) ? (old('public') == ''.$key.'' ? 'selected' : '') : (old('public', $data['inquiry']['public']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-12 {{ isset($data['inquiry']) && $data['inquiry']['config']['show_banner'] == false ? 'hide-form' : '' }}">
                            <label class="form-label">@lang('global.banner')</label>
                            <div class="input-group mb-2">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="image1" aria-label="Image" aria-describedby="button-image" name="banner_file" placeholder="Browse file..."
                                            value="{{ !isset($data['inquiry']) ? old('banner_file') : old('banner_file', $data['inquiry']['banner']['filepath']) }}">
                                    <div class="input-group-append" title="browse file">
                                        <button class="btn btn-primary file-name" id="button-image" type="button"><i class="las la-image"></i>&nbsp; @lang('global.browse')</button>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control" name="banner_title" placeholder="@lang('global.title')"
                                    value="{{ !isset($data['inquiry']) ? old('banner_title') : old('banner_title', $data['inquiry']['banner']['title']) }}">
                                <input type="text" class="form-control" name="banner_alt" placeholder="@lang('global.alt')"
                                    value="{{ !isset($data['inquiry']) ? old('banner_alt') : old('banner_alt', $data['inquiry']['banner']['alt']) }}">
                            </div>
                        </div>
                        <div class="form-group col-md-12" style="display: none;">
                            <label class="form-label">Content Template</label>
                            <textarea class="my-code-area" rows="10" style="width: 100%" name="content_template">{!! !isset($data['inquiry']) ? old('content_template') : old('content_template', $data['inquiry']['content_template']) !!}</textarea>
                        </div>
                    </div>
                </div>

                <hr class="border-light m-0">
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">@lang('global.locked')</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="locked" value="1"
                                {{ !isset($data['inquiry']) ? (old('locked') ? 'checked' : '') : (old('locked', $data['inquiry']['locked']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                            <small class="form-text text-muted">@lang('global.locked_info')</small>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">@lang('global.detail')</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="detail" value="1"
                                    {{ !isset($data['inquiry']) ? (old('detail') ? 'checked' : 'checked') : (old('detail', $data['inquiry']['detail']) ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                            <small class="form-text text-muted">@lang('global.detail_info')</small>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Body</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_body" value="1"
                                {{ !isset($data['inquiry']) ? (old('config_show_body', 1) ? 'checked' : '') : (old('config_show_body', $data['inquiry']['config']['show_body']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show After Body</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_after_body" value="1"
                                {{ !isset($data['inquiry']) ? (old('config_show_after_body', 1) ? 'checked' : '') : (old('config_show_after_body', $data['inquiry']['config']['show_after_body']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Banner</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_banner" value="1"
                                {{ !isset($data['inquiry']) ? (old('config_show_banner', 1) ? 'checked' : '') : (old('config_show_banner', $data['inquiry']['config']['show_banner']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Map</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_map" value="1"
                                {{ !isset($data['inquiry']) ? (old('config_show_map') ? 'checked' : '') : (old('config_show_map', $data['inquiry']['config']['show_map']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Form</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_form" value="1"
                                {{ !isset($data['inquiry']) ? (old('config_show_form') ? 'checked' : '') : (old('config_show_form', $data['inquiry']['config']['show_form']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Lock Form</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_lock_form" value="1"
                                {{ !isset($data['inquiry']) ? (old('config_lock_form') ? 'checked' : '') : (old('config_lock_form', $data['inquiry']['config']['lock_form']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Send Mail Sender</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_send_mail_sender" value="1"
                                {{ !isset($data['inquiry']) ? (old('config_send_mail_sender') ? 'checked' : '') : (old('config_send_mail_sender', $data['inquiry']['config']['send_mail_sender']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                        <div class="form-group col-md-2 hide-form">
                            <label class="form-label">Show Custom Field</label>
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="config_show_custom_field" value="1"
                                {{ !isset($data['inquiry']) ? (old('config_show_custom_field') ? 'checked' : '') : (old('config_show_custom_field', $data['inquiry']['config']['show_custom_field']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                        </div>
                    </div>
                </div>

                @if (Auth::user()->hasRole('developer|super') || isset($data['inquiry']) && $data['inquiry']['config']['show_custom_field'] == true && !empty($data['inquiry']['custom_fields']))
                {{-- CUSTOM FIELD --}}
                <hr class="m-0">
                <div class="table-responsive text-center">
                    <table class="table card-table table-bordered">
                        <thead>
                            @role('developer|super')
                            <tr>
                                <td colspan="3" class="text-center">
                                    <button id="add_field" type="button" class="btn btn-success icon-btn-only-sm btn-sm">
                                        <i class="las la-plus"></i> Field
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
                            @if (isset($data['inquiry']) && !empty($data['inquiry']['custom_fields']))
                                @foreach ($data['inquiry']['custom_fields'] as $key => $val)
                                <tr class="num-list" id="delete-{{ $key }}">
                                    <td>
                                        <input type="text" class="form-control" name="cf_name[]" placeholder="name" 
                                            value="{{ $key }}" {{ !Auth::user()->hasRole('developer|super') ? 'readonly' : '' }}>
                                    </td>
                                    <td>
                                        <textarea class="form-control" name="cf_value[]" placeholder="value">{{ $val }}</textarea>
                                    </td>
                                    @role('developer|super')
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
<script src="{{ asset('assets/backend/jquery-ace/ace/ace.js') }}"></script>
<script src="{{ asset('assets/backend/jquery-ace/ace/theme-monokai.js') }}"></script>
<script src="{{ asset('assets/backend/jquery-ace/ace/mode-html.js') }}"></script>
<script src="{{ asset('assets/backend/jquery-ace/jquery-ace.min.js') }}"></script>
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

        @if(isset($data['inquiry']) && !empty($data['inquiry']['custom_fields']))
            var no = {{ count($data['inquiry']['custom_fields']) }};
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

    $('.my-code-area').ace({ theme: 'monokai', lang: 'html' });
</script>

@if (!Auth::user()->hasRole('developer|super'))
<script>
    $('.hide-form').hide();
</script>
@endif

@include('includes.button-fm')
@include('includes.tinymce-fm')
@endsection