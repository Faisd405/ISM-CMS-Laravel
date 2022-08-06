@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/sweetalert2/sweetalert2.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-12 col-lg-12 col-md-12">

        <div class="card">
            <div class="card-header">
                <h5 class="my-2">
                    @lang('module/user.login_failed.text')
                </h5>
                <div class="box-btn">
                    <button type="button" class="btn btn-default w-icon" data-toggle="modal"
                        data-target="#modals-slide" title="@lang('global.filter')">
                        <i class="fi fi-rr-filter"></i>
                        <span>@lang('global.filter')</span>
                    </button>
                    @role('developer|super')
                    <button type="button" class="btn btn-danger w-icon" onclick="$(this).find('#form-reset').submit();"
                        title="Reset @lang('module/user.login_failed.caption')">
                        <i class="fi fi-rr-refresh"></i> <span>Reset @lang('module/user.login_failed.caption')</span>
                        <form action="{{ route('user.login-failed.reset', $queryParam) }}" method="POST" id="form-reset">
                            @csrf
                            @method('DELETE')
                        </form>
                    </button>
                    @endrole
                </div>
                <!-- Modal Filter -->
                <div class="modal modal-slide fade" id="modals-slide">
                    <div class="modal-dialog">
                        <form class="modal-content pb-0" action="" method="GET">
                            <button type="button" class="close" data-dismiss="modal"
                                aria-label="Close"><i class="fi fi-rr-cross-small"></i></button>
                            <div class="modal-body mt-3">
                                <div class="form-group">
                                    <label class="form-label" for="limit">@lang('global.limit')</label>
                                    <select id="limit" class="form-control" name="limit" data-style="btn-default">
                                        @foreach (config('cms.setting.limit') as $key => $val)
                                        <option value="{{ $key }}" {{ Request::get('limit') == ''.$key.'' ? 'selected' : '' }} 
                                            title="@lang('global.limit') {{ $val }}">
                                            {{ $val }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">@lang('module/user.login_failed.label.login_type')</label>
                                    <select class="form-control" name="user_type">
                                        <option value=" " selected>@lang('global.show_all')</option>
                                        @foreach (__('global.label.login_failed_type') as $key => $val)
                                        <option value="{{ $key }}" {{ Request::get('user_type') == ''.$key.'' ? 'selected' : '' }} 
                                            title="{{ $val }}">{{ Str::upper($val) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="" for="search-filter">@lang('global.search')</label>
                                    <input id="search-filter" type="text" class="form-control" name="q" value="{{ Request::get('q') }}" 
                                        placeholder="@lang('global.search_keyword')">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="box-btn justify-content-between w-100 m-0">
                                    @if ($totalQueryParam > 0)
                                    <a href="{{ url()->current() }}" class="btn btn-default w-100 text-bolder">Clear @lang('global.filter')</a>
                                    @endif
                                    <button type="submit" class="btn btn-main w-100">@lang('global.filter')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px;">#</th>
                            <th>@lang('module/user.login_failed.label.ip_address')</th>
                            <th>@lang('module/user.login_failed.label.username')</th>
                            <th>@lang('module/user.login_failed.label.password')</th>
                            <th style="width: 120px;">@lang('module/user.login_failed.label.login_type')</th>
                            <th style="width: 230px;">@lang('module/user.login_failed.label.date')</th>
                            <th style="width: 80px;" class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['login_faileds'] as $item)
                        <tr>
                            <td>{{ $data['no']++ }}</td>
                            <td><strong>{{ $item['ip_address'] }}</strong></td>
                            <td>{{ $item['username'] }}</td>
                            <td>{{ $item['password'] }}</td>
                            <td><span class="badge badge-{{ $item['user_type'] == 1 ? 'main' : 'warning' }}">{{ __('global.label.login_failed_type.'.$item['user_type']) }}</span></td>
                            <td>{{ $item['failed_time']->format('d F Y (H:i A)') }}</td>
                            <td>
                                <div class="box-btn flex-wrap justify-content-end">
                                    <button type="button" class="btn icon-btn btn-sm btn-danger swal-delete" 
                                        data-ip="{{ $item['ip_address'] }}"
                                        data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="@lang('global.delete_attr', [
                                        'attribute' => __('module/user.login_failed.caption')
                                        ])">
                                        <i class="las la-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" align="center">
                                <i>
                                    <strong class="text-muted">
                                    @if ($totalQueryParam > 0)
                                    ! @lang('global.data_attr_not_found', [
                                        'attribute' => __('module/user.login_failed.caption')
                                    ]) !
                                    @else
                                    ! @lang('global.data_attr_empty', [
                                        'attribute' => __('module/user.login_failed.caption')
                                    ]) !
                                    @endif
                                    </strong>
                                </i>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($data['login_faileds']->total() > 0)
            <div class="card-footer justify-content-center justify-content-lg-between align-items-center flex-wrap">
                <div class="text-muted mb-3 m-lg-0">
                    @lang('pagination.showing') 
                    <strong>{{ $data['login_faileds']->firstItem() }}</strong> - 
                    <strong>{{ $data['login_faileds']->lastItem() }}</strong> 
                    @lang('pagination.of')
                    <strong>{{ $data['login_faileds']->total() }}</strong>
                </div>
                {{ $data['login_faileds']->onEachSide(1)->links() }}
            </div>
            @endif
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/backend/js/ui_tooltips.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('jsbody')
<script>
    //delete
    $(document).ready(function () {
        $('.swal-delete').on('click', function () {
            var ip = $(this).attr('data-ip');
            Swal.fire({
                title: "@lang('global.alert.delete_confirm_title')",
                text: "@lang('global.alert.delete_confirm_text')",
                icon: "warning",
                confirmButtonText: "@lang('global.alert.delete_btn_yes')",
                customClass: {
                    confirmButton: "btn btn-danger btn-lg",
                    cancelButton: "btn btn-secondary btn-lg"
                },
                showLoaderOnConfirm: true,
                showCancelButton: true,
                allowOutsideClick: () => !Swal.isLoading(),
                cancelButtonText: "@lang('global.alert.delete_btn_cancel')",
                preConfirm: () => {
                    return $.ajax({
                        url: '/admin/user/delete/'+ip+'/login-failed',
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json'
                    }).then(response => {
                        if (!response.success) {
                            return new Error(response.message);
                        }
                        return response;
                    }).catch(error => {
                        swal({
                            icon: 'error',
                            text: 'Error while deleting data. Error Message: ' + error
                        })
                    });
                }
            }).then(response => {
                if (response.value.success) {
                    Swal.fire({
                        icon: 'success',
                        text: "@lang('global.alert.delete_success', ['attribute' => __('module/user.login_failed.caption')])"
                    }).then(() => {
                        window.location.reload();
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        text: response.value.message
                    }).then(() => {
                        window.location.reload();
                    })
                }
            });
        });
    });
</script>
@endsection
