@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/fancybox/fancybox.min.css') }}">
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
                    @can('link_media_create')
                    <a href="{{ route('link.media.create', array_merge(['linkId' => $data['link']['id']], $queryParam)) }}" class="btn btn-success icon-btn-only-sm btn-sm mr-2" title="@lang('global.add_attr_new', [
                        'attribute' => __('module/link.media.caption')
                        ])">
                        <i class="las la-plus"></i> <span>@lang('module/link.media.caption')</span>
                    </a>
                    @endcan
                    @role('developer|super')
                    <a href="{{ route('link.media.trash', ['linkId' => $data['link']['id']]) }}" class="btn btn-secondary icon-btn-only-sm btn-sm" title="@lang('global.trash')">
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
                                    @foreach (__('global.label.publish') as $key => $val)
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
                <h5 class="card-header-title mt-1 mb-0">@lang('module/link.media.text')</h5>
            </div>
            <div class="card-header">
                <span class="text-muted">
                    {{ Str::upper(__('module/link.caption')) }} : <b class="text-primary">{{ $data['link']->fieldLang('name') }}</b>
                </span>
            </div>

            <div class="table-responsive">
                <table class="table card-table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 10px;">#</th>
                            <th>@lang('module/link.media.label.field1')</th>
                            <th>@lang('module/link.media.label.field3')</th>
                            <th class="text-center" style="width: 100px;">@lang('global.status')</th>
                            <th style="width: 230px;">@lang('global.created')</th>
                            <th style="width: 230px;">@lang('global.updated')</th>
                            @if ($data['link']['config']['media_order_by'] == 'position')
                            <th class="text-center" style="width: 110px;"></th>
                            @endif
                            <th class="text-center" style="width: 140px;"></th>
                        </tr>
                    </thead>
                    <tbody class="{{ $data['medias']->total() > 1 && $data['link']['config']['media_order_by'] == 'position' ? 'drag' : ''}}">
                        @forelse ($data['medias'] as $item)
                        <tr id="{{ $item['id'] }}" style="cursor: move;">
                            <td>{{ $data['no']++ }}</td>
                            <td>
                                {!! Str::limit($item->fieldLang('title'), 30) !!}
                            </td>
                            <td>
                                <a href="{{ $item['url'] }}" target="_blank">
                                    {{ Str::limit($item['url'], 30) }}
                                </a>
                            </td>
                            <td class="text-center">
                                @can('link_media_update')
                                <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="badge badge-{{ $item['publish'] == 1 ? 'primary' : 'warning' }}"
                                    title="{{ __('global.label.publish.'.$item['publish']) }}">
                                    {{ __('global.label.publish.'.$item['publish']) }}
                                    <form action="{{ route('link.media.publish', ['linkId' => $item['link_id'], 'id' => $item['id']]) }}" method="POST">
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
                            @if ($data['link']['config']['media_order_by'] == 'position')
                            <td class="text-center">
                                @if (Auth::user()->can('link_media_update') && $item->where('link_id', $item['link_id'])->min('position') != $item['position'])
                                <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn icon-btn btn-sm btn-dark" title="@lang('global.position')">
                                    <i class="las la-arrow-up"></i>
                                    <form action="{{ route('link.media.position', ['linkId' => $item['link_id'], 'id' => $item['id'], 'position' => ($item['position'] - 1)]) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                </a>
                                @else
                                <button type="button" class="btn icon-btn btn-sm btn-secondary" title="@lang('global.position')" disabled><i class="las la-arrow-up"></i></button>
                                @endif
                                @if (Auth::user()->can('link_media_update') && $item->where('link_id', $item['link_id'])->max('position') != $item['position'])
                                <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn icon-btn btn-sm btn-dark" title="@lang('global.position')">
                                    <i class="las la-arrow-down"></i>
                                    <form action="{{ route('link.media.position', ['linkId' => $item['link_id'], 'id' => $item['id'], 'position' => ($item['position'] + 1)]) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                </a>
                                @else
                                <button type="button" class="btn icon-btn btn-sm btn-secondary" title="@lang('global.position')" disabled><i class="las la-arrow-down"></i></button>
                                @endif
                            </td>
                            @endif
                            <td class="text-center">
                                @can('link_media_update')
                                <a href="{{ route('link.media.edit', array_merge(['linkId' => $item['link_id'], 'id' => $item['id']], $queryParam)) }}" class="btn icon-btn btn-sm btn-primary" title="@lang('global.edit_attr', [
                                    'attribute' => __('module/link.media.caption')
                                ])">
                                    <i class="las la-pen"></i>
                                </a>
                                @endcan
                                @can('link_media_delete')
                                @if ($item['locked'] == 0)
                                <button type="button" class="btn btn-danger icon-btn btn-sm swal-delete" title="@lang('global.delete_attr', [
                                    'attribute' => __('module/link.media.caption')
                                    ])"
                                    data-link-id="{{ $item['link_id'] }}"
                                    data-id="{{ $item['id'] }}">
                                    <i class="las la-trash-alt"></i>
                                </button>
                                @endif
                                @endcan
                                @if (Auth::user()->hasRole('developer|super|support|admin') && config('cms.module.link.media.approval') == true)
                                <a href="javascript:void(0);" onclick="$(this).find('#form-approval').submit();" class="btn icon-btn btn-sm btn-{{ $item['approved'] == 1 ? 'danger' : 'primary' }}" title="{{ $item['approved'] == 1 ? __('global.label.flags.0') : __('global.label.flags.1')}}">
                                    <i class="las la-{{ $item['approved'] == 1 ? 'times' : 'check' }}"></i>
                                    <form action="{{ route('link.media.approved', ['linkId' => $item['link_id'], 'id' => $item['id']]) }}" method="POST" id="form-approval">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" align="center">
                                <i>
                                    <strong style="color:red;">
                                    @if ($totalQueryParam > 0)
                                    ! @lang('global.data_attr_not_found', [
                                        'attribute' => __('module/link.media.caption')
                                    ]) !
                                    @else
                                    ! @lang('global.data_attr_empty', [
                                        'attribute' => __('module/link.media.caption')
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
                            @lang('pagination.showing') : <strong>{{ $data['medias']->firstItem() }}</strong> - <strong>{{ $data['medias']->lastItem() }}</strong> @lang('pagination.of')
                            <strong>{{ $data['medias']->total() }}</strong>
                        </div>
                        <div class="col-lg-6 m--align-right">
                            {{ $data['medias']->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/backend/fancybox/fancybox.min.js') }}"></script>
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
                var linkId = '{{ $data['link']['id'] }}';
                $.ajax({
                    data: {'datas' : data},
                    url: '/admin/link/'+linkId+'/media/sort',
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
            var linkId = $(this).attr('data-link-id');
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
                        url: '/admin/link/' + linkId + '/media/'+ id +'/soft',
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
                        text: "@lang('global.alert.delete_success', ['attribute' => __('module/link.media.caption')])"
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