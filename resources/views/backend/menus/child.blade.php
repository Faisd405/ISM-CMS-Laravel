@foreach ($childs as $child)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>
        <i>{!! str_repeat('---', $level).' '.Str::limit($child->fieldLang('title'), 65) !!}</i>
    </td>
    <td class="text-center">
        @can ('menu_update')
        <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="badge badge-{{ $child['publish'] == 1 ? 'primary' : 'warning' }}"
            title="@lang('global.status')">
            {{ __('global.label.publish.'.$child['publish']) }}
            <form action="{{ route('menu.publish', ['categoryId' => $child['menu_category_id'], 'id' => $child['id']]) }}" method="POST">
                @csrf
                @method('PUT')
            </form>
        </a>
        @else
        <span class="badge badge-{{ $child['publish'] == 1 ? 'primary' : 'warning' }}">{{ __('global.label.publish.'.$child['publish']) }}</span>
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
    <td class="text-center">
        @if (Auth::user()->can('menu_update') && $child->where('menu_category_id', $child['menu_category_id'])->where('parent', $child['parent'])->min('position') != $child['position'])
        <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn icon-btn btn-sm btn-dark" title="@lang('global.position')">
            <i class="las la-arrow-up"></i>
            <form action="{{ route('menu.position', ['categoryId' => $child['menu_category_id'], 'id' => $child['id'], 'position' => ($child['position'] - 1), 'parent' => $child['parent']]) }}" method="POST">
                @csrf
                @method('PUT')
            </form>
        </a>
        @else
        <button type="button" class="btn icon-btn btn-sm btn-secondary" title="@lang('global.position')" disabled><i class="las la-arrow-up"></i></button>
        @endif
        @if (Auth::user()->can('menu_update') && $child->where('menu_category_id', $child['menu_category_id'])->where('parent', $child['parent'])->max('position') != $child['position'])
        <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn icon-btn btn-sm btn-dark" title="@lang('global.position')">
            <i class="las la-arrow-down"></i>
            <form action="{{ route('menu.position', ['categoryId' => $child['menu_category_id'], 'id' => $child['id'], 'position' => ($child['position'] + 1), 'parent' => $child['parent']]) }}" method="POST">
                @csrf
                @method('PUT')
            </form>
        </a>
        @else
        <button type="button" class="btn icon-btn btn-sm btn-secondary" title="@lang('global.position')" disabled><i class="las la-arrow-down"></i></button>
        @endif
    </td>
    <td class="text-center">
        @can('menu_create')
        <a href="{{ route('menu.create', ['categoryId' => $child['menu_category_id'], 'parent' => $child['id']]) }}" class="btn icon-btn btn-sm btn-success" title="@lang('global.add_attr_new', [
            'attribute' => __('module/menu.caption')
        ])">
            <i class="las la-plus"></i>
        </a>
        @endcan
        @can('menu_update')
        <a href="{{ route('menu.edit', ['categoryId' => $child['menu_category_id'], 'id' => $child['id']]) }}" class="btn icon-btn btn-sm btn-primary" title="@lang('global.edit_attr', [
            'attribute' => __('module/menu.caption')
        ])">
            <i class="las la-pen"></i>
        </a>
        @endcan
        @can('menu_delete')
        <button type="button" class="btn btn-danger icon-btn btn-sm swal-delete" title="@lang('global.delete_attr', [
                'attribute' => __('module/menu.caption')
            ])"
            data-category-id="{{ $child['menu_category_id'] }}"
            data-id="{{ $child['id'] }}">
            <i class="las la-trash-alt"></i>
        </button>
        @endcan
        @if (Auth::user()->hasRole('super') && config('cms.module.menu.approval') == true)
        <a href="javascript:void(0);" onclick="$(this).find('#form-approval').submit();" class="btn icon-btn btn-sm btn-{{ $child['approved'] == 1 ? 'danger' : 'primary' }}" title="{{ $child['approved'] == 1 ? __('global.label.flags.0') : __('global.label.flags.1')}}">
            <i class="las la-{{ $child['approved'] == 1 ? 'times' : 'check' }}"></i>
            <form action="{{ route('menu.approved', ['categoryId' => $child['menu_category_id'], 'id' => $child['id']]) }}" method="POST" id="form-approval">
                @csrf
                @method('PUT')
            </form>
        </a>
        @endif
    </td>
</tr>
@if ($child['childs']->count() > 0)
    @include('backend.pages.child', ['childs' => $child['childs'], 'level' => $level+1])
@endif
@endforeach