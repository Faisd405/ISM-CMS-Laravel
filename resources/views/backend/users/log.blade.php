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
                <div class="d-flex w-100 w-xl-auto">
                    @role ('developer|super')
                    <button type="button" class="btn btn-danger icon-btn-only-sm btn-sm" onclick="$(this).find('#form-reset').submit();"
                        title="Reset @lang('module/user.log.caption')">
                        <i class="las la-redo-alt"></i> Reset @lang('module/user.log.caption')
                        <form action="{{ route('user.log.reset', $queryParam) }}" method="POST" id="form-reset">
                            @csrf
                            @method('DELETE')
                        </form>
                    </button>
                    @endrole
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
                                <label class="form-label">@lang('global.event')</label>
                                <select class="custom-select" name="event">
                                    <option value=" " selected>@lang('global.show_all')</option>
                                    @foreach (__('global.label.event_log') as $key => $val)
                                    <option value="{{ $key }}" {{ Request::get('event') == ''.$key.'' ? 'selected' : '' }} 
                                        title="{{ $val['title'] }}">{{ $val['title'] }}</option>
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
                <h5 class="card-header-title mt-1 mb-0">@lang('module/user.log.text')</h5>
            </div>

            <div class="table-responsive">
                <table class="table card-table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 10px;">#</th>
                            <th style="width: 140px;">@lang('module/user.log.label.field1')</th>
                            <th>User</th>
                            <th style="width: 120px;">@lang('module/user.log.label.field2')</th>
                            <th>@lang('module/user.log.label.field3')</th>
                            <th style="width: 230px;">@lang('module/user.log.label.field4')</th>
                            @role ('developer|super')
                            <th style="width: 80px;" class="text-center">@lang('global.action')</th>
                            @endrole
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['logs'] as $item)
                        <tr>
                            <td>{{ $data['no']++ }}</td>
                            <td><strong>{{ $item['ip_address'] }}</strong></td>
                            <td>
                                {{ $item['user_id'] != null ? $item['user']['name'] : 'User Deleted' }} 
                                <i>{{ $item['user_id'] == Auth::user()['id'] ? '('.__('global.you').')' : '' }}</i>
                            </td>
                            <td>
                                @switch($item['event'])
                                    @case(1)
                                        <span class="badge badge-success">{{ __('global.label.event_log.'.$item['event'].'.title') }}</span>
                                        @break
                                    @case(2)
                                        <span class="badge badge-primary">{{ __('global.label.event_log.'.$item['event'].'.title') }}</span>
                                        @break
                                    @default
                                        <span class="badge badge-danger">{{ __('global.label.event_log.'.$item['event'].'.title') }}</span>
                                @endswitch
                            </td>
                            <td>
                                {{ __('global.label.event_log.'.$item['event'].'.desc') }} <strong>'{{ $item['log_type'] }}'</strong>
                                <span class="badge badge-secondary">ID : {{ $item['logable_id'] }}</span>
                            </td>
                            <td>{{ $item['created_at']->format('d F Y (H:i A)') }}</td>
                            @role ('developer|super')
                            <td class="text-center">
                                <button type="button" data-id="{{ $item['id'] }}" class="btn icon-btn btn-sm btn-danger swal-delete" title="@lang('global.delete_attr', [
                                    'attribute' => __('module/user.log.caption')
                                    ])">
                                    <i class="las la-trash-alt"></i>
                                </button>
                            </td>
                            @endrole
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" align="center">
                                <i>
                                    <strong style="color:red;">
                                    @if ($totalQueryParam > 0)
                                        ! @lang('global.data_attr_not_found', [
                                            'attribute' => __('module/user.log.caption')
                                        ]) !
                                    @else
                                        ! @lang('global.data_attr_empty', [
                                            'attribute' => __('module/user.log.caption')
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
                            @lang('pagination.showing') : <strong>{{ $data['logs']->firstItem() }}</strong> - <strong>{{ $data['logs']->lastItem() }}</strong> @lang('pagination.of')
                            <strong>{{ $data['logs']->total() }}</strong>
                        </div>
                        <div class="col-lg-6 m--align-right">
                            {{ $data['logs']->onEachSide(1)->links() }}
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
                        url: '/admin/user/delete/'+id+'/log',
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
                        text: "@lang('global.alert.delete_success', ['attribute' => __('module/user.log.caption')])"
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
</script>
@endsection