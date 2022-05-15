@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/pages/account.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/select2/select2.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-9 col-md-9">

        <div class="card">
            <h6 class="card-header">
                @lang('global.form_attr', [
                    'attribute' => __('module/event.field.caption')
                ])
            </h6>
            <form action="{{ !isset($data['field']) ? route('event.field.store', ['eventId' => $data['event']['id']]) : 
                route('event.field.update', ['eventId' => $data['event']['id'], 'id' => $data['field']['id']]) }}" method="POST" 
                    enctype="multipart/form-data">
                @csrf
                @isset($data['field'])
                    @method('PUT')
                @endisset

                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/event.field.label.field2') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" 
                            value="{{ !isset($data['field']) ? old('name') : old('name', $data['field']['name']) }}" placeholder="">
                            @include('components.field-error', ['field' => 'name'])
                        </div>
                    </div>
                </div>

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
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/event.field.label.field1') <i class="text-danger">*</i></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control mb-1 @error('label_'.$lang['iso_codes']) is-invalid @enderror" lang="{{ $lang['iso_codes'] }}" 
                                        name="label_{{ $lang['iso_codes'] }}" 
                                        value="{{ !isset($data['field']) ? old('label_'.$lang['iso_codes']) : old('label_'.$lang['iso_codes'], $data['field']->fieldLang('label', $lang['iso_codes'])) }}" 
                                        placeholder="@lang('module/event.field.placeholder.field1')">
                                    @include('components.field-error', ['field' => 'label_'.$lang['iso_codes']])
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2 text-sm-right">@lang('module/event.field.label.field10')</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control mb-1 @error('placeholder_'.$lang['iso_codes']) is-invalid @enderror" lang="{{ $lang['iso_codes'] }}" name="placeholder_{{ $lang['iso_codes'] }}" 
                                        value="{{ !isset($data['field']) ? old('placeholder_'.$lang['iso_codes']) : old('placeholder_'.$lang['iso_codes'], $data['field']->fieldLang('placeholder', $lang['iso_codes'])) }}" 
                                        placeholder="@lang('module/event.field.placeholder.field10')">
                                    @include('components.field-error', ['field' => 'placeholder_'.$lang['iso_codes']])
                                </div>
                            </div>
        
                        </div>
                    </div>
                    @endforeach
                </div>

                <hr class="m-0">
                <div class="card-body">
                    <h6 class="font-weight-semibold mb-4">FIELD SETTING</h6>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/event.field.label.field3')</label>
                        <div class="col-sm-10">
                            <select class="select2 show-tick" name="type" data-style="btn-default">
                                @foreach (config('cms.field.event_field') as $key => $field)
                                    <option value="{{ $key }}" {{ !isset($data['field']) ? (old('type') == ''.$key.'' ? 'selected' : '') : (old('type', $data['field']['type']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $field }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/event.field.label.field4')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('property_type') is-invalid @enderror" name="property_type" 
                                value="{{ !isset($data['field']) ? old('property_type') : old('property_type', $data['field']['properties']['type']) }}" 
                                placeholder="@lang('module/event.field.placeholder.field4')">
                        </div>
                    </div>
                    {{-- <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/event.field.label.field5')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('property_id') is-invalid @enderror" name="property_id" 
                                value="{{ !isset($data['field']) ? old('property_id') : old('property_id', $data['field']['properties']['id']) }}" 
                                placeholder="@lang('module/event.field.placeholder.field5')">
                        </div>
                    </div> --}}
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/event.field.label.field6')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('property_class') is-invalid @enderror" name="property_class" 
                                value="{{ !isset($data['field']) ? old('property_class') : old('property_class', $data['field']['properties']['class']) }}" 
                                placeholder="@lang('module/event.field.placeholder.field6')">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/event.field.label.field7')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('property_attribute') is-invalid @enderror" name="property_attribute" 
                                value="{{ !isset($data['field']) ? old('property_attribute') : old('property_attribute', $data['field']['properties']['attribute']) }}" 
                                placeholder="@lang('module/event.field.placeholder.field7')">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/event.field.label.field8')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('validation') is-invalid @enderror" name="validation" 
                            value="{{ !isset($data['field']) ? old('validation') : old('validation', $data['field']['validation']) }}" 
                            placeholder="@lang('module/event.field.placeholder.field8')">
                        </div>
                    </div>
                </div>

                 {{-- CUSTOM FIELD --}}
                 <hr class="m-0">
                 <div class="table-responsive text-center">
                     <table class="table card-table table-bordered">
                         <thead>
                             <tr>
                                 <td colspan="3" class="text-center">
                                     <button id="add_field" type="button" class="btn btn-success icon-btn-only-sm btn-sm">
                                         <i class="las la-plus"></i> @lang('module/event.field.label.field11')
                                     </button>
                                 </td>
                             </tr>
                             <tr>
                                 <th>Label</th>
                                 <th>Value</th>
                                 <th></th>
                             </tr>
                         </thead>
                         <tbody id="list_field">
                             @if (isset($data['field']) && !empty($data['field']['options']))
                                 @foreach ($data['field']['options'] as $key => $val)
                                 <tr class="num-list" id="delete-{{ $key }}">
                                     <td>
                                         <input type="text" class="form-control" name="opt_label[]" placeholder="label" value="{{ $key }}">
                                     </td>
                                     <td>
                                         <textarea class="form-control" name="opt_value[]" placeholder="value">{{ $val }}</textarea>
                                     </td>
                                     <td style="width: 30px;">
                                         <button type="button" class="btn icon-btn btn-sm btn-danger" id="remove_field" data-id="{{ $key }}"><i class="las la-times"></i></button>
                                     </td>
                                 </tr>
                                 @endforeach
                             @endif
                         </tbody>
                     </table>
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
                                     <option value="{{ $key }}" {{ !isset($data['field']) ? (old('publish') == ''.$key.'' ? 'selected' : '') : (old('publish', $data['field']['publish']) == ''.$key.'' ? 'selected' : '') }}>
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
                                     <option value="{{ $key }}" {{ !isset($data['field']) ? (old('public') == ''.$key.'' ? 'selected' : '') : (old('public', $data['field']['public']) == ''.$key.'' ? 'selected' : '') }}>
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
                                     <option value="{{ $key }}" {{ !isset($data['field']) ? (old('locked') == ''.$key.'' ? 'selected' : '') : (old('locked', $data['field']['locked']) == ''.$key.'' ? 'selected' : '') }}>
                                         {{ $value }}
                                     </option>
                                 @endforeach
                             </select>
                         </div>
                     </div>
                 </div>

                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" name="action" value="back" title="{{ isset($data['field']) ? __('global.save_change') : __('global.save') }}">
                        <i class="las la-save"></i> {{ isset($data['field']) ? __('global.save_change') : __('global.save') }}
                    </button>&nbsp;&nbsp;
                    <button type="submit" class="btn btn-danger" name="action" value="exit" title="{{ isset($data['field']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                        <i class="las la-save"></i> {{ isset($data['field']) ? __('global.save_change_exit') : __('global.save_exit') }}
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
    //select2
    $(function () {
        $('.select2').select2();
    });

    //custom field
    $(function()  {

    @if(isset($data['field']) && !empty($data['field']['options']))
        var no = {{ count($data['field']['options']) }};
    @else
        var no = 1;
    @endif

    $("#add_field").click(function() {
        $("#list_field").append(`
            <tr class="num-list" id="delete-`+no+`">
                <td>
                    <input type="text" class="form-control" name="opt_label[]" placeholder="label">
                </td>
                <td>
                    <textarea class="form-control" name="opt_value[]" placeholder="value"></textarea>
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
@endsection