@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/fancybox/fancybox.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/sweetalert2/sweetalert2.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-12 col-lg-12 col-md-12">

        <div class="card">
            <div class="card-header">
                <h5 class="my-2">
                    @lang('master/media.text')
                </h5>
                <div class="box-btn">
                    @can('media_create')
                        @if (Auth::user()->hasRole('developer|super') || $data['module']['config']['action_media'] == true)
                        <a href="{{ route('media.create', $data['params']) }}" class="btn btn-main w-icon" title="@lang('global.add_attr_new', [
                            'attribute' => __('master/media.caption')
                            ])">
                            <i class="fi fi-rr-add"></i>
                            <span>@lang('master/media.caption')</span>
                        </a>
                        @endif
                    @endcan
                    <button type="button" class="btn btn-default w-icon" data-toggle="modal"
                        data-target="#modals-slide" title="@lang('global.filter')">
                        <i class="fi fi-rr-filter"></i>
                        <span>@lang('global.filter')</span>
                    </button>
                    @role('developer|super')
                    <a href="{{ route('media.trash', $data['params']) }}" class="btn btn-dark w-icon" title="@lang('global.trash')">
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
                        <span>{{ Str::upper(Str::replace('_', ' ', Request::segment(4))) }}</span>
                    </li>
                    <li class="breadcrumb-item active">
                        <b class="text-main">{{ $data['module']['title'][App::getLocale()] }}</b>
                    </li>
                </ol>
            </div>
            <hr class="border-light m-0">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px;">#</th>
                            <th>@lang('master/media.caption')</th>
                            <th>@lang('master/media.label.title')</th>
                            <th style="width: 230px;">@lang('global.created')</th>
                            <th style="width: 230px;">@lang('global.updated')</th>
                            <th class="text-center" style="width: 100px;"></th>
                            <th class="text-center" style="width: 100px;"></th>
                        </tr>
                    </thead>
                    <tbody class="{{ $data['medias']->total() > 1 ? 'drag' : ''}}">
                        @forelse ($data['medias'] as $item)
                        <tr id="{{ $item['id'] }}" style="cursor: move;">
                            <td>{{ $data['no']++ }}</td>
                            <td>
                                @if ($item['is_youtube'] == 1)
                                <a href="https://www.youtube.com/embed/{{ $item['youtube_id'] }}?rel=0;showinfo=0" data-fancybox="gallery">
                                    <img class="img-thumbnail" src="https://img.youtube.com/vi/{{ $item['youtube_id'] }}/mqdefault.jpg" alt="">
                                </a>
                                @else
                                    @if ($item['icon'] == 'image')
                                    <a href="{{ $item['file_src'] }}" data-fancybox="gallery">
                                        <img class="img-thumbnail" src="{{ $item['file_src'] }}" alt="">
                                    </a>
                                    @else
                                    File <b class="text-main">({{ $item['icon'] }})</b>
                                    @endif
                                @endif
                            </td>
                            <td>
                                {{ !empty($item->fieldLang('title')) ? $item->fieldLang('title') : __('global.field_empty_attr', [
                                    'attribute' => __('master/media.label.title')
                                    ]) }}
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
                                <div class="box-btn flex-wrap justify-content-end">
                                    @if (Auth::user()->can('media_update') && $item->where(['module' => $item['module'], 'mediable_id' => $item['mediable_id']])->min('position') != $item['position'])
                                    <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn icon-btn btn-sm btn-dark" 
                                        data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="@lang('global.position')">
                                        <i class="fi fi-rr-arrow-small-up"></i>
                                        <form action="{{ route('media.position', ['moduleId' => $item['mediable_id'], 'moduleType' => $item['module'], 'id' => $item['id'], 'position' => ($item['position'] - 1)]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                    </a>
                                    @else
                                    <button type="button" class="btn icon-btn btn-sm btn-secondary" 
                                        data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="@lang('global.position')" disabled>
                                        <i class="fi fi-rr-arrow-small-up"></i>
                                    </button>
                                    @endif
                                    @if (Auth::user()->can('media_update') && $item->where(['module' => $item['module'], 'mediable_id' => $item['mediable_id']])->max('position') != $item['position'])
                                    <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn icon-btn btn-sm btn-dark" 
                                        data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="@lang('global.position')">
                                        <i class="fi fi-rr-arrow-small-down"></i>
                                        <form action="{{ route('media.position', ['moduleId' => $item['mediable_id'], 'moduleType' => $item['module'], 'id' => $item['id'], 'position' => ($item['position'] + 1)]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                    </a>
                                    @else
                                    <button type="button" class="btn icon-btn btn-sm btn-secondary" 
                                        data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="@lang('global.position')" disabled>
                                        <i class="fi fi-rr-arrow-small-down"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="box-btn flex-wrap justify-content-end">
                                    @can('media_update')
                                    <a href="{{ route('media.edit', ['moduleId' => $item['mediable_id'], 'moduleType' => $item['module'], 'id' => $item['id']]) }}" class="btn icon-btn btn-sm btn-success" 
                                        data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="@lang('global.edit_attr', [
                                        'attribute' => __('master/media.caption')
                                    ])">
                                        <i class="fi fi-rr-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('media_delete')
                                        @if (Auth::user()->hasRole('developer|super') || $item['locked'] == 0 && $data['module']['config']['action_media'] == true)
                                        <button type="button" class="btn btn-danger icon-btn btn-sm swal-delete" 
                                            data-toggle="tooltip" data-placement="bottom"
                                            data-original-title="@lang('global.delete_attr', [
                                                'attribute' => __('master/media.caption')
                                            ])"
                                            data-module-id="{{ $item['mediable_id'] }}"
                                            data-module-type="{{ $item['module'] }}"
                                            data-id="{{ $item['id'] }}">
                                            <i class="fi fi-rr-trash"></i>
                                        </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" align="center">
                                <i>
                                    <strong class="text-muted">
                                    @if ($totalQueryParam > 0)
                                    ! @lang('global.data_attr_not_found', [
                                        'attribute' => __('master/media.caption')
                                    ]) !
                                    @else
                                    ! @lang('global.data_attr_empty', [
                                        'attribute' => __('master/media.caption')
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
            @if ($data['medias']->total() > 0)
            <div class="card-footer justify-content-center justify-content-lg-between align-items-center flex-wrap">
                <div class="text-muted mb-3 m-lg-0">
                    @lang('pagination.showing') 
                    <strong>{{ $data['medias']->firstItem() }}</strong> - 
                    <strong>{{ $data['medias']->lastItem() }}</strong> 
                    @lang('pagination.of')
                    <strong>{{ $data['medias']->total() }}</strong>
                </div>
                {{ $data['medias']->onEachSide(1)->links() }}
            </div>
            @endif
        </div>
        
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/backend/js/ui_tooltips.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/fancybox/fancybox.min.js') }}"></script>
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
                var moduleId = '{{ $data['params']['moduleId'] }}';
                var moduleType = '{{ $data['params']['moduleType'] }}';
                $.ajax({
                    data: {'datas' : data},
                    url: '/admin/media/'+moduleId+'/'+moduleType+'/sort',
                    type: 'POST',
                    dataType:'json',
                    success: function(){
                        refreshNeeded = true;
                    },
                    error: function(argument, error){
                        refreshNeeded = true;
                    },
                });
            },
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
            var modId = $(this).attr('data-module-id');
            var modType = $(this).attr('data-module-type');
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
                        url: '/admin/media/'+ modId +'/'+ modType +'/'+ id +'/soft',
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
                        text: "@lang('global.alert.delete_success', ['attribute' => __('master/media.caption')])"
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