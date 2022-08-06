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
                    @lang('module/user.permission.text')
                </h5>
                <div class="box-btn">
                    <a href="{{ route('permission.create') }}" class="btn btn-main w-icon" title="@lang('global.add_attr_new', [
                        'attribute' => __('module/user.permission.caption')
                        ])">
                        <i class="fi fi-rr-add"></i>
                        <span>@lang('module/user.permission.caption')</span>
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
                            <th style="width: 400px;">@lang('module/user.permission.label.name')</th>
                            <th>@lang('module/user.permission.label.code')</th>
                            <th>@lang('module/user.permission.label.guard_name')</th>
                            <th style="width: 230px;">@lang('global.created')</th>
                            <th style="width: 230px;">@lang('global.updated')</th>
                            <th class="text-center" style="width: 135px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['permissions'] as $item)
                        <tr>
                            <td><strong>{{ $data['no']++ }}</strong></td>
                            <td>
                                @if ($item->where('parent', $item['id'])->count() > 0)
                                <a href="javascript:;" class="parent" data-id="{{ $item['id'] }}">
                                    <strong>{!! Str::replace('_', ' ', Str::upper($item['name'])) !!}</strong>
                                </a>
                                @else
                                <strong>{!! Str::replace('_', ' ', Str::upper($item['name'])) !!}</strong>
                                @endif
                            </td>
                            <td><code>{{ $item['name'] }}</code></td>
                            <td>{{ $item['guard_name'] }}</td>
                            <td>{{ $item['created_at']->format('d F Y (H:i A)') }}</td>
                            <td>{{ $item['updated_at']->format('d F Y (H:i A)') }}</td>
                            <td>
                                <div class="box-btn flex-wrap justify-content-end">
                                    <a href="{{ route('permission.create', ['parent' => $item['id']]) }}" class="btn icon-btn btn-sm btn-main" 
                                        data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="@lang('global.add_attr_new', [
                                            'attribute' => __('module/user.permission.caption')
                                        ])">
                                        <i class="fi fi-rr-plus"></i>
                                    </a>
                                    <a href="{{ route('permission.edit', ['id' => $item['id']]) }}" class="btn icon-btn btn-sm btn-success" 
                                        data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="@lang('global.edit_attr', [
                                            'attribute' => __('module/user.permission.caption')
                                        ])">
                                        <i class="fi fi-rr-pencil"></i>
                                    </a>
                                    @if ($item['locked'] == 0)
                                    <button type="button" class="btn icon-btn btn-sm btn-danger swal-delete" 
                                        data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="@lang('global.delete_attr', [
                                                'attribute' => __('module/user.permission.caption')
                                            ])"
                                        data-id="{{ $item['id'] }}">
                                        <i class="fi fi-rr-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @foreach ($item->where('parent', $item['id'])->get() as $child)
                        @php
                            $parentName = substr_replace($item['name'], '', -1);
                            $childName = Str::replace($parentName.'_', '', $child['name'])
                        @endphp
                        <tr class="child-{{ $item['id'] }}" style="display: none;">
                            <td>{{ $loop->iteration }}</td>
                            <td> --- {{ Str::replace('_', ' ', Str::upper($childName)) }}</td>
                            <td><code>{{ $child['name'] }}</code></td>
                            <td>{{ $child['guard_name'] }}</td>
                            <td>{{ $child['created_at']->format('d F Y (H:i A)') }}</td>
                            <td>{{ $child['updated_at']->format('d F Y (H:i A)') }}</td>
                            <td>
                                <div class="box-btn flex-wrap justify-content-end">
                                    <a href="{{ route('permission.edit', ['id' => $child['id']]) }}" class="btn icon-btn btn-sm btn-success" 
                                        data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="@lang('global.edit_attr', [
                                            'attribute' => __('module/user.permission.caption')
                                        ])">
                                        <i class="fi fi-rr-pencil"></i>
                                    </a>
                                    @if ($child['locked'] == 0)
                                    <button type="button" class="btn icon-btn btn-sm btn-danger swal-delete" 
                                        data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="@lang('global.delete_attr', [
                                                'attribute' => __('module/user.permission.caption')
                                            ])"
                                        data-id="{{ $child['id'] }}">
                                        <i class="fi fi-rr-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @empty
                        <tr>
                            <td colspan="7" align="center">
                                <i>
                                    <strong class="text-muted">
                                    @if ($totalQueryParam)
                                        ! @lang('global.data_attr_not_found', [
                                            'attribute' => __('module/user.permission.caption')
                                        ]) !
                                    @else
                                        ! @lang('global.data_attr_empty', [
                                            'attribute' => __('module/user.permission.caption')
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
            @if ($data['permissions']->total() > 0)
            <div class="card-footer justify-content-center justify-content-lg-between align-items-center flex-wrap">
                <div class="text-muted mb-3 m-lg-0">
                    @lang('pagination.showing') 
                    <strong>{{ $data['permissions']->firstItem() }}</strong> - 
                    <strong>{{ $data['permissions']->lastItem() }}</strong> 
                    @lang('pagination.of')
                    <strong>{{ $data['permissions']->total() }}</strong>
                </div>
                {{ $data['permissions']->onEachSide(1)->links() }}
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
                        url: '/admin/acl/permission/' + id,
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
                        text: "@lang('global.alert.delete_success', ['attribute' => __('module/user.permission.caption')])"
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

    //hide show
    $(".parent").click(function () {
        var parent = $(this).attr('data-id');
       $('.child-' + parent).toggle();
    });
</script>
@endsection