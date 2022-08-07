@extends('layouts.backend.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-8 col-md-8">
        <form action="{{ !isset($data['api']) ? route('api.store', $queryParam) : 
            route('api.update', array_merge(['id' => $data['api']['id']], $queryParam)) }}" method="POST">
            @csrf
            @isset ($data['api'])
                @method('PUT')
            @endisset
            <div class="card">
                <h5 class="card-header my-2">
                    @lang('global.form_attr', [
                        'attribute' => __('feature/api.caption')
                    ])
                </h5>
                <hr class="border-light m-0">
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/api.label.name') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control text-bolder @error('name') is-invalid @enderror" name="name" 
                            value="{{ !isset($data['api']) ? old('name') : old('name', $data['api']['name']) }}" 
                            placeholder="@lang('feature/api.placeholder.name')">
                            @include('components.field-error', ['field' => 'name'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/api.label.description')</label>
                        <div class="col-sm-10">
                        <textarea class="form-control text-bolder @error('description') is-invalid @enderror" name="description" placeholder="@lang('feature/api.placeholder.description')">{{ !isset($data['api']) ? old('description') : old('description', $data['api']['description']) }}</textarea>
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
                    <div class="form-group row hide-form">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('global.locked')</label>
                        </div>
                        <div class="col-md-10">
                            <label class="custom-control custom-checkbox m-0">
                                <input type="checkbox" class="custom-control-input" name="locked" value="1"
                                {{ !isset($data['api']) ? (old('locked') ? 'checked' : '') : (old('locked', $data['api']['locked']) == 1 ? 'checked' : '') }}>
                                <span class="custom-control-label">@lang('global.label.optional.1')</span>
                            </label>
                            <small class="form-text text-muted">@lang('global.locked_info')</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/api.label.ip_address') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <button type="button" id="add_ip" class="btn btn-success btn-sm w-icon mb-2">
                                <i class="fi fi-rr-add"></i> <span>@lang('feature/api.label.ip_address')</span>
                            </button>
                            <div id="list_ip">
                                @if(isset($data['api']) && !empty($data['api']['ip_address']))
                                    @foreach ($data['api']['ip_address'] as $key => $ip)
                                    <div class="input-group mb-2 num-list" id="delete-{{ $key }}">
                                        <input type="text" class="form-control" name="ip_address[]" value="{{ $ip }}"
                                            placeholder="@lang('feature/api.placeholder.ip_address')">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-danger" id="remove_ip" data-id="{{ $key }}">
                                                <i class="fi fi-rr-cross-circle"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                            @include('components.field-error', ['field' => 'ip_address'])
                            <small class="form-text text-muted">@lang('feature/api.placeholder.ip_address')</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('feature/api.label.module')</label>
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
                            @error('roles')
                            <label class="small form-text invalid-feedback" style="display: inline-block; color:red;">{!! $message !!}</label>
                            @enderror
                            <small class="form-text text-muted">@lang('feature/api.placeholder.module')</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer justify-content-center">
                    <div class="box-btn">
                        <button class="btn btn-main w-icon" type="submit" name="action" value="back" title="{{ isset($data['api']) ? __('global.save_change') : __('global.save') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['api']) ? __('global.save_change') : __('global.save') }}</span>
                        </button>
                        <button class="btn btn-success w-icon" type="submit" name="action" value="exit" title="{{ isset($data['api']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['api']) ? __('global.save_change_exit') : __('global.save_exit') }}</span>
                        </button>
                        <button type="reset" class="btn btn-default w-icon" title="{{ __('global.reset') }}">
                            <i class="fi fi-rr-refresh"></i>
                            <span>{{ __('global.reset') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>

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
                        <button type="button" class="btn btn-danger" id="remove_ip" data-id="`+no+`"><i class="fi fi-rr-cross-circle"></i></button>
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

@if(!Auth::user()->hasRole('developer|super'))
<script>
    $('.hide-form').hide();
</script>
@endif
@endsection