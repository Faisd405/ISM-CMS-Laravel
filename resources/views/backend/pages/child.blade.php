@foreach ($childs as $child)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>
        <i>{!! str_repeat('---', $level).' '.Str::limit($child->fieldLang('title'), 60) !!}</i>
        @if ($child['detail'] == 1)
        <a href="{{ route('page.read.'.$child['slug']) }}" title="@lang('global.view_detail')" target="_blank">
            <i class="las la-external-link-square-alt text-bold" style="font-size: 20px;"></i>
        </a>
        @endif
    </td>
    <td class="text-center"><span class="badge badge-info">{{ $child['hits'] }}</span></td>
    <td class="text-center">
        @can ('page_update')
        <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="badge badge-{{ $child['publish'] == 1 ? 'primary' : 'warning' }}"
            title="@lang('global.status')">
            {{ __('global.label.publish.'.$child['publish']) }}
            <form action="{{ route('page.publish', ['id' => $child['id']]) }}" method="POST">
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
        @if ($item['config']['child_order_by'] == 'position')
        @if (Auth::user()->can('page_update') && $child->where('parent', $child['parent'])->min('position') != $child['position'])
        <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn icon-btn btn-sm btn-dark" title="@lang('global.position')">
            <i class="las la-arrow-up"></i>
            <form action="{{ route('page.position', ['id' => $child['id'], 'position' => ($child['position'] - 1), 'parent' => $child['parent']]) }}" method="POST">
                @csrf
                @method('PUT')
            </form>
        </a>
        @else
        <button type="button" class="btn icon-btn btn-sm btn-secondary" title="@lang('global.position')" disabled><i class="las la-arrow-up"></i></button>
        @endif
        @if (Auth::user()->can('page_update') && $child->where('parent', $child['parent'])->max('position') != $child['position'])
        <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn icon-btn btn-sm btn-dark" title="@lang('global.position')">
            <i class="las la-arrow-down"></i>
            <form action="{{ route('page.position', ['id' => $child['id'], 'position' => ($child['position'] + 1), 'parent' => $child['parent']]) }}" method="POST">
                @csrf
                @method('PUT')
            </form>
        </a>
        @else
        <button type="button" class="btn icon-btn btn-sm btn-secondary" title="@lang('global.position')" disabled><i class="las la-arrow-down"></i></button>
        @endif
        @endif
    </td>
    <td class="text-center">
        @can('page_create')
        @if (Auth::user()->hasRole('developer|super') || $child['config']['create_child'] == true)
        <a href="{{ route('page.create', ['parent' => $child['id']]) }}" class="btn icon-btn btn-sm btn-success" title="@lang('global.add_attr_new', [
            'attribute' => __('module/page.caption')
        ])">
            <i class="las la-plus"></i>
        </a>
        @endif
        @endcan
        @if (Auth::user()->hasRole('developer|super') || Auth::user()->can('medias') && config('cms.module.master.media.active') == true  && $child['config']['show_media'] == true)
        <a href="{{ route('media.index', ['moduleId' => $child['id'], 'moduleType' => 'page']) }}" class="btn icon-btn btn-sm btn-info" title="@lang('master/media.caption')">
            <i class="las la-folder"></i>
        </a>
        @endif
        @can('page_update')
        <a href="{{ route('page.edit', ['id' => $child['id']]) }}" class="btn icon-btn btn-sm btn-primary" title="@lang('global.edit_attr', [
            'attribute' => __('module/page.caption')
        ])">
            <i class="las la-pen"></i>
        </a>
        @endcan
        @can('page_delete')
        @if ($child['locked'] == 0)
        <button type="button" class="btn btn-danger icon-btn btn-sm swal-delete" title="@lang('global.delete_attr', [
                'attribute' => __('module/page.caption')
            ])"
            data-id="{{ $child['id'] }}">
            <i class="las la-trash-alt"></i>
        </button>
        @endif
        @endcan
        @if (Auth::user()->hasRole('developer|super') && config('cms.module.page.approval') == true)
        <a href="javascript:void(0);" onclick="$(this).find('#form-approval').submit();" class="btn icon-btn btn-sm btn-{{ $child['approved'] == 1 ? 'danger' : 'primary' }}" title="{{ $child['approved'] == 1 ? __('global.label.flags.0') : __('global.label.flags.1')}}">
            <i class="las la-{{ $child['approved'] == 1 ? 'times' : 'check' }}"></i>
            <form action="{{ route('page.approved', ['id' => $child['id']]) }}" method="POST" id="form-approval">
                @csrf
                @method('PUT')
            </form>
        </a>
        @endif
    </td>
</tr>
@if ($child['childs']->count() > 0)
    @include('backend.pages.child', ['childs' => $child->childs()->orderBy($child['config']['child_order_by'], $child['config']['child_order_type'])->get(), 'level' => $level+1])
@endif
@endforeach