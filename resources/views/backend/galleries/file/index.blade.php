@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/fancybox/fancybox.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/dropzone/dropzone.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/sweetalert2/sweetalert2.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-12 col-lg-12 col-md-12">

        <div class="card">
            <div class="card-header">
                <h5 class="my-2">
                    @lang('module/gallery.file.text')
                </h5>
                <div class="box-btn">
                    @can('gallery_file_create')
                    <a href="{{ route('gallery.file.create', array_merge(['albumId' => $data['album']['id']], $queryParam)) }}" class="btn btn-main w-icon" title="@lang('global.add_attr_new', [
                        'attribute' => __('module/gallery.file.caption')
                        ])">
                        <i class="fi fi-rr-add"></i> <span>@lang('module/gallery.file.caption')</span>
                    </a>
                    @if (Auth::user()->hasRole('developer|super') || $data['album']['config']['type_image'] == true)
                    <button class="btn btn-secondary w-icon" data-toggle="modal" data-target="#modals-dragDrop" title="@lang('global.add_attr_new', [
                        'attribute' => __('module/gallery.file.caption')
                        ])">
                        <i class="fi fi-rr-gallery"></i>
                        <span>@lang('global.drag_drop')</span>
                    </button>
                    @endif
                    @endcan
                    <button type="button" class="btn btn-default w-icon" data-toggle="modal"
                        data-target="#modals-slide" title="@lang('global.filter')">
                        <i class="fi fi-rr-filter"></i>
                        <span>@lang('global.filter')</span>
                    </button>
                    @role('developer|super')
                    <a href="{{ route('gallery.file.trash', ['albumId' => $data['album']['id']]) }}" class="btn btn-dark w-icon" title="@lang('global.trash')">
                        <i class="fi fi-rr-trash"></i> <span>@lang('global.trash')</span>
                    </a>
                    @endrole
                </div>
                <!-- Modal Drag/Drop -->
                <div class="modal fade" id="modals-dragDrop">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    @lang('global.drag_drop')
                                    <span class="font-weight-light">File</span>
                                </h5>
                                <button type="button" class="close" data-dismiss="modal"
                                    aria-label="Close"><i class="fi fi-rr-cross-small"></i></button>
                            </div>
                            <div class="modal-body">
                                <div class="dropzone needsclick" id="dropzone-upload">
                                    <div class="dz-message needsclick">
                                        @lang('global.drag_drop')
                                        <span class="note needsclick">
                                            (
                                                Allowed : <strong>{{ Str::upper(config('cms.files.gallery.mimes')) }}</strong>.
                                                Max Upload File : <strong>10</strong>. Max Size : <strong>{{ config('cms.files.gallery.size') }}</strong>.
                                            )
                                        </span>
                                    </div>
                                    <div class="fallback">
                                        <input name="file" type="file" multiple>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                        @foreach (config('cms.module.gallery.file.type') as $key => $val)
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
            <hr class="border-light m-0">
            <div class="card-header">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <span>{{ Str::upper(__('module/gallery.album.caption')) }}</span>
                    </li>
                    <li class="breadcrumb-item active">
                        <b class="text-main">{{ $data['album']->fieldLang('name') }}</b>
                    </li>
                </ol>
            </div>
            <hr class="border-light m-0">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px;">#</th>
                            <th style="width: 210px;">@lang('module/gallery.file.label.image')</th>
                            <th>@lang('module/gallery.file.label.title')</th>
                            <th style="width: 100px;">@lang('global.type')</th>
                            <th class="text-center" style="width: 100px;">@lang('global.status')</th>
                            <th style="width: 230px;">@lang('global.created')</th>
                            <th style="width: 230px;">@lang('global.updated')</th>
                            @if ($data['album']['config']['file_order_by'] == 'position')
                            <th class="text-center" style="width: 110px;"></th>
                            @endif
                            <th class="text-center" style="width: 180px;"></th>
                        </tr>
                    </thead>
                    <tbody class="{{ $data['files']->total() > 1 && $data['album']['config']['file_order_by'] == 'position' ? 'drag' : ''}}">
                        @forelse ($data['files'] as $item)
                        <tr id="{{ $item['id'] }}" style="cursor: move;">
                            <td>{{ $data['no']++ }}</td>
                            <td>
                                <a href="{{ $item['type'] == '1' && $item['video_type'] == '1' ? $item['file_src']['video'] : $item['file_src']['image'] }}"
                                    class="img-thumbnail" data-fancybox="gallery">
                                    <img src="{{ $item['file_src']['image'] }}" alt="" class="img-fluid">
                                </a>
                                @if ($item['approved'] != 1)
                                <br>
                                <small class="form-text text-danger">@lang('global.approval_info')</small>
                                @endif
                            </td>
                            <td>
                                {!! !empty($item->fieldLang('title')) ? Str::limit($item->fieldLang('title'), 30) : __('global.field_empty_attr', [
                                    'attribute' => __('module/gallery.file.label.title')
                                    ]) !!}
                                @if (!empty($item->fieldLang('description')))
                                    <br>
                                    <small class="text-muted">{!! Str::limit($item->fieldLang('description'), 45) !!}</small>
                                @endif
                            </td>
                            <td>
                                @switch($item['type'])
                                    @case(1)
                                        <span class="badge badge-danger">{{ config('cms.module.gallery.file.type.'.$item['type']) }}</span>
                                        @break
                                    @case(2)
                                        <span class="badge badge-secondary">{{ config('cms.module.gallery.file.type.'.$item['type']) }}</span>
                                        @break
                                    @default
                                    <span class="badge badge-success">{{ config('cms.module.gallery.file.type.'.$item['type']) }}</span>
                                @endswitch
                            </td>
                            <td class="text-center">
                                @can('gallery_file_update')
                                <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="badge badge-{{ $item['publish'] == 1 ? 'main' : 'warning' }}"
                                    title="{{ __('global.label.publish.'.$item['publish']) }}">
                                    {{ __('global.label.publish.'.$item['publish']) }}
                                    <form action="{{ route('gallery.file.publish', ['albumId' => $item['gallery_album_id'], 'id' => $item['id']]) }}" method="POST">
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
                            @if ($data['album']['config']['file_order_by'] == 'position')
                            <td>
                                <div class="box-btn flex-wrap justify-content-center">
                                    @if (Auth::user()->can('gallery_file_update') && $item->where('gallery_album_id', $item['gallery_album_id'])->min('position') != $item['position'])
                                    <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn icon-btn btn-sm btn-dark" title="@lang('global.position')">
                                        <i class="fi fi-rr-arrow-small-up"></i>
                                        <form action="{{ route('gallery.file.position', ['albumId' => $item['gallery_album_id'], 'id' => $item['id'], 'position' => ($item['position'] - 1)]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                    </a>
                                    @else
                                    <button type="button" class="btn icon-btn btn-sm btn-secondary" title="@lang('global.position')" disabled>
                                        <i class="fi fi-rr-arrow-small-up"></i>
                                    </button>
                                    @endif
                                    @if (Auth::user()->can('gallery_file_update') && $item->where('gallery_album_id', $item['gallery_album_id'])->max('position') != $item['position'])
                                    <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn icon-btn btn-sm btn-dark" title="@lang('global.position')">
                                        <i class="fi fi-rr-arrow-small-down"></i>
                                        <form action="{{ route('gallery.file.position', ['albumId' => $item['gallery_album_id'], 'id' => $item['id'], 'position' => ($item['position'] + 1)]) }}" method="POST">
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
                            @endif
                            <td>
                                <div class="box-btn flex-wrap justify-content-end">
                                    @if ($item['type'] == '1' && $item['video_type'] == '0')
                                    <button type="button" class="btn btn-main icon-btn btn-sm modals-preview" data-toggle="modal" 
                                        data-target="#preview-video" data-video="{!! $item['file_src']['video'] !!}" title="@lang('global.preview')">
                                        <i class="fi fi-rr-play-alt"></i>
                                    </button>
                                    @endif
                                    @can('gallery_file_update')
                                    <a href="{{ route('gallery.file.edit', array_merge(['albumId' => $item['gallery_album_id'], 'id' => $item['id']], $queryParam)) }}" class="btn icon-btn btn-sm btn-success" title="@lang('global.edit_attr', [
                                        'attribute' => __('module/gallery.file.caption')
                                    ])">
                                        <i class="fi fi-rr-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('gallery_file_delete')
                                        @if ($item['locked'] == 0)
                                        <button type="button" class="btn btn-danger icon-btn btn-sm swal-delete" title="@lang('global.delete_attr', [
                                                'attribute' => __('module/gallery.file.caption')
                                            ])"
                                            data-album-id="{{ $item['gallery_album_id'] }}"
                                            data-id="{{ $item['id'] }}">
                                            <i class="fi fi-rr-trash"></i>
                                        </button>
                                        @endif
                                    @endcan
                                    @if (Auth::user()->hasRole('developer|super|support|admin') && config('cms.module.gallery.file.approval') == true)
                                    <a href="javascript:void(0);" onclick="$(this).find('#form-approval').submit();" class="btn icon-btn btn-sm btn-default" 
                                        title="{{ $item['approved'] == 1 ? __('global.label.flags.0') : __('global.label.flags.1')}}">
                                        <i class="fi fi-rr-{{ $item['approved'] == 1 ? 'ban text-danger' : 'check text-success' }}"></i>
                                        <form action="{{ route('gallery.file.approved', ['albumId' => $item['gallery_album_id'], 'id' => $item['id']]) }}" method="POST" id="form-approval">
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
                                        'attribute' => __('module/gallery.file.caption')
                                    ]) !
                                    @else
                                    ! @lang('global.data_attr_empty', [
                                        'attribute' => __('module/gallery.file.caption')
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
            @if ($data['files']->total() > 0)
            <div class="card-footer justify-content-center justify-content-lg-between align-items-center flex-wrap">
                <div class="text-muted mb-3 m-lg-0">
                    @lang('pagination.showing') 
                    <strong>{{ $data['files']->firstItem() }}</strong> - 
                    <strong>{{ $data['files']->lastItem() }}</strong> 
                    @lang('pagination.of')
                    <strong>{{ $data['files']->total() }}</strong>
                </div>
                {{ $data['files']->onEachSide(1)->links() }}
            </div>
            @endif
        </div>

    </div>
</div>

@include('backend.galleries.file.modal-preview')
@endsection

@section('scripts')
<script src="{{ asset('assets/backend/js/ui_tooltips.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/fancybox/fancybox.min.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/dropzone/dropzone.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('jsbody')
<script src="{{ asset('assets/backend/js/jquery-ui.js') }}"></script>
<script>
    //dropzone
    $(document).ready(function () {
        //form upload
        $('.files').show();
        $("#dropzone").hide();
        $('#upload').click(function(){
            $('.files').toggle('slow');
            $("#dropzone").toggle('slow');
        });
    });
    $('#dropzone-upload').dropzone({
        url: '/admin/gallery/album/{{ $data['album']['id'] }}/file/multiple',
        method:'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        parallelUploads: 2,
        maxFilesize: 0,
        maxFiles: 10,
        filesizeBase: 1000,
        acceptedFiles:"image/*",
        paramName:"file",
        dictInvalidFileType:"Type file not allowed",
        addRemoveLinks: true,

        init : function () {
            this.on('complete', function () {
                if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                    toastr.options = {
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": false,
                        "progressBar": false,
                        "positionClass": "toast-top-center",
                        "preventDuplicates": false,
                        "onclick": null,
                        "showDuration": "300",
                        "hideDuration": "1000",
                        "timeOut": "5000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    };
                    toastr.success('@lang('global.alert.create_success', ['attribute' => __('module/gallery.file.caption')])', '@lang('global.alert.success_caption')');

                    setTimeout(() => window.location.reload(), 1500);
                }
            });
        }
    });

    //modals preview
    $('.modals-preview').click(function() {
        var video = $(this).data('video');

        $('.modal-body').html(`
            <video style="width:100%; height:100%;" controls>
                <source src="`+video+`" type="video/mp4">
                Your browser does not support HTML video.
            </video>
        `);
    });

    //sort
    $(function () {
        var refreshNeeded = false;
        $(".drag").sortable({
            connectWith: '.drag',
            update : function (event, ui) {
                var data  = $(this).sortable('toArray');
                var albumId = '{{ $data['album']['id'] }}';
                $.ajax({
                    data: {'datas' : data},
                    url: '/admin/gallery/album/'+albumId+'/file/sort',
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
            var albumId = $(this).attr('data-album-id');
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
                        url: '/admin/gallery/album/' + albumId + '/file/'+ id +'/soft',
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
                        text: "@lang('global.alert.delete_success', ['attribute' => __('module/gallery.file.caption')])"
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