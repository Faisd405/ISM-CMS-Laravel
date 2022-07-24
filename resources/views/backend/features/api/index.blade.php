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
                    @can('api_create')
                    <a href="{{ route('api.create', $queryParam) }}" class="btn btn-success icon-btn-only-sm btn-sm mr-2" title="@lang('global.add_attr_new', [
                            'attribute' => __('feature/api.caption')
                        ])">
                        <i class="las la-plus"></i> <span>@lang('feature/api.caption')</span>
                    </a>
                    @endcan
                    @role('developer|super')
                    <a href="{{ route('api.trash') }}" class="btn btn-secondary icon-btn-only-sm btn-sm" title="@lang('global.trash')">
                        <i class="las la-trash"></i> <span>@lang('global.trash')</span>
                    </a>
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
                <h5 class="card-header-title mt-1 mb-0">@lang('feature/api.text')</h5>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table card-table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 10px;">#</th>
                            <th style="width: 100px;" class="text-center">@lang('feature/api.label.field1')</th>
                            <th>@lang('feature/api.label.field2')</th>
                            <th>@lang('feature/api.caption')</th>
                            <th>@lang('feature/api.label.field6')</th>
                            <th>@lang('feature/api.label.field5')</th>
                            <th style="width: 80px;" class="text-center">@lang('global.status')</th>
                            <th style="width: 230px;">@lang('global.created')</th>
                            <th style="width: 230px;">@lang('global.updated')</th>
                            <th class="text-center" style="width: 110px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['apis'] as $item)
                        <tr>
                            <td>{{ $data['no']++ }}</td>
                            <td>{{ $item['name'] }}</td>
                            <td>
                                {{ $item['description'] ?? __('global.field_empty_attr', [
                                    'attribute' => __('feature/api.label.field2')
                                ]) }}
                            </td>
                            <td>
                                @lang('feature/api.label.field3') : <strong>{{ $item['api_key'] }}</strong><br>
                                @lang('feature/api.label.field4') : <strong>{{ $item['api_secret'] }}</strong>
                                @can('api_update')
                                <br>
                                <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="badge badge-primary"
                                    title="@lang('feature/api.label.regenerate')">
                                    <i class="las la-sync"></i> @lang('feature/api.label.regenerate')
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
                                        'attribute' => __('feature/api.label.field6')
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
                                        'attribute' => __('feature/api.label.field5')
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
                            <td class="text-center">
                                @can('api_update')
                                <a href="{{ route('api.edit', array_merge(['id' => $item['id']], $queryParam)) }}" class="btn btn-primary icon-btn btn-sm" title="@lang('global.edit_attr', [
                                        'attribute' => __('feature/api.caption')
                                    ])">
                                    <i class="las la-pen"></i>
                                </a>
                                @endcan
                                @can('api_delete')
                                @if ($item['locked'] == 0)  
                                <button type="button" class="btn btn-danger icon-btn btn-sm swal-delete" title="@lang('global.delete_attr', [
                                        'attribute' => __('feature/api.caption')
                                    ])"
                                    data-id="{{ $item['id'] }}">
                                    <i class="las la-trash-alt"></i>
                                </button>
                                @endif
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" align="center">
                                <i>
                                    <strong style="color:red;">
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
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-lg-6 m--valign-middle">
                            @lang('pagination.showing') : <strong>{{ $data['apis']->firstItem() }}</strong> - <strong>{{ $data['apis']->lastItem() }}</strong> @lang('pagination.of')
                            <strong>{{ $data['apis']->total() }}</strong>
                        </div>
                        <div class="col-lg-6 m--align-right">
                            {{ $data['apis']->onEachSide(1)->links() }}
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
                            type: 'error',
                            text: 'Error while deleting data. Error Message: ' + error
                        })
                    });
                }
            }).then(response => {
                if (response.value.success) {
                    Swal.fire({
                        type: 'success',
                        text: "@lang('global.alert.delete_success', ['attribute' => __('feature/api.caption')])"
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