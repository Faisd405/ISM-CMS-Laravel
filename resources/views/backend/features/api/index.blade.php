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
                    @lang('feature/api.text')
                </h5>
                <div class="box-btn">
                    @can('api_create')
                    <a href="{{ route('api.create', $queryParam) }}" class="btn btn-main w-icon" title="@lang('global.add_attr_new', [
                        'attribute' => __('feature/api.caption')
                        ])">
                        <i class="fi fi-rr-add"></i>
                        <span>@lang('feature/api.caption')</span>
                    </a>
                    @endcan
                    <button type="button" class="btn btn-default w-icon" data-toggle="modal"
                        data-target="#modals-slide" title="@lang('global.filter')">
                        <i class="fi fi-rr-filter"></i>
                        <span>@lang('global.filter')</span>
                    </button>
                    @role('developer|super')
                    <a href="{{ route('api.trash') }}" class="btn btn-dark w-icon" title="@lang('global.trash')">
                        <i class="fi fi-rr-trash"></i> <span>@lang('global.trash')</span>
                    </a>
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
                            <th style="width: 100px;" class="text-center">@lang('feature/api.label.name')</th>
                            <th>@lang('feature/api.label.description')</th>
                            <th>@lang('feature/api.caption')</th>
                            <th>@lang('feature/api.label.ip_address')</th>
                            <th>@lang('feature/api.label.module')</th>
                            <th style="width: 80px;" class="text-center">@lang('global.status')</th>
                            <th style="width: 230px;">@lang('global.created')</th>
                            <th style="width: 230px;">@lang('global.updated')</th>
                            <th class="text-center" style="width: 100px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['apis'] as $item)
                        <tr>
                            <td>{{ $data['no']++ }}</td>
                            <td>{{ $item['name'] }}</td>
                            <td>
                                {{ $item['description'] ?? __('global.field_empty_attr', [
                                    'attribute' => __('feature/api.label.description')
                                ]) }}
                            </td>
                            <td>
                                @lang('feature/api.label.api_key') : <strong>{{ $item['api_key'] }}</strong><br>
                                @lang('feature/api.label.api_secret') : <strong>{{ $item['api_secret'] }}</strong>
                                @can('api_update')
                                <br>
                                <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="badge badge-primary w-icon"
                                    title="@lang('feature/api.label.regenerate')">
                                    <i class="fi fi-rr-refresh"></i> <span>@lang('feature/api.label.regenerate')</span>
                                    <form action="{{ route('api.regenerate', ['id' => $item->id]) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                </a>
                                @endif
                            </td>
                            <td>
                                @if (!empty($item['ip_address']))
                                    @foreach ($item['ip_address'] as $ip)
                                    [{{ $ip }}]
                                    @endforeach
                                @else
                                    {{ __('global.field_empty_attr', [
                                        'attribute' => __('feature/api.label.ip_address')
                                    ]) }}
                                @endif
                            </td>
                            <td>
                                @if (!empty($item['modules']))
                                    @foreach ($item['modules'] as $mod)
                                    [{{ $mod }}]
                                    @endforeach
                                @else
                                    {{ __('global.field_empty_attr', [
                                        'attribute' => __('feature/api.label.module')
                                    ]) }}
                                @endif
                            </td>
                            <td class="text-center">
                                @can('api_update')
                                <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="badge badge-{{ $item['active'] == 1 ? 'success' : 'secondary' }}"
                                    title="{{ __('global.label.active.'.$item['active']) }}">
                                    {{ __('global.label.active.'.$item['active']) }}
                                    <form action="{{ route('api.activate', array_merge(['id' => $item->id], $queryParam)) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                </a>
                                @else
                                <span class="badge badge-{{ $item['active'] == 1 ? 'success' : 'secondary' }}">{{ __('global.label.active.'.$item['active']) }}</span>
                                @endcan
                            </td>
                            <td>
                                {{ $item['created_at']->format('d F Y (H:i A)') }}
                                @if (!empty($item['created_by']))
                                <br>
                                <span class="text-muted"> @lang('global.by') : {{ $item['createBy'] != null ? $item['createBy']['name'] : 'User Deleted' }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $item['updated_at']->format('d F Y (H:i A)') }}
                                @if (!empty($item['updated_by']))
                                <br>
                                <span class="text-muted"> @lang('global.by') : {{ $item['updateBy'] != null ? $item['updateBy']['name'] : 'User Deleted' }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="box-btn flex-wrap justify-content-end">
                                    @can('api_update')
                                    <a href="{{ route('api.edit', array_merge(['id' => $item['id']], $queryParam)) }}" class="btn icon-btn btn-sm btn-success" 
                                        data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="@lang('global.edit_attr', [
                                            'attribute' => __('feature/api.caption')
                                        ])">
                                        <i class="fi fi-rr-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('api_delete')
                                        @if ($item['locked'] == 0)
                                        <button type="button" class="btn icon-btn btn-sm btn-danger swal-delete" 
                                            data-id="{{ $item['id'] }}"
                                            data-toggle="tooltip" data-placement="bottom"
                                            data-original-title="@lang('global.delete_attr', [
                                                    'attribute' => __('feature/api.caption')
                                                ])">
                                            <i class="fi fi-rr-trash"></i>
                                        </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" align="center">
                                <i>
                                    <strong class="text-muted">
                                        @if ($totalQueryParam > 0)
                                            ! @lang('global.data_attr_not_found', [
                                                'attribute' => __('feature/api.caption')
                                            ]) !
                                        @else
                                            ! @lang('global.data_attr_empty', [
                                                'attribute' => __('feature/api.caption')
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
            @if ($data['apis']->total() > 0)
            <div class="card-footer justify-content-center justify-content-lg-between align-items-center flex-wrap">
                <div class="text-muted mb-3 m-lg-0">
                    @lang('pagination.showing') 
                    <strong>{{ $data['apis']->firstItem() }}</strong> - 
                    <strong>{{ $data['apis']->lastItem() }}</strong> 
                    @lang('pagination.of')
                    <strong>{{ $data['apis']->total() }}</strong>
                </div>
                {{ $data['apis']->onEachSide(1)->links() }}
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
                        url: '/admin/api/' + id + '/soft',
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
                        text: "@lang('global.alert.delete_success', ['attribute' => __('feature/api.caption')])"
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