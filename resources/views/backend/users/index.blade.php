@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/sweetalert2/sweetalert2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/fancybox/fancybox.min.css') }}">
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
                    @can ('user_create')
                    <a href="{{ route('user.create', $queryParam) }}" class="btn btn-success icon-btn-only-sm btn-sm mr-2" title="@lang('global.add_attr_new', [
                            'attribute' => __('module/user.caption')
                        ])">
                        <i class="las la-plus"></i> <span>@lang('module/user.caption')</span>
                    </a>
                    @endcan
                    @role('super')
                    <a href="{{ route('user.trash') }}" class="btn btn-secondary icon-btn-only-sm btn-sm" title="@lang('global.trash')">
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
                                <label class="form-label">@lang('module/user.role.caption')</label>
                                <select class="form-control select2" name="role">
                                    <option value=" " selected>@lang('global.show_all')</option>
                                    @foreach ($data['roles'] as $role)
                                    <option value="{{ $role['id'] }}" {{ $role['id'] == Request::get('role') ? 'selected' : '' }} 
                                        title="{{ $role['name'] }}">{{ Str::upper($role['name']) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">@lang('global.status')</label>
                                <select class="custom-select" name="status">
                                    <option value=" " selected>@lang('global.show_all')</option>
                                    @foreach (__('global.label.active') as $key => $val)
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
                <h5 class="card-header-title mt-1 mb-0">@lang('module/user.text')</h5>
            </div>

            <div class="table-responsive">
                <table class="table card-table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 10px;">#</th>
                            <th style="width: 40px;"></th>
                            <th>@lang('module/user.label.field1')</th>
                            <th>@lang('module/user.label.field2')</th>
                            <th>@lang('module/user.label.field3')</th>
                            <th>@lang('module/user.role.caption')</th>
                            <th class="text-center">@lang('global.status')</th>
                            <th style="width: 230px;">@lang('module/user.label.last_activity')</th>
                            <th style="width: 230px;">@lang('global.created')</th>
                            <th style="width: 230px;">@lang('global.updated')</th>
                            <th class="text-center" style="width: 140px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['users'] as $item)
                        <tr>
                            <td>{{ $data['no']++ }} </td>
                            <td>
                                <a href="{{ $item->avatars() }}" data-fancybox="gallery">
                                    <img src="{{ $item->avatars() }}" class="d-block ui-w-40 rounded-circle" alt="">
                                </a>
                            </td>
                            <td>
                                <strong>{{ $item['name'] }}</strong><br>
                                @if (Cache::has('online-'.$item['id']))
                                <div class="chat-status small text-muted"><span class="badge badge-dot badge-success"></span>&nbsp; Online</div>
                                @else
                                <div class="chat-status small text-muted"><span class="badge badge-dot badge-danger"></span>&nbsp; Offline</div>
                                @endif
                            </td>
                            <td>
                                <a href="mailto:{{ $item['email'] }}">{{ $item['email'] }}</a>
                            </td>
                            <td>{{ $item['username'] }}</td>
                            <td>
                                @foreach ($item['roles'] as $role)
                                <span class="badge badge-primary">{{ Str::upper($role['name']) }}</span>
                                @endforeach
                            </td>
                            <td class="text-center">
                                @if (Auth::user()->can('user_update') && $item->roles[0]['id'] >= Auth::user()->roles[0]['id'] && ($item['id'] != Auth::user()['id']))
                                <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="badge badge-{{ $item['active'] == 1 ? 'success' : 'secondary' }}"
                                    title="{{ __('global.label.active.'.$item['active']) }}">
                                    {{ __('global.label.active.'.$item['active']) }}
                                    <form action="{{ route('user.activate', array_merge(['id' => $item->id], $queryParam)) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                </a>
                                @else
                                <span class="badge badge-{{ $item['active'] == 1 ? 'success' : 'secondary' }}">{{ __('global.label.active.'.$item['active']) }}</span>
                                @endif
                            </td>
                            <td>{{ !empty($item['session']) && !empty($item['session']['last_activity']) ? $item['session']['last_activity']->format('d F Y (H:i A)') : __('module/user.label.no_activity') }}</td>
                            <td>
                                {{ $item['created_at']->format('d F Y (H:i A)') }}
                                @if (!empty($item['created_by']))
                                <br>
                                <span class="text-muted"> @lang('global.by') : {{ $item->createBy() != null ? $item->createBy()['name'] : 'User Deleted' }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $item['updated_at']->format('d F Y (H:i A)') }}
                                @if (!empty($item['updated_by']))
                                <br>
                                <span class="text-muted"> @lang('global.by') : {{ $item->updateBy() != null ? $item->updateBy()['name'] : 'User Deleted' }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($item->roles[0]['id'] > Auth::user()->roles[0]['id'] && ($item['id'] != Auth::user()['id']))
                                <a href="javascript:void(0);" onclick="$(this).find('#form-bypass').submit();" class="btn btn-warning icon-btn btn-sm" title="Bypass Login">
                                    <i class="las la-sign-in-alt"></i>
                                    <form action="{{ route('user.bypass', array_merge(['id' => $item['id']], $queryParam)) }}" method="POST" id="form-bypass">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                </a>
                                @endif
                                @if (Auth::user()->can('user_update') && $item->roles[0]['id'] > Auth::user()->roles[0]['id'] && ($item['id'] != Auth::user()['id']))
                                <a href="{{ route('user.edit', array_merge(['id' => $item['id']], $queryParam)) }}" class="btn btn-primary icon-btn btn-sm" title="@lang('global.edit_attr', [
                                    'attribute' => __('module/user.caption')
                                ])">
                                    <i class="las la-pen"></i>
                                </a>
                                @endif
                                @if (Auth::user()->can('user_delete') && $item->roles[0]['id'] > Auth::user()->roles[0]['id'] && ($item['id'] != Auth::user()['id']))
                                <button type="button" data-id="{{ $item['id'] }}" class="btn icon-btn btn-sm btn-danger swal-delete" title="@lang('global.delete_attr', [
                                    'attribute' => __('module/user.caption')
                                    ])">
                                    <i class="las la-trash-alt"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" align="center">
                                <i>
                                    <strong style="color:red;">
                                    @if ($totalQueryParam > 0)
                                        ! @lang('global.data_attr_not_found', [
                                            'attribute' => __('module/user.caption')
                                        ]) !
                                    @else
                                        ! @lang('global.data_attr_empty', [
                                            'attribute' => __('module/user.caption')
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
                            @lang('pagination.showing') : <strong>{{ $data['users']->firstItem() }}</strong> - <strong>{{ $data['users']->lastItem() }}</strong> @lang('pagination.of')
                            <strong>{{ $data['users']->total() }}</strong>
                        </div>
                        <div class="col-lg-6 m--align-right">
                            {{ $data['users']->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/backend/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
<script src="{{ asset('assets/backend/fancybox/fancybox.min.js') }}"></script>
@endsection

@section('jsbody')
<script>
    //select2
    $(function () {
        $('.select2').select2();
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
                        url: '/admin/user/'+id+'/soft',
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
                        text: "@lang('global.alert.delete_success', ['attribute' => __('module/user.caption')])"
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