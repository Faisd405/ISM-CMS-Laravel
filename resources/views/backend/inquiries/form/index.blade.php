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
                    @lang('module/inquiry.form.text')
                </h5>
                <div class="box-btn">
                    <button type="button" class="btn btn-success w-icon" data-toggle="modal"
                        data-target="#modals-export" title="@lang('global.export')">
                        <i class="fi fi-rr-file-invoice"></i>
                        <span>@lang('global.export')</span>
                    </button>
                    <button type="button" class="btn btn-default w-icon" data-toggle="modal"
                        data-target="#modals-slide" title="@lang('global.filter')">
                        <i class="fi fi-rr-filter"></i>
                        <span>@lang('global.filter')</span>
                    </button>
                </div>
                <!-- Modal Export -->
                <div class="modal modal-export fade" id="modals-export">
                    <div class="modal-dialog">
                        <form class="modal-content" action="{{ route('inquiry.form.export', ['inquiryId' => $data['inquiry']['id']]) }}" method="POST">
                            @csrf

                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"
                                    aria-label="Close"><i class="fi fi-rr-cross-small"></i></button>
                            </div>
                            
                            <div class="modal-body mt-3">
                                <div class="form-group">
                                    <label class="form-label">@lang('global.status')</label>
                                    <select class="form-control" name="status">
                                        <option value=" " selected>@lang('global.show_all')</option>
                                        @foreach (__('global.label.read') as $key => $val)
                                        <option value="{{ $key }}" {{ Request::get('status') == ''.$key.'' ? 'selected' : '' }} 
                                            title="{{ $val }}">{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">@lang('global.export')</label>
                                    <select class="form-control" name="exported">
                                        <option value=" " selected>@lang('global.show_all')</option>
                                        @foreach (__('global.label.optional') as $key => $val)
                                        <option value="{{ $key }}" {{ Request::get('exported') == ''.$key.'' ? 'selected' : '' }} 
                                            title="{{ $val }}">{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="box-btn justify-content-between w-100 m-0">
                                    <button type="submit" class="btn btn-success w-100">@lang('global.export')</button>
                                </div>
                            </div>
                        </form>
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
                                    <label class="form-label">@lang('global.export')</label>
                                    <select class="form-control" name="exported">
                                        <option value=" " selected>@lang('global.show_all')</option>
                                        @foreach (__('global.label.optional') as $key => $val)
                                        <option value="{{ $key }}" {{ Request::get('exported') == ''.$key.'' ? 'selected' : '' }} 
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
                        <span>{{ Str::upper(__('module/inquiry.field.caption')) }}</span>
                    </li>
                    <li class="breadcrumb-item active">
                        <b class="text-main">{{ $data['inquiry']->fieldLang('name') }}</b>
                    </li>
                </ol>
            </div>
            <hr class="border-light m-0">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px;">#</th>
                            <th>@lang('module/inquiry.form.label.ip_address')</th>
                            @foreach ($data['fields']->take(3) as $item)
                                <th>{{ $item->fieldLang('label') }}</th>
                            @endforeach
                            <th class="text-center" style="width: 80px;">@lang('global.status')</th>
                            <th class="text-center" style="width: 80px;">@lang('module/inquiry.form.label.exported')</th>
                            <th style="width: 230px;">@lang('module/inquiry.form.label.submit_time')</th>
                            <th class="text-center" style="width: 110px;">@lang('global.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['forms'] as $item)
                        <tr>
                            <td>{{ $data['no']++ }}</td>
                            <td><strong>{{ $item['ip_address'] }}</strong></td>
                            @foreach ($data['fields']->take(3) as $keyF => $field)
                            <td>
                                {!! isset($item['fields'][$field['name']]) ? $item['fields'][$field['name']] : '-' !!}
                            </td>
                            @endforeach
                            <td class="text-center">
                                <span class="badge badge-{{ $item['status'] == 1 ? 'primary' : 'warning' }}">{{ __('global.label.read.'.$item['status']) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-{{ $item['exported'] == 1 ? 'success' : 'danger' }}">{{ __('global.label.optional.'.$item['exported']) }}</span>
                            </td>
                            <td>
                                {{ $item['submit_time']->format('d F Y (H:i A)') }}
                            </td>
                            <td>
                                <div class="box-btn flex-wrap justify-content-end">
                                    <button type="button" class="btn icon-btn btn-sm btn-main read-form" 
                                        data-inquiry-id="{{ $item['inquiry_id'] }}"
                                        data-id="{{ $item['id'] }}"
                                        data-status="{{ $item['status'] }}"
                                        data-toggle="modal"
                                        data-target="#modal-read-{{ $item['id'] }}"
                                        title="@lang('global.detail')">
                                        <i class="fi fi-rr-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger icon-btn btn-sm swal-delete" title="@lang('global.delete_attr', [
                                            'attribute' => __('module/inquiry.form.caption')
                                        ])"
                                        data-inquiry-id="{{ $item['inquiry_id'] }}" data-id="{{ $item['id'] }}">
                                        <i class="fi fi-rr-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ 6+$data['fields']->count() }}" align="center">
                                <i>
                                    <strong class="text-muted">
                                    @if ($totalQueryParam > 0)
                                    ! @lang('global.data_attr_not_found', [
                                        'attribute' => __('module/inquiry.form.caption')
                                    ]) !
                                    @else
                                    ! @lang('global.data_attr_empty', [
                                        'attribute' => __('module/inquiry.form.caption')
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
            @if ($data['forms']->total() > 0)
            <div class="card-footer justify-content-center justify-content-lg-between align-items-center flex-wrap">
                <div class="text-muted mb-3 m-lg-0">
                    @lang('pagination.showing') 
                    <strong>{{ $data['forms']->firstItem() }}</strong> - 
                    <strong>{{ $data['forms']->lastItem() }}</strong> 
                    @lang('pagination.of')
                    <strong>{{ $data['forms']->total() }}</strong>
                </div>
                {{ $data['forms']->onEachSide(1)->links() }}
            </div>
            @endif
        </div>

    </div>
</div>

@include('backend.inquiries.form.modal-detail')
@endsection

@section('scripts')
<script src="{{ asset('assets/backend/js/ui_tooltips.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('jsbody')
<script>
    $(document).ready(function () {

        //read
        $('.read-form').on('click', function () {
            var inquiryId = $(this).attr('data-inquiry-id');
            var id = $(this).attr('data-id');
            var status = $(this).attr('data-status');
            if (status == 0) {
                $.ajax({
                    url: '/admin/inquiry/' + inquiryId + '/form/' + id + '/status',
                    type: 'PUT',
                });
            }
        });

        $('.swal-delete').on('click', function () {
            var inquiryId = $(this).attr('data-inquiry-id');
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
                        url: '/admin/inquiry/'+inquiryId+'/form/'+id,
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
                        text: "@lang('global.alert.delete_success', ['attribute' => __('module/inquiry.form.caption')])"
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
    })
</script>
@endsection