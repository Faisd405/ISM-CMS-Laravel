@extends('layouts.backend.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-8 col-md-8">
        
        <div class="card">
            <h6 class="card-header">
                @lang('global.form_attr', [
                    'attribute' => __('module/user.role.caption')
                ])
            </h6>
            <form action="{{ !isset($data['role']) ? route('role.store', $queryParam) : 
                route('role.update', array_merge(['id' => $data['role']['id']], $queryParam)) }}" method="POST">
                @csrf
                @isset ($data['role'])
                    @method('PUT')
                @endisset

                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/user.role.label.name') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control text-bolder @error('name') is-invalid @enderror" name="name" 
                                value="{{ !isset($data['role']) ? old('name') : old('name', $data['role']['name']) }}" 
                                placeholder="@lang('module/user.role.placeholder.name')" {{ isset($data['role']) && $data['role']['locked'] == 1 ? 'readonly' : 'autofocus' }}>
                            @include('components.field-error', ['field' => 'name'])
                            <small class="form-text">@lang('global.lower_case')</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/user.role.label.level') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <select class="form-control text-bolder @error('level') is-invalid @enderror" name="level" data-style="btn-default">
                                <option value="" disabled selected>@lang('global.select')</option>
                                @for ($i = 1; $i <= 100; $i++)
                                <option value="{{ $i }}" 
                                    {{ !isset($data['role']) ? (old('level') == $i ? 'selected' : '') : (old('level', $data['role']['level']) == $i ? 'selected' : '') }}>{{ $i }}</option>
                                @endfor
                            </select>
                            @include('components.field-error', ['field' => 'level'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                          <label class="col-form-label text-sm-right">@lang('module/user.role.label.role_register')</label>
                        </div>
                        <div class="col-md-10">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" id="is_register" type="checkbox" name="is_register" value="1"
                                    {{ !isset($data['role']) ? (old('is_register') ? 'checked' : '') : (old('is_register', $data['role']['is_register']) == 1 ? 'checked' : '') }}>
                                <label class="custom-control-label" for="is_register"></label>
                            </div>
                            <small class="form-text">@lang('module/user.role.placeholder.role_register')</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                          <label class="col-form-label text-sm-right">@lang('global.locked')</label>
                        </div>
                        <div class="col-md-10">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" id="locked" type="checkbox" name="locked" value="1"
                                    {{ !isset($data['role']) ? (old('locked') ? 'checked' : '') : (old('locked', $data['role']['locked']) == 1 ? 'checked' : '') }}>
                                <label class="custom-control-label" for="locked"></label>
                            </div>
                            <small class="form-text">@lang('global.locked_info')</small>
                        </div>
                    </div>
                </div>

                <div class="card-footer justify-content-center">
                    <div class="box-btn">
                        <button class="btn btn-main w-icon" type="submit" name="action" value="back" title="{{ isset($data['role']) ? __('global.save_change') : __('global.save') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['role']) ? __('global.save_change') : __('global.save') }}</span>
                        </button>
                        <button class="btn btn-success w-icon" type="submit" name="action" value="exit" title="{{ isset($data['role']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['role']) ? __('global.save_change_exit') : __('global.save_exit') }}</span>
                        </button>
                        <button type="reset" class="btn btn-default w-icon" title="{{ __('global.reset') }}">
                            <i class="fi fi-rr-refresh"></i>
                            <span>{{ __('global.reset') }}</span>
                        </button>
                    </div>
                </div>

                <div class="table-responsive" style="overflow: scroll; height: 400px;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width:10px;">#</th>
                                <th style="width:250px;">Module</th>
                                <th>@lang('global.action')</th>
                                <th style="width:100px;">Check</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data['permissions'] as $item)
                            <tr class="table-secondary">
                                <td><strong>{{ $loop->iteration }}</strong></td>
                                <td>
                                    <strong>
                                        {!! Str::replace('_', ' ', Str::upper($item['name'])) !!}
                                    </strong>
                                </td>
                                <td><strong><i>READ</i></strong></td>
                                <td class="text-center">
                                    <div class="form-group m-0">
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input check-parent" id="parent{{ $item['id'] }}" type="checkbox" data-id="{{ $item['id'] }}" name="permission[]" value="{{ $item['id'] }}" 
                                                {{ isset($data['permission_ids']) ? (in_array($item['id'], $data['permission_ids']) ? 'checked' : '') : '' }}>
                                            <label class="custom-control-label" for="parent{{ $item['id'] }}"></label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @foreach ($item->where('parent', $item['id'])->get() as $child)
                            @php
                                $parentName = substr_replace($item['name'], '', -1);
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td></td>
                                <td>
                                    <i>{{ Str::upper(Str::replace('_', ' ', $child['name'])) }}</i>
                                </td>
                                <td class="text-center">
                                    <div class="form-group m-0">
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input check-child-{{ $child['parent'] }}" id="child{{ $child['id'] }}" type="checkbox" name="permission[]" value="{{ $child['id'] }}"
                                                {{ isset($data['permission_ids']) ? (in_array($child['id'], $data['permission_ids']) ? 'checked' : '') : '' }}>
                                            <label class="custom-control-label" for="child{{ $child['id'] }}"></label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @empty
                            <tr>
                                <td colspan="4" align="center">
                                    <i>
                                        <strong style="color:red;">
                                        ! @lang('global.data_attr_empty', [
                                            'attribute' => __('module/user.permission.caption')
                                        ]) !
                                        </strong>
                                    </i>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@section('jsbody')
<script>
    //checked child
    $('.check-parent').click(function () {
        var parent = $(this).attr('data-id');
       $('.check-child-' + parent).not(this).prop('checked', this.checked);
    });
</script>
@endsection