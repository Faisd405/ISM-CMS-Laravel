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
                    @can('template_create')
                    <a href="{{ route('template.create', $queryParam) }}" class="btn btn-success icon-btn-only-sm btn-sm mr-2" title="@lang('global.add_attr_new', [
                            'attribute' => __('master/template.caption')
                        ])">
                        <i class="las la-plus"></i> <span>@lang('master/template.caption')</span>
                    </a>
                    @endcan
                    @role('developer|super')
                    <a href="{{ route('template.trash') }}" class="btn btn-secondary icon-btn-only-sm btn-sm" title="@lang('global.trash')">
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
                                <label class="form-label">@lang('master/template.label.field2')</label>
                                <select class="custom-select" name="module">
                                    <option value=" " selected>@lang('global.show_all')</option>
                                    @foreach (config('cms.module.master.template.mod') as $key => $val)
                                    <option value="{{ $key }}" {{ Request::get('type') == ''.$key.'' ? 'selected' : '' }} 
                                        title="{{ Str::replace('_', ' ', $key) }}">{{ Str::replace('_', ' ', $key) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">@lang('global.type')</label>
                                <select class="custom-select" name="type">
                                    <option value=" " selected>@lang('global.show_all')</option>
                                    @foreach (config('cms.module.master.template.type') as $key => $val)
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

        <div class="card">
            <div class="card-header with-elements">
                <h5 class="card-header-title mt-1 mb-0">@lang('master/template.text')</h5>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table card-table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 10px;">#</th>
                            <th>@lang('master/template.label.field1')</th>
                            <th>@lang('master/template.label.field2')</th>
                            <th>@lang('global.type')</th>
                            <th>@lang('master/template.label.field3')</th>
                            <th>@lang('master/template.label.field4')</th>
                            <th style="width: 230px;">@lang('global.created')</th>
                            <th style="width: 230px;">@lang('global.updated')</th>
                            <th class="text-center" style="width: 110px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['templates'] as $item)
                        <tr>
                            <td>{{ $data['no']++ }}</td>
                            <td><strong>{{ $item['name'] }}</strong></td>
                            <td>
                                <span class="badge badge-primary">{{ Str::replace('_', ' ', Str::upper($item['module'])) }}</span>
                            </td>
                            <td><span class="badge badge-info">{{ config('cms.module.master.template.type.'.$item['type']) }}</span></td>
                            <td><code>{{ $item['filepath'] }}</code></td>
                            <td><code>{{ $item['filename'] }}</code></td>
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
                                @can('template_update')
                                <a href="{{ route('template.edit', array_merge(['id' => $item['id']], $queryParam)) }}" class="btn btn-primary icon-btn btn-sm" title="@lang('global.edit_attr', [
                                    'attribute' => __('master/template.caption')
                                ])">
                                    <i class="las la-pen"></i>
                                </a>
                                @endcan
                                @can('template_delete')
                                @if ($item['locked'] == 0)    
                                <button type="button" class="btn btn-danger icon-btn btn-sm swal-delete" title="@lang('global.delete_attr', [
                                    'attribute' => __('master/template.caption')
                                ])"
                                    data-id="{{ $item->id }}">
                                    <i class="las la-trash-alt"></i>
                                </button>
                                @endif
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" align="center">
                                <i>
                                    <strong style="color:red;">
                                    @if ($totalQueryParam > 0)
                                    ! @lang('global.data_attr_not_found', [
                                        'attribute' => __('master/template.caption')
                                    ]) !
                                    @else
                                    ! @lang('global.data_attr_empty', [
                                        'attribute' => __('master/template.caption')
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
                            @lang('pagination.showing') : <strong>{{ $data['templates']->firstItem() }}</strong> - <strong>{{ $data['templates']->lastItem() }}</strong> @lang('pagination.of')
                            <strong>{{ $data['templates']->total() }}</strong>
                        </div>
                        <div class="col-lg-6 m--align-right">
                            {{ $data['templates']->onEachSide(1)->links() }}
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
                        url: '/admin/template/' + id + '/soft',
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
                        text: "@lang('global.alert.delete_success', ['attribute' => __('master/template.caption')])"
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