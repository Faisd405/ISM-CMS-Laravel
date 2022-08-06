@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/sweetalert2/sweetalert2.css') }}">
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/fancybox/fancybox.min.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-12 col-lg-12 col-md-12">

        <div class="card">
            <div class="card-header">
                <h5 class="my-2">
                    @lang('module/user.text')
                </h5>
                <div class="box-btn">
                    @can ('user_create')
                    <a href="{{ route('user.create', $queryParam) }}" class="btn btn-main w-icon" title="@lang('global.add_attr_new', [
                        'attribute' => __('module/user.caption')
                        ])">
                        <i class="fi fi-rr-add"></i>
                        <span>@lang('module/user.caption')</span>
                    </a>
                    @endcan
                    <button type="button" class="btn btn-default w-icon" data-toggle="modal"
                        data-target="#modals-slide" title="@lang('global.filter')">
                        <i class="fi fi-rr-filter"></i>
                        <span>@lang('global.filter')</span>
                    </button>
                    @role('developer|super')
                    <a href="{{ route('user.trash') }}" class="btn btn-dark w-icon" title="@lang('global.trash')">
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
                                    <label class="form-label">@lang('module/user.role.caption')</label>
                                    <select class="form-control select2" name="role">
                                        <option value=" " selected>@lang('global.show_all')</option>
                                        @foreach ($data['roles'] as $role)
                                        <option value="{{ $role['id'] }}" {{ $role['id'] == Request::get('role') ? 'selected' : '' }} 
                                            title="{{ $role['name'] }}">{{ Str::upper($role['name']) }}</option>
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
                            <th style="width: 40px;"></th>
                            <th>@lang('module/user.label.name')</th>
                            <th>@lang('module/user.label.email')</th>
                            <th>@lang('module/user.label.username')</th>
                            <th>@lang('module/user.role.caption')</th>
                            <th class="text-center">@lang('global.status')</th>
                            <th style="width: 230px;">@lang('module/user.label.last_activity')</th>
                            <th style="width: 230px;">@lang('global.created')</th>
                            <th style="width: 230px;">@lang('global.updated')</th>
                            <th class="text-center" style="width: 135px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['users'] as $item)
                        <tr>
                            <td>{{ $data['no']++ }} </td>
                            <td>
                                <a href="{{ $item['avatar'] }}" data-fancybox="gallery">
                                    <img src="{{ $item['avatar'] }}" class="d-block ui-w-40 rounded-circle" alt="">
                                </a>
                            </td>
                            <td>
                                <strong>{{ $item['name'] }}</strong><br>
                                @if ($item['is_online'])
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
                                <span class="badge badge-main">{{ Str::upper($role['name']) }}</span>
                                @endforeach
                            </td>
                            <td class="text-center">
                                @if (Auth::user()->can('user_update') && $item['roles'][0]['level'] >= Auth::user()['roles'][0]['level'] && ($item['id'] != Auth::user()['id']))
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
                            <td>
                                <div class="box-btn flex-wrap justify-content-end">
                                    @if ($item->roles[0]['level'] > Auth::user()->roles[0]['level'] && ($item['id'] != Auth::user()['id']))
                                    <a href="javascript:void(0);" onclick="$(this).find('#form-bypass').submit();" class="btn icon-btn btn-sm btn-warning" 
                                        data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="Bypass Login">
                                        <i class="fi fi-rr-sign-in-alt"></i>
                                        <form action="{{ route('user.bypass', array_merge(['id' => $item['id']], $queryParam)) }}" method="POST" id="form-bypass">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                    </a>
                                    @endif
                                    @if (Auth::user()->can('user_update') && $item->roles[0]['level'] > Auth::user()->roles[0]['level'] && ($item['id'] != Auth::user()['id']))
                                    <a href="{{ route('user.edit', array_merge(['id' => $item['id']], $queryParam)) }}" class="btn icon-btn btn-sm btn-success" 
                                        data-toggle="tooltip" data-placement="bottom"
                                        data-original-title="@lang('global.edit_attr', [
                                            'attribute' => __('module/user.caption')
                                        ])">
                                        <i class="fi fi-rr-pencil"></i>
                                    </a>
                                    @endif
                                    @if (Auth::user()->can('user_delete') && $item->roles[0]['level'] > Auth::user()->roles[0]['level'] && ($item['id'] != Auth::user()['id']))
                                        @if ($item['locked'] == 0)
                                        <button type="button" class="btn icon-btn btn-sm btn-danger swal-delete" 
                                            data-id="{{ $item['id'] }}"
                                            data-toggle="tooltip" data-placement="bottom"
                                            data-original-title="@lang('global.delete_attr', [
                                                    'attribute' => __('module/user.caption')
                                                ])">
                                            <i class="fi fi-rr-trash"></i>
                                        </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" align="center">
                                <i>
                                    <strong class="text-muted">
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
            </div>
            @if ($data['users']->total() > 0)
            <div class="card-footer justify-content-center justify-content-lg-between align-items-center flex-wrap">
                <div class="text-muted mb-3 m-lg-0">
                    @lang('pagination.showing') 
                    <strong>{{ $data['users']->firstItem() }}</strong> - 
                    <strong>{{ $data['users']->lastItem() }}</strong> 
                    @lang('pagination.of')
                    <strong>{{ $data['users']->total() }}</strong>
                </div>
                {{ $data['users']->onEachSide(1)->links() }}
            </div>
            @endif
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/backend/js/ui_tooltips.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
<script src="{{ asset('assets/backend/vendor/libs/fancybox/fancybox.min.js') }}"></script>
@endsection

@section('jsbody')
<script>
    //select2
    $(function () {
        $('.select2').select2({
            dropdownParent: $("#modals-slide")
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