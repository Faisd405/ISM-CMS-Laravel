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
                    @lang('module/menu.text')
                </h5>
                <div class="box-btn">
                    @role('developer|super')
                    <a href="{{ route('menu.create', array_merge(['categoryId' => $data['category']['id']], $queryParam)) }}" class="btn btn-main w-icon" title="@lang('global.add_attr_new', [
                        'attribute' => __('module/menu.caption')
                        ])">
                        <i class="fi fi-rr-add"></i>
                        <span>@lang('module/menu.caption')</span>
                    </a>
                    @endrole
                    <button type="button" class="btn btn-default w-icon" data-toggle="modal"
                        data-target="#modals-slide" title="@lang('global.filter')">
                        <i class="fi fi-rr-filter"></i>
                        <span>@lang('global.filter')</span>
                    </button>
                    @role('developer|super')
                    <a href="{{ route('menu.trash', ['categoryId' => $data['category']['id']]) }}" class="btn btn-dark w-icon" title="@lang('global.trash')">
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
            <hr class="border-light m-0">
            <div class="card-header">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <span>{{ Str::upper(__('module/menu.category.caption')) }}</span>
                    </li>
                    <li class="breadcrumb-item active">
                        <b class="text-main">{{ $data['category']['name'] }}</b>
                    </li>
                </ol>
            </div>
            <hr class="border-light m-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 10px;">#</th>
                            <th>@lang('module/menu.label.title')</th>
                            <th class="text-center" style="width: 100px;">@lang('global.status')</th>
                            <th style="width: 230px;">@lang('global.created')</th>
                            <th style="width: 230px;">@lang('global.updated')</th>
                            <th class="text-center" style="width: 110px;"></th>
                            <th class="text-center" style="width: 180px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['menus'] as $item)
                        <tr class="table-secondary">
                            <td>{{ $data['no']++ }}</td>
                            <td>
                                <strong>{!! Str::limit($item['module_data']['title'], 65) !!}</strong>
                                @if ($item['approved'] != 1)
                                <br>
                                <small class="form-text text-danger">@lang('global.approval_info')</small>
                                @endif
                            </td>
                            <td class="text-center">
                                @can ('menu_update')
                                <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="badge badge-{{ $item['publish'] == 1 ? 'main' : 'warning' }}"
                                    title="@lang('global.status')">
                                    {{ __('global.label.publish.'.$item['publish']) }}
                                    <form action="{{ route('menu.publish', ['categoryId' => $item['menu_category_id'], 'id' => $item['id']]) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                </a>
                                @else
                                <span class="badge badge-{{ $item['publish'] == 1 ? 'main' : 'warning' }}">{{ __('global.label.publish.'.$item['publish']) }}</span>
                                @endcan
                            </td>
                            <td>
                                {{ $item['created_at']->format('d F Y (H:i A)') }}
                                @if (!empty($item['created_by']))
                                    <br>
                                   <span class="text-muted">@lang('global.by') : {{ $item['createBy'] != null ? $item['createBy']['name'] : 'User Deleted' }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $item['updated_at']->format('d F Y (H:i A)') }}
                                @if (!empty($item['updated_by']))
                                    <br>
                                    <span class="text-muted">@lang('global.by') : {{ $item['updateBy'] != null ? $item['updateBy']['name'] : 'User Deleted' }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="box-btn flex-wrap justify-content-center">
                                    @if (Auth::user()->can('menu_update') && $item->where('menu_category_id', $item['menu_category_id'])->where('parent', $item['parent'])->min('position') != $item['position'])
                                    <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn icon-btn btn-sm btn-dark" title="@lang('global.position')">
                                        <i class="fi fi-rr-arrow-small-up"></i>
                                        <form action="{{ route('menu.position', ['categoryId' => $item['menu_category_id'], 'id' => $item['id'], 'position' => ($item['position'] - 1)]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                    </a>
                                    @else
                                    <button type="button" class="btn icon-btn btn-sm btn-secondary" title="@lang('global.position')" disabled>
                                        <i class="fi fi-rr-arrow-small-up"></i>
                                    </button>
                                    @endif
                                    @if (Auth::user()->can('menu_update') && $item->where('menu_category_id', $item['menu_category_id'])->where('parent', $item['parent'])->max('position') != $item['position'])
                                    <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn icon-btn btn-sm btn-dark" title="@lang('global.position')">
                                        <i class="fi fi-rr-arrow-small-down"></i>
                                        <form action="{{ route('menu.position', ['categoryId' => $item['menu_category_id'], 'id' => $item['id'], 'position' => ($item['position'] + 1)]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                    </a>
                                    @else
                                    <button type="button" class="btn icon-btn btn-sm btn-secondary" title="@lang('global.position')" disabled>
                                        <i class="fi fi-rr-arrow-small-down"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="box-btn flex-wrap justify-content-end">
                                    @can('menu_create')
                                        @if (Auth::user()->hasRole('developer|super') || !Auth::user()->hasRole('developer|super') && $item['config']['create_child'] == true)  
                                        <a href="{{ route('menu.create', array_merge(['categoryId' => $item['menu_category_id'], 'parent' => $item['id']], $queryParam)) }}" class="btn icon-btn btn-sm btn-main" title="@lang('global.add_attr_new', [
                                            'attribute' => __('module/menu.caption')
                                        ])">
                                            <i class="fi fi-rr-add"></i>
                                        </a>
                                        @endif
                                    @endcan
                                    @can('menu_update')
                                    @if (Auth::user()->hasRole('developer|super') || !Auth::user()->hasRole('developer|super') && $item['config']['edit_public_menu'] == true)
                                    <a href="{{ route('menu.edit', array_merge(['categoryId' => $item['menu_category_id'], 'id' => $item['id']], $queryParam)) }}" class="btn icon-btn btn-sm btn-success" title="@lang('global.edit_attr', [
                                        'attribute' => __('module/menu.caption')
                                    ])">
                                        <i class="fi fi-rr-pencil"></i>
                                    </a>
                                    @endif
                                    @endcan
                                    @can('menu_delete')
                                        @if ($item['locked'] == 0)
                                        <button type="button" class="btn btn-danger icon-btn btn-sm swal-delete" title="@lang('global.delete_attr', [
                                            'attribute' => __('module/menu.caption')
                                            ])"
                                            data-category-id="{{ $item['menu_category_id'] }}"
                                            data-id="{{ $item['id'] }}">
                                            <i class="fi fi-rr-trash"></i>
                                        </button>
                                        @endif
                                    @endcan
                                    @if (Auth::user()->hasRole('developer|super') && config('cms.module.menu.approval') == true)
                                    <a href="javascript:void(0);" onclick="$(this).find('#form-approval').submit();" class="btn icon-btn btn-sm btn-default" 
                                            title="{{ $item['approved'] == 1 ? __('global.label.flags.0') : __('global.label.flags.1')}}">
                                            <i class="fi fi-rr-{{ $item['approved'] == 1 ? 'ban text-danger' : 'check text-success' }}"></i>
                                        <form action="{{ route('menu.approved', ['categoryId' => $item['menu_category_id'], 'id' => $item['id']]) }}" method="POST" id="form-approval">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @if ($item['childs']->count() > 0)
                            @include('backend.menus.child', ['childs' => $item->childs()->orderBy('position', 'ASC')->get(), 'level' => 1])
                        @endif
                        @empty
                        <tr>
                            <td colspan="7" align="center">
                                <i>
                                    <strong class="text-muted">
                                    @if ($totalQueryParam > 0)
                                    ! @lang('global.data_attr_not_found', [
                                        'attribute' => __('module/menu.caption')
                                    ]) !
                                    @else
                                    ! @lang('global.data_attr_empty', [
                                        'attribute' => __('module/menu.caption')
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
            @if ($data['menus']->total() > 0)
            <div class="card-footer justify-content-center justify-content-lg-between align-items-center flex-wrap">
                <div class="text-muted mb-3 m-lg-0">
                    @lang('pagination.showing') 
                    <strong>{{ $data['menus']->firstItem() }}</strong> - 
                    <strong>{{ $data['menus']->lastItem() }}</strong> 
                    @lang('pagination.of')
                    <strong>{{ $data['menus']->total() }}</strong>
                </div>
                {{ $data['menus']->onEachSide(1)->links() }}
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
            var categoryId = $(this).attr('data-category-id');
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
                        url: '/admin/menu/'+categoryId+'/'+id+'/soft',
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
                        text: "@lang('global.alert.delete_success', ['attribute' => __('module/menu.caption')])"
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