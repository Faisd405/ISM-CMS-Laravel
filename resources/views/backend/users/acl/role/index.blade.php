@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/sweetalert2/sweetalert2.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-12 col-lg-12 col-md-12">

        <!-- Table Defaults -->
        <div class="card">
            <div class="card-header">
                <h5 class="my-2">
                    @lang('module/user.role.text')
                </h5>
                <div class="box-btn">
                    <a href="{{ route('role.create', $queryParam) }}" class="btn btn-main w-icon" title="@lang('global.add_attr_new', [
                        'attribute' => __('module/user.role.caption')
                        ])">
                        <i class="fi fi-rr-add"></i>
                        <span>@lang('module/user.role.caption')</span>
                    </a>
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
                            <th>@lang('module/user.role.label.name')</th>
                            <th>@lang('module/user.role.label.code')</th>
                            <th>@lang('module/user.role.label.guard_name')</th>
                            <th style="width: 80px;" class="text-center">@lang('module/user.role.label.level')</th>
                            <th style="width: 120px;" class="text-center">@lang('module/user.role.label.role_register')</th>
                            <th style="width: 230px;">@lang('global.created')</th>
                            <th style="width: 230px;">@lang('global.updated')</th>
                            <th class="text-center" style="width: 100px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['roles'] as $item)
                        <tr>
                            <td>{{ $data['no']++ }}</td>
                            <td><strong>{{ Str::replace('_', ' ', Str::upper($item['name'])) }}</strong></td>
                            <td><code>{{ $item['name'] }}</code></td>
                            <td>{{ $item['guard_name'] }}</td>
                            <td class="text-center"><span class="badge badge-info">{{ $item['level'] }}</span></td>
                            <td class="text-center">
                                <span class="badge badge-{{ $item['is_register'] == 1 ? 'main' : 'secondary' }}">{{ __('global.label.optional.'.$item['is_register']) }}</span>
                            </td>
                            <td>{{ $item['created_at']->format('d F Y (H:i A)') }}</td>
                            <td>{{ $item['updated_at']->format('d F Y (H:i A)') }}</td>
                            <td>
                                <div class="box-btn flex-wrap justify-content-end">
                                    <a href="{{ route('role.edit', array_merge(['id' => $item['id']], $queryParam)) }}" class="btn icon-btn btn-sm btn-success" 
                                        data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="@lang('global.edit_attr', [
                                            'attribute' => __('module/user.role.caption')
                                        ])">
                                        <i class="fi fi-rr-pencil"></i>
                                    </a>
                                    @if ($item['locked'] == 0)
                                    <button type="button" class="btn icon-btn btn-sm btn-danger swal-delete" 
                                        data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="@lang('global.delete_attr', [
                                                'attribute' => __('module/user.role.caption')
                                            ])"
                                        data-id="{{ $item['id'] }}">
                                        <i class="fi fi-rr-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" align="center">
                                <i>
                                    <strong class="text-muted">
                                    @if ($totalQueryParam)
                                        ! @lang('global.data_attr_not_found', [
                                            'attribute' => __('module/user.role.caption')
                                        ]) !
                                    @else
                                        ! @lang('global.data_attr_empty', [
                                            'attribute' => __('module/user.role.caption')
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
            @if ($data['roles']->total() > 0)
            <div class="card-footer justify-content-center justify-content-lg-between align-items-center flex-wrap">
                <div class="text-muted mb-3 m-lg-0">
                    @lang('pagination.showing') 
                    <strong>{{ $data['roles']->firstItem() }}</strong> - 
                    <strong>{{ $data['roles']->lastItem() }}</strong> 
                    @lang('pagination.of')
                    <strong>{{ $data['roles']->total() }}</strong>
                </div>
                {{ $data['roles']->onEachSide(1)->links() }}
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
                        url: '/admin/acl/role/' + id,
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
                        text: "@lang('global.alert.delete_success', ['attribute' => __('module/user.role.caption')])"
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