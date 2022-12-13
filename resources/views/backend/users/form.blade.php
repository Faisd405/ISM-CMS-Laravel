@extends('layouts.backend.layout')

@if (config('cms.setting.recaptcha') == true)
    @section('jshead')
        {!! htmlScriptTagJsApi() !!}
    @endsection
@endif

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/select2/select2.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-8 col-md-8">

        <form action="{{ !isset($data['user']) ? route('user.store', $queryParam) :
            route('user.update', array_merge(['id' => $data['user']['id']], $queryParam)) }}" method="POST">
            @csrf
            @isset ($data['user'])
                @method('PUT')
                <input type="hidden" name="old_email" value="{{ $data['user']['email'] }}">
            @endisset
            <div class="card">
                <h5 class="card-header my-2">
                    @lang('global.form_attr', [
                        'attribute' => __('module/user.caption')
                    ])
                </h5>
                <hr class="border-light m-0">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('module/user.label.name') <i class="text-danger">*</i></label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" class="form-control text-bolder @error('name') is-invalid @enderror" name="name"
                                value="{{ !isset($data['user']) ? old('name') : old('name', $data['user']['name']) }}"
                                placeholder="@lang('module/user.placeholder.name')" autofocus>
                                @include('components.field-error', ['field' => 'name'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('module/user.label.email') <i class="text-danger">*</i></label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" class="form-control text-bolder @error('email') is-invalid @enderror" name="email"
                                value="{{ !isset($data['user']) ? old('email') : old('email', $data['user']['email']) }}"
                                placeholder="@lang('module/user.placeholder.email')">
                                @include('components.field-error', ['field' => 'email'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('module/user.label.phone')</label>
                        </div>
                        <div class="col-md-10">
                            <input type="number" class="form-control text-bolder @error('phone') is-invalid @enderror" name="phone"
                                value="{{ !isset($data['user']) ? old('phone') : old('phone', $data['user']['phone']) }}"
                                placeholder="@lang('module/user.placeholder.phone')">
                            @include('components.field-error', ['field' => 'phone'])
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('module/user.label.username') <i class="text-danger">*</i></label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" class="form-control text-bolder @error('username') is-invalid @enderror" name="username"
                                value="{{ !isset($data['user']) ? old('username') : old('username', $data['user']['username']) }}"
                                placeholder="@lang('module/user.placeholder.username')">
                                @include('components.field-error', ['field' => 'username'])
                            <small class="form-text">@lang('module/user.username_info')</small>
                        </div>
                    </div>
                    @if (!isset($data['user']) || isset($data['user']) && $data['user']->roles[0]['level'] <= 5)
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('module/user.role.caption') <i class="text-danger">*</i></label>
                        </div>
                        <div class="col-md-10">
                            <select id="roles" class="form-control select2 @error('roles') is-invalid @enderror" name="roles" data-style="btn-default">
                                <option value="" disabled selected>@lang('global.select')</option>
                                @foreach ($data['roles'] as $item)
                                    <option value="{{ $item['name'] }}" {{ !isset($data['user']) ? (old('roles') == $item['name'] ? 'selected' : '') :
                                        (old('roles', $data['user']->roles[0]['name']) == $item['name'] ? 'selected' : '') }}>
                                        {{ Str::upper($item['name']) }}
                                    </option>
                                @endforeach
                            </select>
                            @include('components.field-error', ['field' => 'roles'])
                        </div>
                    </div>
                    @else
                    <input type="hidden" name="roles" value="{{ $data['user']->roles[0]['name'] }}">
                    @endif
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('global.status')</label>
                        </div>
                        <div class="col-md-10">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" id="active" type="checkbox" name="active" value="1"
                                    {{ !isset($data['user']) ? (old('active') ? 'checked' : 'checked') : (old('active', $data['user']['active']) == 1 ? 'checked' : '') }}>
                                <label class="custom-control-label" for="active"></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row hide-form">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('global.locked')</label>
                        </div>
                        <div class="col-md-10">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" id="locked" type="checkbox" name="locked" value="1"
                                    {{ !isset($data['user']) ? (old('locked') ? 'checked' : '') : (old('locked', $data['user']['locked']) == 1 ? 'checked' : '') }}>
                                <label class="custom-control-label" for="locked"></label>
                            </div>
                            <small class="form-text">@lang('global.locked_info')</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('module/user.label.password')</label>
                        </div>
                        <div class="col-md-10">
                            <div class="input-group input-group-merge">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                    name="password" placeholder="@lang('module/user.placeholder.password')">
                                <div class="input-group-append">
                                    <i class="input-group-text toggle-password fi fi-rr-eye" toggle="#password"></i>
                                </div>
                                @include('components.field-error', ['field' => 'password'])
                            </div>
                            <small class="form-text">
                                @lang('module/user.password_info')
                            </small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2 text-md-right">
                            <label class="col-form-label text-sm-right">@lang('module/user.label.password_confirmation')</label>
                        </div>
                        <div class="col-md-10">
                            <div class="input-group input-group-merge">
                                <input id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                    name="password_confirmation"  placeholder="@lang('module/user.placeholder.password_confirmation')">
                                <div class="input-group-append">
                                    <i class="input-group-text toggle-password-confirmation fi fi-rr-eye" toggle="#password_confirmation"></i>
                                </div>
                                @include('components.field-error', ['field' => 'password_confirmation'])
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer justify-content-center">
                    <div class="box-btn">
                        <button class="btn btn-main w-icon" type="submit" name="action" value="back" title="{{ isset($data['users']) ? __('global.save_change') : __('global.save') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['users']) ? __('global.save_change') : __('global.save') }}</span>
                        </button>
                        <button class="btn btn-success w-icon" type="submit" name="action" value="exit" title="{{ isset($data['users']) ? __('global.save_change_exit') : __('global.save_exit') }}">
                            <i class="fi fi-rr-disk"></i>
                            <span>{{ isset($data['users']) ? __('global.save_change_exit') : __('global.save_exit') }}</span>
                        </button>
                        <button type="reset" class="btn btn-default w-icon" title="{{ __('global.reset') }}">
                            <i class="fi fi-rr-refresh"></i>
                            <span>{{ __('global.reset') }}</span>
                        </button>
                    </div>
                </div>

                @if (!isset($data['user']) || isset($data['user']))
                <div class="table-responsive" id="permissions" style="overflow: scroll; height: 400px;">
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
                                            <input class="custom-control-input check-parent" id="parent{{ $item['id'] }}" type="checkbox" data-id="{{ $item['id'] }}" name="permissions[]" value="{{ $item['id'] }}"
                                                {{ isset($data['permission_ids']) ? (in_array($item['id'], $data['permission_ids']) ? 'checked' : '') : '' }}>
                                            <label class="custom-control-label" for="parent{{ $item['id'] }}"></label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @php
                                if (Auth::user()->hasRole('developer')) {
                                    $childs = $item->where('parent', $item['id'])->get();
                                } else {
                                    $childs = Auth::user()['roles'][0]['permissions']->where('parent', $item['id']);
                                }
                            @endphp
                            @foreach ($childs as $child)
                            @php
                                $parentName = substr_replace($item['name'], '', -1);
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td></td>
                                <td>
                                    <i>{!! Str::replace('_', ' ', Str::upper($child['name'])) !!}</i>
                                </td>
                                <td class="text-center">
                                    <div class="form-group m-0">
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input check-child-{{ $child['parent'] }}" id="child{{ $child['id'] }}" type="checkbox" name="permissions[]" value="{{ $child['id'] }}"
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
                @endif
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/backend/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('assets/backend/js/ui_tooltips.js') }}"></script>
@endsection

@section('jsbody')
<script>
    //select2
    $(function () {
        $('.select2').select2();
    });

    // show & hide password
    $(".toggle-password, .toggle-password-confirmation").click(function() {

        $(this).toggleClass("fi-rr-eye fi-rr-eye-crossed");

        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });

    //permissions
    @if (isset($data['user']) && $data['user']['roles'][0]['level'] == 5)
        $('#permissions').show();
    @else
        $('#permissions').hide();
    @endif

    $('#roles').on('change', function() {
        var val = $(this).val();
        if (val == 'editor') {
            $('#permissions').show();
        } else {
            $('#permissions').hide();
        }
    });

    //checked child
    $('.check-parent').click(function () {
        var parent = $(this).attr('data-id');
        $('.check-child-' + parent).not(this).prop('checked', this.checked);
    });

</script>

@if(!Auth::user()->hasRole('developer|super'))
<script>
    $('.hide-form').hide();
</script>
@endif
@endsection
