@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/fancybox/fancybox.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/dropzone/dropzone.css') }}">
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
                    @can('document_file_create')
                    <a href="{{ route('document.file.create', array_merge(['documentId' => $data['document']['id']], $queryParam)) }}" class="btn btn-success icon-btn-only-sm btn-sm mr-2" title="@lang('global.add_attr_new', [
                        'attribute' => __('module/document.file.caption')
                        ])">
                        <i class="las la-plus"></i> <span>@lang('module/document.file.caption')</span>
                    </a>
                    <a href="javascript:;" id="upload" class="btn btn-primary icon-btn-only-sm btn-sm mr-2" title="@lang('global.add_attr_new', [
                        'attribute' => __('module/document.file.caption')
                        ])">
                        <i class="las la-hand-pointer"></i> <span>@lang('global.drag_drop')</span>
                    </a>
                    @endcan
                    @role('developer|super')
                    <a href="{{ route('document.file.trash', ['documentId' => $data['document']['id']]) }}" class="btn btn-secondary icon-btn-only-sm btn-sm" title="@lang('global.trash')">
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
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">@lang('global.type')</label>
                                <select class="custom-select" name="type">
                                    <option value=" " selected>@lang('global.show_all')</option>
                                    @foreach (config('cms.module.document.file.type') as $key => $val)
                                    <option value="{{ $key }}" {{ Request::get('type') == ''.$key.'' ? 'selected' : '' }} 
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

        {{-- Drag / Drop --}}
        <div class="card mb-2" id="dropzone">
            <div class="dropzone needsclick" id="dropzone-upload">
                <div class="dz-message needsclick">
                  @lang('global.drag_drop')
                  <span class="note needsclick">(File Type : <strong>{{ Str::upper(config('cms.files.document.mimes')) }}</strong>, 
                    Max Upload File <strong>10</strong>, Max Upload Size : <strong>{{ config('cms.files.document.size') }}</strong>)</span>
                </div>
                <div class="fallback">
                  <input name="file" type="file" multiple>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header with-elements">
                <h5 class="card-header-title mt-1 mb-0">@lang('module/document.file.text')</h5>
            </div>
            <div class="card-header">
                <span class="text-muted">
                    {{ Str::upper(__('module/document.caption')) }} : <b class="text-primary">{{ $data['document']->fieldLang('name') }}</b>
                </span>
            </div>

            <div class="table-responsive">
                <table class="table card-table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 10px;">#</th>
                            <th style="width: 210px;">@lang('module/document.file.label.file')</th>
                            <th>@lang('module/document.file.label.field1')</th>
                            <th style="width: 100px;">@lang('global.type')</th>
                            <th class="text-center" style="width: 100px;">@lang('global.download')</th>
                            <th class="text-center" style="width: 100px;">@lang('global.status')</th>
                            <th style="width: 230px;">@lang('global.created')</th>
                            <th style="width: 230px;">@lang('global.updated')</th>
                            @if ($data['document']['config']['file_order_by'] == 'position')
                            <th class="text-center" style="width: 110px;"></th>
                            @endif
                            <th class="text-center" style="width: 140px;"></th>
                        </tr>
                    </thead>
                    <tbody class="{{ $data['files']->total() > 1 && $data['document']['config']['file_order_by'] == 'position' ? 'drag' : ''}}">
                        @forelse ($data['files'] as $item)
                        <tr id="{{ $item['id'] }}" style="cursor: move;">
                            <td>{{ $data['no']++ }}</td>
                            <td>
                                <a href="{{ route('document.download', ['id' => $item['id']]) }}" target="_blank" title="@lang('global.download')">
                                    {{ $item['file'] }}
                                </a>
                            </td>
                            <td>
                                {!! !empty($item->fieldLang('title')) ? Str::limit($item->fieldLang('title'), 30) : __('global.field_empty_attr', [
                                    'attribute' => __('module/document.file.label.field1')
                                    ]) !!}
                                @if (!empty($item->fieldLang('description')))
                                    <br>
                                    <small class="text-muted">{!! Str::limit($item->fieldLang('description'), 45) !!}</small>
                                @endif
                            </td>
                            <td>
                                @switch($item['type'])
                                    @case(1)
                                        <span class="badge badge-danger">{{ config('cms.module.document.file.type.'.$item['type']) }}</span>
                                        @break
                                    @case(2)
                                        <span class="badge badge-secondary">{{ config('cms.module.document.file.type.'.$item['type']) }}</span>
                                        @break
                                    @default
                                    <span class="badge badge-success">{{ config('cms.module.document.file.type.'.$item['type']) }}</span>
                                @endswitch
                            </td>
                            <td class="text-center">
                                <span class="badge badge-info">{{ $item['download'] }}</span>
                            </td>
                            <td class="text-center">
                                @can('document_file_update')
                                <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="badge badge-{{ $item['publish'] == 1 ? 'primary' : 'warning' }}"
                                    title="{{ __('global.label.publish.'.$item['publish']) }}">
                                    {{ __('global.label.publish.'.$item['publish']) }}
                                    <form action="{{ route('document.file.publish', ['documentId' => $item['document_id'], 'id' => $item['id']]) }}" method="POST">
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
                            @if ($data['document']['config']['file_order_by'] == 'position')
                            <td class="text-center">
                                @if (Auth::user()->can('document_file_update') && $item->where('document_id', $item['document_id'])->min('position') != $item['position'])
                                <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn icon-btn btn-sm btn-dark" title="@lang('global.position')">
                                    <i class="las la-arrow-up"></i>
                                    <form action="{{ route('document.file.position', ['documentId' => $item['document_id'], 'id' => $item['id'], 'position' => ($item['position'] - 1)]) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                </a>
                                @else
                                <button type="button" class="btn icon-btn btn-sm btn-secondary" title="@lang('global.position')" disabled><i class="las la-arrow-up"></i></button>
                                @endif
                                @if (Auth::user()->can('document_file_update') && $item->where('document_id', $item['document_id'])->max('position') != $item['position'])
                                <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn icon-btn btn-sm btn-dark" title="@lang('global.position')">
                                    <i class="las la-arrow-down"></i>
                                    <form action="{{ route('document.file.position', ['documentId' => $item['document_id'], 'id' => $item['id'], 'position' => ($item['position'] + 1)]) }}" method="POST">
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
                                @can('document_file_update')
                                <a href="{{ route('document.file.edit', array_merge(['documentId' => $item['document_id'], 'id' => $item['id']], $queryParam)) }}" class="btn icon-btn btn-sm btn-primary" title="@lang('global.edit_attr', [
                                    'attribute' => __('module/document.file.caption')
                                ])">
                                    <i class="las la-pen"></i>
                                </a>
                                @endcan
                                @can('document_file_delete')
                                @if ($item['locked'] == 0)
                                <button type="button" class="btn btn-danger icon-btn btn-sm swal-delete" title="@lang('global.delete_attr', [
                                    'attribute' => __('module/document.file.caption')
                                    ])"
                                    data-document-id="{{ $item['document_id'] }}"
                                    data-id="{{ $item['id'] }}">
                                    <i class="las la-trash-alt"></i>
                                </button>
                                @endif
                                @endcan
                                @if (Auth::user()->hasRole('developer|super|support|admin') && config('cms.module.document.file.approval') == true)
                                <a href="javascript:void(0);" onclick="$(this).find('#form-approval').submit();" class="btn icon-btn btn-sm btn-{{ $item['approved'] == 1 ? 'danger' : 'primary' }}" title="{{ $item['approved'] == 1 ? __('global.label.flags.0') : __('global.label.flags.1')}}">
                                    <i class="las la-{{ $item['approved'] == 1 ? 'times' : 'check' }}"></i>
                                    <form action="{{ route('document.file.approved', ['documentId' => $item['document_id'], 'id' => $item['id']]) }}" method="POST" id="form-approval">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" align="center">
                                <i>
                                    <strong style="color:red;">
                                    @if ($totalQueryParam > 0)
                                    ! @lang('global.data_attr_not_found', [
                                        'attribute' => __('module/document.file.caption')
                                    ]) !
                                    @else
                                    ! @lang('global.data_attr_empty', [
                                        'attribute' => __('module/document.file.caption')
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
                            @lang('pagination.showing') : <strong>{{ $data['files']->firstItem() }}</strong> - <strong>{{ $data['files']->lastItem() }}</strong> @lang('pagination.of')
                            <strong>{{ $data['files']->total() }}</strong>
                        </div>
                        <div class="col-lg-6 m--align-right">
                            {{ $data['files']->onEachSide(1)->links() }}
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
<script src="{{ asset('assets/backend/vendor/libs/dropzone/dropzone.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('jsbody')
<script src="{{ asset('assets/backend/jquery-ui.js') }}"></script>
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
        url: '/admin/document/{{ $data['document']['id'] }}/file/multiple',
        method:'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        parallelUploads: 2,
        maxFilesize: 0,
        maxFiles: 10,
        filesizeBase: 1000,
        acceptedFiles:"application/pdf,.doc,.docx,.xls,.xlsx,.csv,.ppt,.pptx",
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
                    toastr.success('@lang('global.alert.create_success', ['attribute' => __('module/document.file.caption')])', '@lang('global.alert.success_caption')');

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
                var documentId = '{{ $data['document']['id'] }}';
                $.ajax({
                    data: {'datas' : data},
                    url: '/admin/document/'+documentId+'/file/sort',
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
            var documentId = $(this).attr('data-document-id');
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
                        url: '/admin/document/' + documentId + '/file/'+ id +'/soft',
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
                        text: "@lang('global.alert.delete_success', ['attribute' => __('module/document.file.caption')])"
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