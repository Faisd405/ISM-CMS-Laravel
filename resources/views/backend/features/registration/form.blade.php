@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/bootstrap-material-datetimepicker/bootstrap-material-datetimepicker.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-8 col-md-8">

        <div class="card">
            <h6 class="card-header">
                @lang('global.form_attr', [
                    'attribute' => __('feature/registration.caption')
                ])
            </h6>
            <form action="{{ !isset($data['registration']) ? route('registration.store') : route('registration.update', ['id' => $data['registration']['id']]) }}" method="POST">
                @csrf
                @isset ($data['registration'])
                    @method('PUT')
                @endisset
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/registration.label.field1') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" 
                            value="{{ !isset($data['registration']) ? old('name') : old('name', $data['registration']['name']) }}" 
                            placeholder="@lang('feature/registration.placeholder.field1')" autofocus>
                            @include('components.field-error', ['field' => 'name'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/registration.label.field2') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <table class="table table-striped" style="width: 30%;">
                                <tbody>
                                    @forelse ($data['roles'] as $item)
                                    <tr>
                                        <th>{{ $item['name'] }}</th>
                                        <td class="text-center">
                                            <label class="switcher switcher-success">
                                                <input type="checkbox" class="switcher-input check-parent" data-id="{{ $item['id'] }}" name="roles[]" value="{{ $item['id'] }}" 
                                                    {{ isset($data['registration']) && !empty($data['registration']->roles) ? 
                                                        (in_array($item['id'], $data['registration']['roles']) ? 'checked' : '') : '' }}>
                                                <span class="switcher-indicator">
                                                <span class="switcher-yes">
                                                    <span class="ion ion-md-checkmark"></span>
                                                </span>
                                                <span class="switcher-no">
                                                    <span class="ion ion-md-close"></span>
                                                </span>
                                                </span>
                                            </label>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td>
                                            <i>@lang('global.data_attr_empty', [
                                                'attribute' => __('module/user.role.caption')
                                            ])</i>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            @error('roles')
                            <label class="small form-text invalid-feedback" style="display: inline-block; color:red;">{!! $message !!}</label>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                          <label class="col-form-label text-sm-right">@lang('global.type') <i class="text-danger">*</i></label>
                        </div>
                        <div class="col-md-10">
                            <select class="form-control show-tick @error('type') is-invalid @enderror" name="type" data-style="btn-default">
                                <option value="" disabled selected>@lang('global.select')</option>
                                @foreach (__('feature/registration.type') as $key => $val)
                                    <option value="{{ $key }}" {{ !isset($data['registration']) ? (old('type') == ''.$key.'' ? 'selected' : '') : 
                                        (old('type', $data['registration']['type']) == ''.$key.'' ? 'selected' : '') }}>
                                        {{ $val }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                            <label class="error jquery-validation-error small form-text invalid-feedback" style="display: inline-block; color:red;">{!! $message !!}</label>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('feature/registration.label.field3')</label>
                        </div>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input id="start_date" type="text" class="datetime-picker form-control @error('start_date') is-invalid @enderror" name="start_date"
                                    value="{{ !isset($data['registration']) ? old('start_date') : (!empty($data['registration']['start_date']) ? 
                                        old('start_date', $data['registration']['start_date']->format('Y-m-d H:i')) : old('start_date')) }}" 
                                    placeholder="@lang('feature/registration.placeholder.field3')">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="las la-calendar"></i></span>
                                    <span class="input-group-text">
                                        <input type="checkbox" id="enable_start" value="1">&nbsp; NULL
                                    </span>
                                </div>
                                @include('components.field-error', ['field' => 'start_date'])
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('feature/registration.label.field4')</label>
                        </div>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input id="end_date" type="text" class="datetime-picker form-control @error('end_date') is-invalid @enderror" name="end_date"
                                    value="{{ !isset($data['registration']) ? old('end_date') : (!empty($data['registration']['end_date']) ? 
                                        old('end_date', $data['registration']['end_date']->format('Y-m-d H:i')) : old('end_date')) }}" 
                                    placeholder="@lang('feature/registration.placeholder.field4')">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="las la-calendar"></i></span>
                                    <span class="input-group-text">
                                        <input type="checkbox" id="enable_end" value="1">&nbsp; NULL
                                    </span>
                                </div>
                                @include('components.field-error', ['field' => 'end_date'])
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                          <label class="col-form-label text-sm-right">@lang('global.status')</label>
                        </div>
                        <div class="col-md-10">
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="active" value="1"
                                {{ !isset($data['registration']) ? (old('active') ? 'checked' : 'checked') : (old('active', $data['registration']['active']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.active.1')</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" name="action" value="back" title="{{ isset($data['registration']) ? __('global.save_change') : __('global.save') }}">
                        <i class="las la-save"></i> {{ isset($data['registration']) ? __('global.save_change') : __('global.save') }}
                    </button>&nbsp;&nbsp;
                    <button type="submit" class="btn btn-danger" name="action" value="exit" title="{{ isset($data['registration']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                        <i class="las la-save"></i> {{ isset($data['registration']) ? __('global.save_change_exit') : __('global.save_exit') }}
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
<script src="{{ asset('assets/backend/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/bootstrap-material-datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
@endsection

@section('jsbody')
<script>
    //datetime
    $('.datetime-picker').bootstrapMaterialDatePicker({
        date: true,
        shortTime: false,
        format: 'YYYY-MM-DD HH:mm'
    });

    $('#enable_start').click(function() {
        if ($('#enable_start').prop('checked') == false) {
            var valEnd = "{{ now()->format('Y-m-d H:i') }}";
            $('#start_date').val(valEnd);
        } else {
            $('#start_date').val('');
        }
    });

    $('#enable_end').click(function() {
        if ($('#enable_end').prop('checked') == false) {
            var valEnd = "{{ now()->addMonth(1)->format('Y-m-d H:i') }}";
            $('#end_date').val(valEnd);
        } else {
            $('#end_date').val('');
        }
    });
</script>
@endsection