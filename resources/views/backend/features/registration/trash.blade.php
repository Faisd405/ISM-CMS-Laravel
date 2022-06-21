@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/sweetalert2/sweetalert2.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-12 col-lg-12 col-md-12">

        {{-- Filter --}}
        <div class="card">
            <div class="card-body d-flex flex-wrap justify-content-between">
                <div class="d-flex w-100 w-xl-auto">
                    <button type="button" class="btn btn-dark icon-btn-only-sm btn-sm mr-2" title="@lang('global.filter')" id="filter-btn">
                        <i class="las la-filter"></i> <span>@lang('global.filter')</span>
                    </button>
                    @if ($totalQueryParam > 0)
                    <a href="{{ url()->current() }}" class="btn btn-warning icon-btn-only-sm btn-sm" title="Clear @lang('global.filter')">
                        <i class="las la-redo-alt"></i> <span>Clear @lang('global.filter')</span>
                    </a>
                    @endif
                </div>
            </div>
            <hr class="m-0">
            <div class="card-body" id="{{ $totalQueryParam == 0 ? 'filter-form' : '' }}">
                <form action="" method="GET">
                    <div class="form-row align-items-center">
                        <div class="col-md-1">
                            <div class="form-group">
                                <label class="form-label">@lang('global.limit')</label>
                                <select class="custom-select" name="limit">
                                    @foreach (config('cms.setting.limit') as $key => $val)
                                    <option value="{{ $key }}" {{ Request::get('limit') == ''.$key.'' ? 'selected' : '' }} 
                                        title="@lang('global.limit') {{ $val }}">{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">@lang('global.type')</label>
                                <select class="custom-select" name="type">
                                    <option value=" " selected>@lang('global.show_all')</option>
                                    @foreach (__('feature/registration.type') as $key => $val)
                                    <option value="{{ $key }}" {{ Request::get('type') == ''.$key.'' ? 'selected' : '' }} 
                                        title="{{ $val }}">{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">@lang('global.status')</label>
                                <select class="custom-select" name="status">
                                    <option value=" " selected>@lang('global.show_all')</option>
                                    @foreach (__('global.label.active') as $key => $val)
                                    <option value="{{ $key }}" {{ Request::get('status') == ''.$key.'' ? 'selected' : '' }} 
                                        title="{{ $val }}">{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="form-group">
                                <label class="form-label">@lang('global.search')</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="q" value="{{ Request::get('q') }}" placeholder="@lang('global.search_keyword')">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-dark" title="@lang('global.search')"><i class="las la-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header with-elements">
                <h5 class="card-header-title mt-1 mb-0">@lang('global.trash')</h5>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table card-table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 10px;">#</th>
                            <th>@lang('feature/registration.label.field1')</th>
                            <th>@lang('feature/registration.label.field2')</th>
                            <th style="width: 230px;">@lang('feature/registration.label.field3')</th>
                            <th style="width: 230px;">@lang('feature/registration.label.field4')</th>
                            <th style="width: 120px;">@lang('global.type')</th>
                            <th style="width: 80px;" class="text-center">@lang('global.status')</th>
                            <th style="width: 230px;">@lang('global.deleted')</th>
                            <th class="text-center" style="width: 110px;"></th>
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
                                        'attribute' => __('feature/registration.label.field2')
                                    ])
                                @endif
                            </td>
                            <td>
                                @if ($item['start_date'] != null)
                                    {{ $item['start_date']->format('d F Y (H:i A)') }}
                                @else
                                @lang('global.field_empty_attr', [
                                    'attribute' => __('feature/registration.label.field3')
                                ])
                                @endif
                            </td>
                            <td>
                                @if ($item['end_date'] != null)
                                    {{ $item['end_date']->format('d F Y (H:i A)') }}
                                @else
                                @lang('global.field_empty_attr', [
                                    'attribute' => __('feature/registration.label.field4')
                                ])
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-primary">{{ __('feature/registration.type.'.$item['type']) }}</span>
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
                            <td class="text-center">
                                <button type="button" class="btn btn-success icon-btn btn-sm restore" onclick="$(this).find('#form-restore').submit();" title="@lang('global.restore')" data-id="{{ $item['id'] }}">
                                    <i class="las la-trash-restore-alt"></i>
                                    <form action="{{ route('registration.restore', ['id' => $item['id']])}}" method="POST" id="form-restore-{{ $item['id'] }}">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                </button>
                                <button type="button" class="btn btn-danger icon-btn btn-sm swal-delete" data-id="{{ $item['id'] }}" title="@lang('global.delete')">
                                    <i class="las la-ban"></i>
                                </button>
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
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-lg-6 m--valign-middle">
                            @lang('pagination.showing') : <strong>{{ $data['registrations']->firstItem() }}</strong> - <strong>{{ $data['registrations']->lastItem() }}</strong> @lang('pagination.of')
                            <strong>{{ $data['registrations']->total() }}</strong>
                        </div>
                        <div class="col-lg-6 m--align-right">
                            {{ $data['registrations']->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
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
                type: "warning",
                confirmButtonText: "@lang('global.alert.delete_btn_yes')",
                customClass: {
                    confirmButton: "btn btn-danger btn-lg",
                    cancelButton: "btn btn-primary btn-lg"
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
                            type: 'error',
                            text: 'Error while deleting data. Error Message: ' + error
                        })
                    });
                }
            }).then(response => {
                if (response.value.success) {
                    Swal.fire({
                        type: 'success',
                        text: "{{ __('global.alert.delete_success', ['attribute' => __('feature/registration.caption')]) }}"
                    }).then(() => {
                        window.location.reload();
                    })
                } else {
                    Swal.fire({
                        type: 'error',
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
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "@lang('global.restore')",
        cancelButtonText: "@lang('global.cancel')",
        }).then((result) => {
        if (result.value) {
            $("#form-restore-" + id).submit();
        }
        })
    });
</script>
@endsection