@foreach ($childs as $child)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>
        <i>{!! str_repeat('---', $level).' '.Str::limit($child['module_data']['title'], 65) !!}</i>
        @if ($child['approved'] != 1)
        <br>
        <small class="form-text text-danger">@lang('global.approval_info')</small>
        @endif
    </td>
    <td class="text-center">
        @can ('menu_update')
        <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="badge badge-{{ $child['publish'] == 1 ? 'main' : 'warning' }}"
            title="@lang('global.status')">
            {{ __('global.label.publish.'.$child['publish']) }}
            <form action="{{ route('menu.publish', ['categoryId' => $child['menu_category_id'], 'id' => $child['id']]) }}" method="POST">
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
        <div class="box-btn flex-wrap justify-content-center">
            @if (Auth::user()->can('menu_update') && $child->where('menu_category_id', $child['menu_category_id'])->where('parent', $child['parent'])->min('position') != $child['position'])
            <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn icon-btn btn-sm btn-dark" title="@lang('global.position')">
                <i class="fi fi-rr-arrow-small-up"></i>
                <form action="{{ route('menu.position', ['categoryId' => $child['menu_category_id'], 'id' => $child['id'], 'position' => ($child['position'] - 1), 'parent' => $child['parent']]) }}" method="POST">
                    @csrf
                    @method('PUT')
                </form>
            </a>
            @else
            <button type="button" class="btn icon-btn btn-sm btn-secondary" title="@lang('global.position')" disabled>
                <i class="fi fi-rr-arrow-small-up"></i>
            </button>
            @endif
            @if (Auth::user()->can('menu_update') && $child->where('menu_category_id', $child['menu_category_id'])->where('parent', $child['parent'])->max('position') != $child['position'])
            <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn icon-btn btn-sm btn-dark" title="@lang('global.position')">
                <i class="fi fi-rr-arrow-small-down"></i>
                <form action="{{ route('menu.position', ['categoryId' => $child['menu_category_id'], 'id' => $child['id'], 'position' => ($child['position'] + 1), 'parent' => $child['parent']]) }}" method="POST">
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
    <td>
        <div class="box-btn flex-wrap justify-content-end">
            @can('menu_create')
            @if (Auth::user()->hasRole('developer|super') || !Auth::user()->hasRole('developer|super') && $child['config']['create_child'] == true)  
            <a href="{{ route('menu.create', ['categoryId' => $child['menu_category_id'], 'parent' => $child['id']]) }}" class="btn icon-btn btn-sm btn-main" title="@lang('global.add_attr_new', [
                'attribute' => __('module/menu.caption')
            ])">
                <i class="fi fi-rr-add"></i>
            </a>
            @endif
            @endcan
            @can('menu_update')
            @if (Auth::user()->hasRole('super') || !Auth::user()->hasRole('super') && $child['config']['edit_public_menu'] == true)
            <a href="{{ route('menu.edit', ['categoryId' => $child['menu_category_id'], 'id' => $child['id']]) }}" class="btn icon-btn btn-sm btn-success" title="@lang('global.edit_attr', [
                'attribute' => __('module/menu.caption')
            ])">
                <i class="fi fi-rr-pencil"></i>
            </a>
            @endif
            @endcan
            @can('menu_delete')
                @if ($child['locked'] == 0)
                <button type="button" class="btn btn-danger icon-btn btn-sm swal-delete" title="@lang('global.delete_attr', [
                        'attribute' => __('module/menu.caption')
                    ])"
                    data-category-id="{{ $child['menu_category_id'] }}"
                    data-id="{{ $child['id'] }}">
                    <i class="fi fi-rr-trash"></i>
                </button>
                @endif
            @endcan
            @if (Auth::user()->hasRole('developer|super') && config('cms.module.menu.approval') == true)
            <a href="javascript:void(0);" onclick="$(this).find('#form-approval').submit();" class="btn icon-btn btn-sm btn-default" 
                title="{{ $child['approved'] == 1 ? __('global.label.flags.0') : __('global.label.flags.1')}}">
                <i class="fi fi-rr-{{ $child['approved'] == 1 ? 'ban text-danger' : 'check text-success' }}"></i>
                <form action="{{ route('menu.approved', ['categoryId' => $child['menu_category_id'], 'id' => $child['id']]) }}" method="POST" id="form-approval">
                    @csrf
                    @method('PUT')
                </form>
            </a>
            @endif
        </div>
    </td>
</tr>
@if ($child['childs']->count() > 0)
    @include('backend.menus.child', ['childs' => $child->childs()->orderBy('position', 'ASC')->get(), 'level' => $level+1])
@endif
@endforeach