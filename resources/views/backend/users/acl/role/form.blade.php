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
            <form action="{{ !isset($data['role']) ? route('role.store') : route('role.update', ['id' => $data['role']['id']]) }}" method="POST">
                @csrf
                @isset ($data['role'])
                    @method('PUT')
                @endisset
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/user.role.label.field1') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" 
                                value="{{ !isset($data['role']) ? old('name') : old('name', $data['role']['name']) }}" 
                                placeholder="@lang('module/user.role.placeholder.field1')" autofocus>
                            <small class="text-muted">@lang('global.lower_case')</small>
                            @include('components.field-error', ['field' => 'name'])
                        </div>
                    </div>
    
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">@lang('module/user.role.label.field4') <i class="text-danger">*</i></label>
                        <div class="col-sm-10">
                            <select class="form-control @error('level') is-invalid @enderror" name="level" data-style="btn-default">
                                <option value="" disabled selected>@lang('global.select')</option>
                                @for ($i = 1; $i <= 100; $i++)
                                <option value="{{ $i }}" 
                                    {{ !isset($data['role']) ? (old('level') == $i ? 'selected' : '') : (old('level', $data['role']['level']) == $i ? 'selected' : '') }}>{{ $i }}</option>
                                @endfor
                            </select>
                            @error('level')
                            <label class="error jquery-validation-error small form-text invalid-feedback" style="display: inline-block; color:red;">{!! $message !!}</label>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" name="action" value="back" title="{{ isset($data['role']) ? __('global.save_change') : __('global.save') }}">
                        <i class="las la-save"></i> {{ isset($data['role']) ? __('global.save_change') : __('global.save') }}
                    </button>&nbsp;&nbsp;
                    <button type="submit" class="btn btn-danger" name="action" value="exit" title="{{ isset($data['role']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                        <i class="las la-save"></i> {{ isset($data['role']) ? __('global.save_change_exit') : __('global.save_exit') }}
                    </button>&nbsp;&nbsp;
                    <button type="reset" class="btn btn-secondary" title="{{ __('global.reset') }}">
                    <i class="las la-redo-alt"></i> {{ __('global.reset') }}
                    </button>
                </div>
                <div class="table-responsive" style="overflow: scroll; height: 400px;">
                    <table class="table mb-2 card-table">
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
                            <tr class="bg-primary" style="color: #fff;">
                                <td><strong>{{ $loop->iteration }}</strong></td>
                                <td>
                                    <strong>
                                        {!! Str::replace('_', ' ', Str::upper($item['name'])) !!}
                                    </strong>
                                </td>
                                <td><strong><i>READ</i></strong></td>
                                <td class="text-center">
                                    <label class="form-check form-check-inline">
                                        <input type="checkbox" class="form-check-input check-parent" data-id="{{ $item['id'] }}" name="permission[]" value="{{ $item['id'] }}" 
                                            {{ isset($data['permission_ids']) ? (in_array($item['id'], $data['permission_ids']) ? 'checked' : '') : '' }}>
                                        <span class="form-check-label"></span>
                                    </label>
                                </td>
                            </tr>
                            @foreach ($item->where('parent', $item['id'])->get() as $child)
                            @php
                                $parentName = substr_replace($item['name'], '', -1);
                                // $childName = str_replace([$parentName.'_', 'content_category_', 'banner_category_'], '', $child['name'])
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td></td>
                                <td>
                                    <i>{{ Str::upper(Str::replace('_', ' ', $child['name'])) }}</i>
                                </td>
                                <td class="text-center">
                                    <label class="form-check form-check-inline">
                                        <input type="checkbox" class="form-check-input check-child-{{ $child['parent'] }}" name="permission[]" value="{{ $child['id'] }}"
                                            {{ isset($data['permission_ids']) ? (in_array($child['id'], $data['permission_ids']) ? 'checked' : '') : '' }}>
                                        <span class="form-check-label"></span>
                                    </label>
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