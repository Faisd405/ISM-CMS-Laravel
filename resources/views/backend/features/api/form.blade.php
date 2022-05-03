@extends('layouts.backend.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-8 col-md-8">

        <div class="card">
            <h6 class="card-header">
                @lang('global.form_attr', [
                    'attribute' => __('feature/api.caption')
                ])
            </h6>
            <form action="{{ !isset($data['api']) ? route('api.store') : route('api.update', ['id' => $data['api']['id']]) }}" method="POST">
                @csrf
                @isset ($data['api'])
                    @method('PUT')
                @endisset
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/api.label.field1') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" 
                            value="{{ !isset($data['api']) ? old('name') : old('name', $data['api']['name']) }}" 
                            placeholder="@lang('feature/api.placeholder.field1')">
                            @include('components.field-error', ['field' => 'name'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/api.label.field2')</label>
                        <div class="col-sm-10">
                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" placeholder="@lang('feature/api.placeholder.field2')">{{ !isset($data['api']) ? old('description') : old('description', $data['api']['description']) }}</textarea>
                        @include('components.field-error', ['field' => 'description'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                          <label class="col-form-label text-sm-right">@lang('global.status')</label>
                        </div>
                        <div class="col-md-10">
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="active" value="1"
                                {{ !isset($data['api']) ? (old('active') ? 'checked' : 'checked') : (old('active', $data['api']['active']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.active.1')</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/api.label.field6') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <button type="button" id="add_ip" class="btn btn-success btn-sm mb-2">
                                <i class="las la-plus"></i> @lang('feature/api.label.field6')
                            </button>
                            <div id="list_ip">
                                @if(isset($data['api']) && !empty($data['api']['ip_address']))
                                    @foreach ($data['api']['ip_address'] as $key => $ip)
                                    <div class="input-group mb-2 num-list" id="delete-{{ $key }}">
                                        <input type="text" class="form-control" name="ip_address[]" value="{{ $ip }}"
                                            placeholder="@lang('feature/api.placeholder.field6')">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-danger" id="remove_ip" data-id="{{ $key }}"><i class="las la-times"></i></button>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                            <small class="text-muted">@lang('feature/api.placeholder.field6')</small>
                            @error('ip_address')
                            <label class="error jquery-validation-error small form-text invalid-feedback" style="display: inline-block;color:red;">{!! $message !!}</label>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/api.label.field5')</label>
                        <div class="col-sm-10">
                            <table class="table table-striped" style="width: 30%;">
                                <tbody>
                                    @forelse (config('cms.module.feature.api.mod') as $key => $val)
                                    <tr>
                                        <th>{{ $val }}</th>
                                        <td class="text-center">
                                            <label class="switcher switcher-success">
                                                <input type="checkbox" class="switcher-input" name="modules[]" value="{{ $val }}" 
                                                    {{ isset($data['api']) && !empty($data['api']->modules) ? 
                                                        (in_array($val, $data['api']['modules']) ? 'checked' : '') : '' }}>
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
                                        <td><i>Nothing</i></td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <small class="text-muted">@lang('feature/api.placeholder.field5')</small>
                            @error('roles')
                            <label class="small form-text invalid-feedback" style="display: inline-block; color:red;">{!! $message !!}</label>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" name="action" value="back" title="{{ isset($data['api']) ? __('global.save_change') : __('global.save') }}">
                        <i class="las la-save"></i> {{ isset($data['api']) ? __('global.save_change') : __('global.save') }}
                    </button>&nbsp;&nbsp;
                    <button type="submit" class="btn btn-danger" name="action" value="exit" title="{{ isset($data['api']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                        <i class="las la-save"></i> {{ isset($data['api']) ? __('global.save_change_exit') : __('global.save_exit') }}
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

@section('jsbody')
<script>
    $(function()  {

        @if(isset($data['api']) && !empty($data['api']['ip_address']))
            var no = {{ count($data['api']['ip_address']) }};
        @else
            var no = 1;
        @endif
        $("#add_ip").click(function() {
            $("#list_ip").append(`
                <div class="input-group mb-2 num-list" id="delete-`+no+`">
                    <input type="text" class="form-control" name="ip_address[]"
                        placeholder="127.0.0.1">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger" id="remove_ip" data-id="`+no+`"><i class="las la-times"></i></button>
                    </div>
                </div>
            `);

            var noOfColumns = $('.num-list').length;
            var maxNum = 5;
            if (noOfColumns < maxNum) {
                $("#add_ip").show();
            } else {
                $("#add_ip").hide();
            }

            no++;
        });

    });

    $(document).on('click', '#remove_ip', function() {
        var id = $(this).attr("data-id");
        $("#delete-"+id).remove();
    });
</script>
@endsection