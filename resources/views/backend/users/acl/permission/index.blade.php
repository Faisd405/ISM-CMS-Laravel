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
                    <a href="{{ route('permission.create') }}" class="btn btn-success icon-btn-only-sm btn-sm" title="@lang('global.add_attr_new', [
                            'attribute' => __('module/user.permission.caption')
                        ])">
                        <i class="las la-plus"></i> <span>@lang('module/user.permission.caption')</span>
                    </a>
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
                <h5 class="card-header-title mt-1 mb-0">@lang('module/user.permission.text')</h5>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table card-table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 10px;">#</th>
                            <th style="width: 400px;">@lang('module/user.permission.label.field2')</th>
                            <th>@lang('module/user.permission.label.field3')</th>
                            <th>@lang('module/user.permission.label.field4')</th>
                            <th style="width: 230px;">@lang('global.created')</th>
                            <th style="width: 230px;">@lang('global.updated')</th>
                            <th class="text-center" style="width: 140px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['permissions'] as $item)
                        <tr class="{{ $item->where('parent', $item['id'])->count() > 0 ? 'table-secondary' : '' }}">
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
                            <td class="text-center">
                                <a href="{{ route('permission.create', ['parent' => $item['id']]) }}" class="btn btn-success icon-btn btn-sm" title="@lang('global.add_attr_new', [
                                        'attribute' => __('module/user.permission.caption')
                                    ])">
                                    <i class="las la-plus"></i>
                                </a>
                                <a href="{{ route('permission.edit', ['id' => $item['id']]) }}" class="btn btn-primary icon-btn btn-sm" title="@lang('lang.edit_attr', [
                                        'attribute' => __('module/user.permission.caption')
                                    ])">
                                    <i class="las la-pen"></i>
                                </a>
                                <button type="button" class="btn btn-danger icon-btn btn-sm swal-delete" title="@lang('lang.delete_attr', [
                                    'attribute' => __('module/user.permission.caption')
                                ])"
                                    data-id="{{ $item['id'] }}">
                                    <i class="las la-trash-alt"></i>
                                </button>
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
                            <td class="text-center">
                                <button type="button" class="btn btn-success icon-btn btn-sm" title="@lang('global.add_attr_new', [
                                    'attribute' => __('module/user.permission.caption')
                                    ])" disabled>
                                    <i class="las la-plus"></i>
                                </button>
                                <a href="{{ route('permission.edit', ['id' => $child['id']]) }}" class="btn btn-primary icon-btn btn-sm" title="@lang('lang.edit_attr', [
                                        'attribute' => __('module/user.permission.caption')
                                    ])">
                                    <i class="las la-pen"></i>
                                </a>
                                <button type="button" class="btn btn-danger icon-btn btn-sm swal-delete" title="@lang('lang.delete_attr', [
                                    'attribute' => __('module/user.permission.caption')
                                ])"
                                    data-id="{{ $child['id'] }}">
                                    <i class="las la-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                        @empty
                        <tr>
                            <td colspan="7" align="center">
                                <i>
                                    <strong style="color:red;">
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
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-lg-6 m--valign-middle">
                            @lang('pagination.showing') : <strong>{{ $data['permissions']->firstItem() }}</strong> - <strong>{{ $data['permissions']->lastItem() }}</strong> @lang('pagination.of')
                            <strong>{{ $data['permissions']->total() }}</strong>
                        </div>
                        <div class="col-lg-6 m--align-right">
                            {{ $data['permissions']->onEachSide(1)->links() }}
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
                            type: 'error',
                            text: 'Error while deleting data. Error Message: ' + error
                        })
                    });
                }
            }).then(response => {
                if (response.value.success) {
                    Swal.fire({
                        type: 'success',
                        text: "@lang('global.alert.delete_success', ['attribute' => __('module/user.permission.caption')])"
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

    //hide show
    $(".parent").click(function () {
        var parent = $(this).attr('data-id');
       $('.child-' + parent).toggle();
    });
</script>
@endsection