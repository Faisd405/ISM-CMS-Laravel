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
                    @lang('module/event.text')
                </h5>
                <div class="box-btn">
                    @can ('event_create')
                    <a href="{{ route('event.create', $queryParam) }}" class="btn btn-main w-icon" title="@lang('global.add_attr_new', [
                        'attribute' => __('module/event.caption')
                        ])">
                        <i class="fi fi-rr-add"></i>
                        <span>@lang('module/event.caption')</span>
                    </a>
                    @endcan
                    <button type="button" class="btn btn-default w-icon" data-toggle="modal"
                        data-target="#modals-slide" title="@lang('global.filter')">
                        <i class="fi fi-rr-filter"></i>
                        <span>@lang('global.filter')</span>
                    </button>
                    @role('developer|super')
                    <a href="{{ route('event.trash') }}" class="btn btn-dark w-icon" title="@lang('global.trash')">
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
                                    <label class="form-label">@lang('global.type')</label>
                                    <select class="form-control" name="type">
                                        <option value=" " selected>@lang('global.show_all')</option>
                                        @foreach (config('cms.module.event.type') as $key => $val)
                                        <option value="{{ $key }}" {{ Request::get('type') == ''.$key.'' ? 'selected' : '' }} 
                                            title="{{ $val }}">{{ $val }}</option>
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
                            <th>@lang('module/event.label.name')</th>
                            <th class="text-center" style="width: 100px;">@lang('global.type')</th>
                            <th class="text-center" style="width: 80px;">@lang('global.hits')</th>
                            <th class="text-center" style="width: 100px;">@lang('global.status')</th>
                            <th style="width: 230px;">@lang('global.created')</th>
                            <th style="width: 230px;">@lang('global.updated')</th>
                            <th class="text-center" style="width: 110px;"></th>
                            <th class="text-center" style="width: 210px;"></th>
                        </tr>
                    </thead>
                    <tbody class="{{ $data['events']->total() > 1 && isset(config('cms.module.event.ordering')['position']) ? 'drag' : ''}}">
                        @forelse ($data['events'] as $item)
                        <tr id="{{ $item['id'] }}" style="cursor: move;">
                            <td>{{ $data['no']++ }}</td>
                            <td>
                                <strong>{!! Str::limit($item->fieldLang('name'), 65) !!}</strong>
                                @if ($item['detail'] == 1)
                                <a href="{{ route('event.read', ['slugEvent' => $item['slug']]) }}" title="@lang('global.view_detail')" target="_blank">
                                    <i class="fi fi-rr-link text-bold" style="font-size: 14px;"></i>
                                </a>
                                @endif
                                @if ($item['approved'] != 1)
                                <br>
                                <small class="form-text text-danger">@lang('global.approval_info')</small>
                                @endif
                                @php
                                    $unread =  $item->forms()->where('status', 0)->count()
                                @endphp
                                @if ($unread > 0)
                                <br>
                                <small class="form-text"><span class="badge badge-main">{{ $unread }}</span> @lang('global.unread_message')</small>
                                @endif
                            </td>
                            <td class="text-center"><span class="badge badge-{{ $item['type'] == 1 ? 'success' : 'secondary' }}">{{ config('cms.module.event.type.'.$item['type']) }}</span></td>
                            <td class="text-center"><span class="badge badge-info">{{ $item['hits'] }}</span></td>
                            <td class="text-center">
                                @can ('event_update')
                                <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="badge badge-{{ $item['publish'] == 1 ? 'main' : 'warning' }}"
                                    title="@lang('global.status')">
                                    {{ __('global.label.publish.'.$item['publish']) }}
                                    <form action="{{ route('event.publish', ['id' => $item['id']]) }}" method="POST">
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
                                @if (isset(config('cms.module.event.ordering')['position']))    
                                <div class="box-btn flex-wrap justify-content-center">
                                    @if (Auth::user()->can('event_update') && $item->min('position') != $item['position'])
                                    <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn icon-btn btn-sm btn-dark" title="@lang('global.position')">
                                        <i class="fi fi-rr-arrow-small-up"></i>
                                        <form action="{{ route('event.position', ['id' => $item['id'], 'position' => ($item['position'] - 1)]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                    </a>
                                    @else
                                    <button type="button" class="btn icon-btn btn-sm btn-secondary" title="@lang('global.position')" disabled>
                                        <i class="fi fi-rr-arrow-small-up"></i>
                                    </button>
                                    @endif
                                    @if (Auth::user()->can('event_update') && $item->max('position') != $item['position'])
                                    <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn icon-btn btn-sm btn-dark" title="@lang('global.position')">
                                        <i class="fi fi-rr-arrow-small-down"></i>
                                        <form action="{{ route('event.position', ['id' => $item['id'], 'position' => ($item['position'] + 1)]) }}" method="POST">
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
                                @endif
                            </td>
                            <td>
                                <div class="box-btn flex-wrap justify-content-end">
                                    @if ($item['config']['show_form'] == true)
                                    <a href="{{ route('event.form', ['eventId' => $item['id']]) }}" class="btn icon-btn btn-sm btn-main" title="@lang('module/event.form.caption')">
                                        <i class="fi fi-rr-envelope-download"></i>
                                    </a>
                                    @endif
                                    @can('event_fields')
                                    <a href="{{ route('event.field.index', ['eventId' => $item['id']]) }}" class="btn icon-btn btn-sm btn-main" title="@lang('module/event.field.caption')">
                                        <i class="fi fi-rr-form"></i>
                                    </a>
                                    @endcan
                                    @can('event_update')
                                    <a href="{{ route('event.edit', array_merge(['id' => $item['id']], $queryParam)) }}" class="btn icon-btn btn-sm btn-success" title="@lang('global.edit_attr', [
                                        'attribute' => __('module/event.caption')
                                    ])">
                                        <i class="fi fi-rr-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('event_delete')
                                        @if ($item['locked'] == 0)
                                        <button type="button" class="btn btn-danger icon-btn btn-sm swal-delete" title="@lang('global.delete_attr', [
                                                'attribute' => __('module/event.caption')
                                            ])"
                                            data-id="{{ $item['id'] }}">
                                            <i class="fi fi-rr-trash"></i>
                                        </button>
                                        @endif
                                    @endcan
                                    @if (Auth::user()->hasRole('developer|super') && config('cms.module.inquiry.approval') == true)
                                    <a href="javascript:void(0);" onclick="$(this).find('#form-approval').submit();" class="btn icon-btn btn-sm btn-default" 
                                        title="{{ $item['approved'] == 1 ? __('global.label.flags.0') : __('global.label.flags.1')}}">
                                        <i class="fi fi-rr-{{ $item['approved'] == 1 ? 'ban text-danger' : 'check text-success' }}"></i>
                                        <form action="{{ route('event.approved', ['id' => $item['id']]) }}" method="POST" id="form-approval">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" align="center">
                                <i>
                                    <strong class="text-muted">
                                    @if ($totalQueryParam > 0)
                                    ! @lang('global.data_attr_not_found', [
                                        'attribute' => __('module/event.caption')
                                    ]) !
                                    @else
                                    ! @lang('global.data_attr_empty', [
                                        'attribute' => __('module/event.caption')
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
            @if ($data['events']->total() > 0)
            <div class="card-footer justify-content-center justify-content-lg-between align-items-center flex-wrap">
                <div class="text-muted mb-3 m-lg-0">
                    @lang('pagination.showing') 
                    <strong>{{ $data['events']->firstItem() }}</strong> - 
                    <strong>{{ $data['events']->lastItem() }}</strong> 
                    @lang('pagination.of')
                    <strong>{{ $data['events']->total() }}</strong>
                </div>
                {{ $data['events']->onEachSide(1)->links() }}
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
<script src="{{ asset('assets/backend/js/jquery-ui.js') }}"></script>
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
                    url: '/admin/event/sort',
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
                        url: '/admin/event/'+id+'/soft',
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
                        text: "@lang('global.alert.delete_success', ['attribute' => __('module/event.caption')])"
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