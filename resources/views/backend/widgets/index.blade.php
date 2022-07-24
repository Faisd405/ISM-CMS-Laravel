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
                    @can ('widget_create')
                    <div class="btn-group dropdown mr-2">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle hide-arrow icon-btn-only-sm" 
                            data-toggle="dropdown" title="@lang('global.add_attr_new', [
                                'attribute' => __('module/widget.caption')
                            ])"><i class="las la-plus"></i><span>@lang('module/widget.caption')</span></button>
                        <div class="dropdown-menu dropdown-menu-right">
                            @foreach (config('cms.module.widget.type') as $key => $val)
                            <a href="{{ route('widget.create', array_merge(['type' => $val], $queryParam)) }}" class="dropdown-item" title="{{ Str::replace('_', ' ', Str::upper($val)) }}">
                                <i class="las la-edit"></i>
                                <span>{{ Str::replace('_', ' ', Str::upper($val)) }}</span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endcan
                    @role('developer|super')
                    <a href="{{ route('widget.trash') }}" class="btn btn-secondary icon-btn-only-sm btn-sm" title="@lang('global.trash')">
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
                                <label class="form-label">@lang('module/widget.label.field3')</label>
                                <select class="custom-select" name="widget_set">
                                    <option value=" " selected>@lang('global.show_all')</option>
                                    @foreach (config('cms.module.widget.set') as $key => $val)
                                    <option value="{{ $key }}" {{ Request::get('widget_set') == ''.$key.'' ? 'selected' : '' }} 
                                        title="{{ $val }}">{{ Str::upper($val) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">@lang('global.type')</label>
                                <select class="custom-select" name="widget_type">
                                    <option value=" " selected>@lang('global.show_all')</option>
                                    @foreach (config('cms.module.widget.type') as $key => $val)
                                    <option value="{{ $key }}" {{ Request::get('widget_type') == ''.$key.'' ? 'selected' : '' }} 
                                        title="{{ $val }}">{{ Str::replace('_', ' ', Str::upper($val)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">@lang('global.status')</label>
                                <select class="custom-select" name="publish">
                                    <option value=" " selected>@lang('global.show_all')</option>
                                    @foreach (__('global.label.publish') as $key => $val)
                                    <option value="{{ $key }}" {{ Request::get('publish') == ''.$key.'' ? 'selected' : '' }} 
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
                <h5 class="card-header-title mt-1 mb-0">@lang('module/widget.text')</h5>
            </div>

            <div class="table-responsive">
                <table class="table card-table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 10px;">#</th>
                            <th>@lang('module/widget.label.field1')</th>
                            <th style="width: 120px;">@lang('module/widget.label.field4')</th>
                            <th style="width: 120px;">@lang('global.type')</th>
                            <th class="text-center" style="width: 100px;">@lang('global.status')</th>
                            <th style="width: 230px;">@lang('global.created')</th>
                            <th style="width: 230px;">@lang('global.updated')</th>
                            <th class="text-center" style="width: 110px;"></th>
                            <th class="text-center" style="width: 210px;"></th>
                        </tr>
                    </thead>
                    <tbody class="{{ $data['widgets']->total() > 1 ? 'drag' : ''}}">
                        @forelse ($data['widgets'] as $item)
                        <tr id="{{ $item['id'] }}" style="cursor: move;">
                            <td>{{ $data['no']++ }} </td>
                            <td>
                                <strong>{!!  Str::replace('_', ' ', Str::upper($item['name'])) !!}</strong>
                            </td>
                            <td>
                                <span class="badge badge-secondary">{{ Str::upper(config('cms.module.widget.set.'.$item['widget_set'])) }}</span>
                            </td>
                            <td>
                                <span class="badge badge-success">{{ Str::replace('_', ' ', Str::upper($item['type'])) }}</span>
                            </td>
                            <td class="text-center">
                                @can ('widget_update')
                                <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="badge badge-{{ $item['publish'] == 1 ? 'primary' : 'warning' }}"
                                    title="@lang('global.status')">
                                    {{ __('global.label.publish.'.$item['publish']) }}
                                    <form action="{{ route('widget.publish', ['id' => $item['id']]) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                </a>
                                @else
                                <span class="badge badge-{{ $item['publish'] == 1 ? 'primary' : 'warning' }}">{{ __('global.label.publish.'.$item['publish']) }}</span>
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
                            <td class="text-center">
                                @if (Auth::user()->can('widget_update') && $item->min('position') != $item['position'])
                                <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn icon-btn btn-sm btn-dark" title="@lang('global.position')">
                                    <i class="las la-arrow-up"></i>
                                    <form action="{{ route('widget.position', ['id' => $item['id'], 'position' => ($item['position'] - 1)]) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                </a>
                                @else
                                <button type="button" class="btn icon-btn btn-sm btn-secondary" title="@lang('global.position')" disabled><i class="las la-arrow-up"></i></button>
                                @endif
                                @if (Auth::user()->can('widget_update') && $item->max('position') != $item['position'])
                                <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn icon-btn btn-sm btn-dark" title="@lang('global.position')">
                                    <i class="las la-arrow-down"></i>
                                    <form action="{{ route('widget.position', ['id' => $item['id'], 'position' => ($item['position'] + 1)]) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                </a>
                                @else
                                <button type="button" class="btn icon-btn btn-sm btn-secondary" title="@lang('global.position')" disabled><i class="las la-arrow-down"></i></button>
                                @endif
                            </td>
                            <td class="text-center">
                                @can('widget_update')
                                <a href="{{ route('widget.edit', array_merge(['type' => $item['type'], 'id' => $item['id']], $queryParam)) }}" class="btn icon-btn btn-sm btn-primary" title="@lang('global.edit_attr', [
                                    'attribute' => __('module/widget.caption')
                                ])">
                                    <i class="las la-pen"></i>
                                </a>
                                @endcan
                                @can('widget_delete')
                                @if ($item['locked'] == 0)
                                <button type="button" class="btn btn-danger icon-btn btn-sm swal-delete" title="@lang('global.delete_attr', [
                                    'attribute' => __('module/widget.caption')
                                    ])"
                                    data-id="{{ $item['id'] }}">
                                    <i class="las la-trash-alt"></i>
                                </button>
                                @endcan
                                @endif
                                @if (Auth::user()->hasRole('developer|super') && config('cms.module.widget.approval') == true)
                                <a href="javascript:void(0);" onclick="$(this).find('#form-approval').submit();" class="btn icon-btn btn-sm btn-{{ $item['approved'] == 1 ? 'danger' : 'primary' }}" title="{{ $item['approved'] == 1 ? __('global.label.flags.0') : __('global.label.flags.1')}}">
                                    <i class="las la-{{ $item['approved'] == 1 ? 'times' : 'check' }}"></i>
                                    <form action="{{ route('widget.approved', ['id' => $item['id']]) }}" method="POST" id="form-approval">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" align="center">
                                <i>
                                    <strong style="color:red;">
                                    @if ($totalQueryParam > 0)
                                    ! @lang('global.data_attr_not_found', [
                                        'attribute' => __('module/widget.caption')
                                    ]) !
                                    @else
                                    ! @lang('global.data_attr_empty', [
                                        'attribute' => __('module/widget.caption')
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
                            @lang('pagination.showing') : <strong>{{ $data['widgets']->firstItem() }}</strong> - <strong>{{ $data['widgets']->lastItem() }}</strong> @lang('pagination.of')
                            <strong>{{ $data['widgets']->total() }}</strong>
                        </div>
                        <div class="col-lg-6 m--align-right">
                            {{ $data['widgets']->onEachSide(1)->links() }}
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
<script src="{{ asset('assets/backend/jquery-ui.js') }}"></script>
<script>
    //sort
    $(function () {
        var refreshNeeded = false;
        $(".drag").sortable({
            connectWith: '.drag',
            update : function (event, ui) {
                var data  = $(this).sortable('toArray');
                $.ajax({
                    data: {'datas' : data},
                    url: '/admin/widget/sort',
                    type: 'POST',
                    dataType:'json',
                    success: function(){
                        refreshNeeded = true;
                    },
                    error: function(argument, error){
                        refreshNeeded = true;
                    },
                });
            }
        }).disableSelection();

        $(document).ajaxStop(function(){
            if(refreshNeeded){
                window.location.reload();
            }
        });
    });

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
                        url: '/admin/widget/'+id+'/soft',
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
                        text: "@lang('global.alert.delete_success', ['attribute' => __('module/widget.caption')])"
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