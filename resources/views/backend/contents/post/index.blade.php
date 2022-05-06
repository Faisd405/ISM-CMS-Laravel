@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/libs/select2/select2.css') }}">
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
                    @if (Auth::user()->can('content_categories') && config('cms.module.content.category.active') == true)
                    <a href="{{ route('content.category.index', ['sectionId' => $data['section']['id']]) }}" class="btn btn-primary icon-btn-only-sm btn-sm mr-2" title="@lang('module/content.category.manage')">
                        <i class="las la-list"></i> <span>@lang('module/content.category.manage')</span>
                    </a>
                    @endif
                    @can ('content_post_create')
                    <a href="{{ route('content.post.create', ['sectionId' => $data['section']['id']]) }}" class="btn btn-success icon-btn-only-sm btn-sm mr-2" title="@lang('global.add_attr_new', [
                            'attribute' => __('module/content.post.caption')
                        ])">
                        <i class="las la-plus"></i> <span>@lang('module/content.post.caption')</span>
                    </a>
                    @endcan
                    @role('super')
                    <a href="{{ route('content.post.trash', ['sectionId' => $data['section']['id']]) }}" class="btn btn-secondary icon-btn-only-sm btn-sm" title="@lang('global.trash')">
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
                        @if (config('cms.module.content.category.active') == true)   
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">@lang('module/content.category.caption')</label>
                                <select class="select2 form-select" name="category_id">
                                    <option value=" " selected>@lang('global.show_all')</option>
                                    @foreach ($data['categories'] as $cat)
                                    <option value="{{ $cat['id'] }}" {{ Request::get('category_id') == $cat['id'] ? 'selected' : '' }} 
                                        title="{{ $cat->fieldLang('name') }}">{{ $cat->fieldLang('name') }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
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
                <h5 class="card-header-title mt-1 mb-0">@lang('module/content.post.text') <span class="badge badge-primary">{{ $data['section']->fieldLang('name') }}</span></h5>
            </div>

            <div class="table-responsive">
                <table class="table card-table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 10px;">#</th>
                            <th style="width: 40px;"></th>
                            <th>@lang('module/content.post.label.field1')</th>
                            @if (config('cms.module.content.category.active') == true)   
                            <th style="width: 210px;">@lang('module/content.category.caption')</th>
                            @endif
                            <th class="text-center" style="width: 80px;">@lang('global.hits')</th>
                            <th class="text-center" style="width: 100px;">@lang('global.status')</th>
                            <th style="width: 230px;">@lang('global.created')</th>
                            <th style="width: 230px;">@lang('global.updated')</th>
                            @if ($data['section']['ordering']['order_by'] == 'position')
                            <th class="text-center" style="width: 110px;"></th>
                            @endif
                            <th class="text-center" style="width: 190px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['posts'] as $item)
                        <tr>
                            <td>{{ $data['no']++ }}</td>
                            <td>
                                @can ('content_post_update')
                                <a href="javascript:void(0);" onclick="$(this).find('#form-selected').submit();" class="btn icon-btn btn-sm btn-{{ $item['selected'] == 1 ? 'warning' : 'secondary' }}" title="{{ $item['selected'] == 1 ? 'UNSELECTED' : 'SELECTED'}}">
                                    <i class="las la-star"></i>
                                    <form action="{{ route('content.post.selected', ['sectionId' => $item['section_id'], 'id' => $item['id']]) }}" method="POST" id="form-selected">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                </a>
                                @endcan
                            </td>
                            <td>
                                <strong>{!! Str::limit($item->fieldLang('title'), 65) !!}</strong>
                                @if ($item['config']['is_detail'] == 1)
                                <a href="{{ route('content.post.read.'.$item['section']['slug'], ['slugPost' => $item['slug']]) }}" title="@lang('global.view_detail')" target="_blank">
                                    <i class="las la-external-link-square-alt text-bold" style="font-size: 20px;"></i>
                                </a>
                                @endif
                            </td>
                            @if (config('cms.module.content.category.active') == true)   
                            <td>
                                @if (!empty($item['category_id']))
                                    @foreach ($item->categories() as $cat)
                                    <span class="badge badge-secondary">{{ $cat->fieldLang('name') }}</span>
                                    @endforeach
                                @else
                                {{ __('global.field_empty_attr', [
                                    'attribute' => __('module/content.category.caption')
                                ]) }}
                                @endif
                            </td>
                            @endif
                            <td class="text-center"><span class="badge badge-info">{{ $item['hits'] }}</span></td>
                            <td class="text-center">
                                @can ('content_post_update')
                                <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="badge badge-{{ $item['publish'] == 1 ? 'primary' : 'warning' }}"
                                    title="@lang('global.status')">
                                    {{ __('global.label.publish.'.$item['publish']) }}
                                    <form action="{{ route('content.post.publish', ['sectionId' => $item['section_id'], 'id' => $item['id']]) }}" method="POST">
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
                            @if ($data['section']['ordering']['order_by'] == 'position')
                            <td class="text-center">
                                @if (Auth::user()->can('content_post_update') && $item->where('section_id', $item['section_id'])->min('position') != $item['position'])
                                <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn icon-btn btn-sm btn-dark" title="@lang('global.position')">
                                    <i class="las la-arrow-up"></i>
                                    <form action="{{ route('content.post.position', ['sectionId' => $item['section_id'], 'id' => $item['id'], 'position' => ($item['position'] - 1)]) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                </a>
                                @else
                                <button type="button" class="btn icon-btn btn-sm btn-secondary" title="@lang('global.position')" disabled><i class="las la-arrow-up"></i></button>
                                @endif
                                @if (Auth::user()->can('content_post_update') && $item->where('section_id', $item['section_id'])->max('position') != $item['position'])
                                <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn icon-btn btn-sm btn-dark" title="@lang('global.position')">
                                    <i class="las la-arrow-down"></i>
                                    <form action="{{ route('content.post.position', ['sectionId' => $item['section_id'], 'id' => $item['id'], 'position' => ($item['position'] + 1)]) }}" method="POST">
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
                                @if (Auth::user()->can('medias') && config('cms.module.master.media.active') == true)
                                <a href="{{ route('media.index', ['moduleId' => $item['id'], 'moduleType' => 'content_post']) }}" class="btn icon-btn btn-sm btn-info" title="@lang('master/media.caption')">
                                    <i class="las la-folder"></i>
                                </a>
                                @endif
                                @can('content_post_update')
                                <a href="{{ route('content.post.edit', ['sectionId' => $item['section_id'], 'id' => $item['id']]) }}" class="btn icon-btn btn-sm btn-primary" title="@lang('global.edit_attr', [
                                    'attribute' => __('module/content.post.caption')
                                ])">
                                    <i class="las la-pen"></i>
                                </a>
                                @endcan
                                @can('content_post_delete')
                                <button type="button" class="btn btn-danger icon-btn btn-sm swal-delete" title="@lang('global.delete_attr', [
                                        'attribute' => __('module/content.post.caption')
                                    ])"
                                    data-section-id="{{ $item['section_id'] }}"
                                    data-id="{{ $item['id'] }}">
                                    <i class="las la-trash-alt"></i>
                                </button>
                                @endcan
                                @if (Auth::user()->hasRole('super|support|admin') && config('cms.module.content.post.approval') == true)
                                <a href="javascript:void(0);" onclick="$(this).find('#form-approval').submit();" class="btn icon-btn btn-sm btn-{{ $item['approved'] == 1 ? 'danger' : 'primary' }}" title="{{ $item['approved'] == 1 ? __('global.label.flags.0') : __('global.label.flags.1')}}">
                                    <i class="las la-{{ $item['approved'] == 1 ? 'times' : 'check' }}"></i>
                                    <form action="{{ route('content.post.approved', ['sectionId' => $item['section_id'], 'id' => $item['id']]) }}" method="POST" id="form-approval">
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
                                        'attribute' => __('module/content.post.caption')
                                    ]) !
                                    @else
                                    ! @lang('global.data_attr_empty', [
                                        'attribute' => __('module/content.post.caption')
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
                            @lang('pagination.showing') : <strong>{{ $data['posts']->firstItem() }}</strong> - <strong>{{ $data['posts']->lastItem() }}</strong> @lang('pagination.of')
                            <strong>{{ $data['posts']->total() }}</strong>
                        </div>
                        <div class="col-lg-6 m--align-right">
                            {{ $data['posts']->onEachSide(1)->links() }}
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
            var sectionId = $(this).attr('data-section-id');
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
                        url: '/admin/content/'+sectionId+'/post/'+id+'/soft',
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
                        text: "@lang('global.alert.delete_success', ['attribute' => __('module/content.post.caption')])"
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