@foreach ($childs as $child)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>
        <i>{!! str_repeat('---', $level).' '.Str::limit($child->fieldLang('title'), 60) !!}</i>
        @if ($child['detail'] == 1)
        <a href="{{ route('page.read.child.'.$child['slug']) }}" title="@lang('global.view_detail')" target="_blank">
            <i class="fi fi-rr-link text-bold" style="font-size: 14px;"></i>
        </a>
        @endif
        @if ($child['approved'] != 1)
        <br>
        <small class="form-text text-danger">@lang('global.approval_info')</small>
        @endif
    </td>
    <td class="text-center"><span class="badge badge-info">{{ $child['hits'] }}</span></td>
    <td class="text-center">
        @can ('page_update')
        <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="badge badge-{{ $child['publish'] == 1 ? 'main' : 'warning' }}"
            title="@lang('global.status')">
            {{ __('global.label.publish.'.$child['publish']) }}
            <form action="{{ route('page.publish', ['id' => $child['id']]) }}" method="POST">
                @csrf
                @method('PUT')
            </form>
        </a>
        @else
        <span class="badge badge-{{ $child['publish'] == 1 ? 'main' : 'warning' }}">{{ __('global.label.publish.'.$child['publish']) }}</span>
        @endcan
    </td>
    <td>
        {{ $child['created_at']->format('d F Y (H:i A)') }}
        @if (!empty($child['created_by']))
            <br>
           <span class="text-muted">@lang('global.by') : {{ $child['createBy'] != null ? $child['createBy']['name'] : 'User Deleted' }}</span>
        @endif
    </td>
    <td>
        {{ $child['updated_at']->format('d F Y (H:i A)') }}
        @if (!empty($child['updated_by']))
            <br>
            <span class="text-muted">@lang('global.by') : {{ $child['updateBy'] != null ? $child['updateBy']['name'] : 'User Deleted' }}</span>
        @endif
    </td>
    <td>
        @if ($child->getParent()['config']['child_order_by'] == 'position')
        <div class="box-btn flex-wrap justify-content-center">
            @if (Auth::user()->can('page_update') && $child->where('parent', $child['parent'])->min('position') != $child['position'])
            <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn icon-btn btn-sm btn-dark" 
                data-toggle="tooltip" data-placement="bottom"
                data-original-title="@lang('global.position')">
                <i class="fi fi-rr-arrow-small-up"></i>
                <form action="{{ route('page.position', ['id' => $child['id'], 'position' => ($child['position'] - 1), 'parent' => $child['parent']]) }}" method="POST">
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
            @if (Auth::user()->can('page_update') && $child->where('parent', $child['parent'])->max('position') != $child['position'])
            <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn icon-btn btn-sm btn-dark" 
                data-toggle="tooltip" data-placement="bottom"
                data-original-title="@lang('global.position')">
                <i class="fi fi-rr-arrow-small-down"></i>
                <form action="{{ route('page.position', ['id' => $child['id'], 'position' => ($child['position'] + 1), 'parent' => $child['parent']]) }}" method="POST">
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
        @endif
    </td>
    <td>
        <div class="box-btn flex-wrap justify-content-end">
            @can('page_create')
                @if (Auth::user()->hasRole('developer|super') || $child['config']['create_child'] == true)
                <a href="{{ route('page.create', ['parent' => $child['id']]) }}" class="btn icon-btn btn-sm btn-main" 
                    data-toggle="tooltip" data-placement="bottom"
                    data-original-title="@lang('global.add_attr_new', [
                    'attribute' => __('module/page.caption')
                ])">
                    <i class="fi fi-rr-plus"></i>
                </a>
                @endif
            @endcan
            @if (Auth::user()->hasRole('developer|super') || Auth::user()->can('medias') && config('cms.module.master.media.active') == true  && $child['config']['show_media'] == true)
            <a href="{{ route('media.index', ['moduleId' => $child['id'], 'moduleType' => 'page']) }}" class="btn icon-btn btn-sm btn-main" 
                data-toggle="tooltip" data-placement="bottom"
                data-original-title="@lang('master/media.caption')">
                <i class="fi fi-rr-add-folder"></i>
            </a>
            @endif
            @can('page_update')
            <a href="{{ route('page.edit', ['id' => $child['id']]) }}" class="btn icon-btn btn-sm btn-success" 
                data-toggle="tooltip" data-placement="bottom"
                data-original-title="@lang('global.edit_attr', [
                'attribute' => __('module/page.caption')
            ])">
                <i class="fi fi-rr-pencil"></i>
            </a>
            @endcan
            @can('page_delete')
                @if ($child['locked'] == 0)
                <button type="button" class="btn btn-danger icon-btn btn-sm swal-delete" 
                    data-toggle="tooltip" data-placement="bottom"
                    data-original-title="@lang('global.delete_attr', [
                        'attribute' => __('module/page.caption')
                    ])"
                    data-id="{{ $child['id'] }}">
                    <i class="fi fi-rr-trash"></i>
                </button>
                @endif
            @endcan
            @if (Auth::user()->hasRole('developer|super') && config('cms.module.page.approval') == true)
            <a href="javascript:void(0);" onclick="$(this).find('#form-approval').submit();" class="btn icon-btn btn-sm btn-default" 
                data-toggle="tooltip" data-placement="bottom"
                data-original-title="{{ $child['approved'] == 1 ? __('global.label.flags.0') : __('global.label.flags.1')}}">
                <i class="fi fi-rr-{{ $child['approved'] == 1 ? 'ban text-danger' : 'check text-success' }}"></i>
                <form action="{{ route('page.approved', ['id' => $child['id']]) }}" method="POST" id="form-approval">
                    @csrf
                    @method('PUT')
                </form>
            </a>
            @endif
        </div>
    </td>
</tr>
@if ($child['childs']->count() > 0)
    @include('backend.pages.child', ['childs' => $child->childs()->orderBy($child['config']['child_order_by'], $child['config']['child_order_type'])->get(), 'level' => $level+1])
@endif
@endforeach