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
                    @lang('global.trash')
                </h5>
                <div class="box-btn">
                    <button type="button" class="btn btn-default w-icon" data-toggle="modal"
                        data-target="#modals-slide" title="@lang('global.filter')">
                        <i class="fi fi-rr-filter"></i>
                        <span>@lang('global.filter')</span>
                    </button>
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
                                    <label class="form-label">@lang('global.type')</label>
                                    <select class="form-control" name="type">
                                        <option value=" " selected>@lang('global.show_all')</option>
                                        @foreach (config('cms.module.feature.registration.type') as $key => $val)
                                        <option value="{{ $key }}" {{ Request::get('type') == ''.$key.'' ? 'selected' : '' }} 
                                            title="{{ $val }}">{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">@lang('global.status')</label>
                                    <select class="form-control" name="status">
                                        <option value=" " selected>@lang('global.show_all')</option>
                                        @foreach (__('global.label.active') as $key => $val)
                                        <option value="{{ $key }}" {{ Request::get('status') == ''.$key.'' ? 'selected' : '' }} 
                                            title="{{ $val }}">{{ $val }}</option>
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
                            <th>@lang('feature/registration.label.name')</th>
                            <th>@lang('module/user.role.caption')</th>
                            <th style="width: 230px;">@lang('feature/registration.label.start_date')</th>
                            <th style="width: 230px;">@lang('feature/registration.label.end_date')</th>
                            <th style="width: 120px;">@lang('global.type')</th>
                            <th style="width: 80px;" class="text-center">@lang('global.status')</th>
                            <th style="width: 230px;">@lang('global.deleted')</th>
                            <th class="text-center" style="width: 100px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['registrations'] as $item)
                        <tr>
                            <td>{{ $data['no']++ }}</td>
                            <td><strong>{{ $item['name'] }}</strong></td>
                            <td>
                                @if ($item['roles'] != null)
                                    <ul>
                                        @foreach ($item['role_list'] as $role)
                                            <li>{{ $role['name'] }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    @lang('global.field_empty_attr', [
                                        'attribute' => __('module/user.role.caption')
                                    ])
                                @endif
                            </td>
                            <td>
                                @if ($item['start_date'] != null)
                                    {{ $item['start_date']->format('d F Y (H:i A)') }}
                                @else
                                @lang('global.field_empty_attr', [
                                    'attribute' => __('feature/registration.label.start_date')
                                ])
                                @endif
                            </td>
                            <td>
                                @if ($item['end_date'] != null)
                                    {{ $item['end_date']->format('d F Y (H:i A)') }}
                                @else
                                @lang('global.field_empty_attr', [
                                    'attribute' => __('feature/registration.label.end_date')
                                ])
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-main">{{ config('cms.module.feature.registration.type.'.$item['type']) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-{{ $item['active'] == 1 ? 'success' : 'secondary' }}">{{ __('global.label.active.'.$item['active']) }}</span>
                            </td>
                            <td>
                                {{ $item['deleted_at']->format('d F Y (H:i A)') }}
                                @if (!empty($item['deleted_by']))
                                <br>
                                <span class="text-muted"> @lang('global.by') : {{ $item['deleteBy'] != null ? $item['deleteBy']['name'] : 'User Deleted' }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="box-btn flex-wrap justify-content-end">
                                    <button type="button" class="btn icon-btn btn-sm btn-success restore" onclick="$(this).find('#form-restore').submit();" 
                                        data-id="{{ $item['id'] }}"    
                                        data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="@lang('global.restore')">
                                        <i class="fi fi-rr-time-past"></i>
                                        <form action="{{ route('registration.restore', ['id' => $item['id']])}}" method="POST" id="form-restore-{{ $item['id'] }}">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                    </button>
                                    <button type="button" class="btn icon-btn btn-sm btn-danger swal-delete" 
                                        data-id="{{ $item['id'] }}"
                                        data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="@lang('global.delete')">
                                        <i class="fi fi-rr-ban"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" align="center">
                                <i>
                                    <strong style="color:red;">
                                    @if ($totalQueryParam > 0)
                                    ! @lang('global.data_attr_not_found', [
                                        'attribute' => __('global.trash')
                                    ]) !
                                    @else
                                    ! @lang('global.data_attr_empty', [
                                        'attribute' => __('global.trash')
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
            @if ($data['registrations']->total() > 0)
            <div class="card-footer justify-content-center justify-content-lg-between align-items-center flex-wrap">
                <div class="text-muted mb-3 m-lg-0">
                    @lang('pagination.showing') 
                    <strong>{{ $data['registrations']->firstItem() }}</strong> - 
                    <strong>{{ $data['registrations']->lastItem() }}</strong> 
                    @lang('pagination.of')
                    <strong>{{ $data['registrations']->total() }}</strong>
                </div>
                {{ $data['registrations']->onEachSide(1)->links() }}
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
            var id = $(this).attr('data-id');
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
                        url: '/admin/registration/' + id + '/permanent?is_trash=yes',
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
                        text: "{{ __('global.alert.delete_success', ['attribute' => __('feature/registration.caption')]) }}"
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

    //restore
    $('.restore').click(function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var url = $(this).attr('href');
        Swal.fire({
        title: "@lang('global.alert.delete_confirm_restore_title')",
        text: "@lang('global.alert.delete_confirm_text')",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "@lang('global.restore')",
        cancelButtonText: "@lang('global.cancel')",
        customClass: {
            confirmButton: "btn btn-success btn-lg",
            cancelButton: "btn btn-secondary btn-lg"
        },
        }).then((result) => {
        if (result.value) {
            $("#form-restore-" + id).submit();
        }
        })
    });
</script>
@endsection