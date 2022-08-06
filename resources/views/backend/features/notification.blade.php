@extends('layouts.backend.layout')

@section('content')
<!-- Table Defaults -->
<div class="card">
    <div class="card-header">
        <h5 class="my-2">
            @lang('feature/notification.text')
        </h5>
        <div class="box-btn">
            <button type="button" class="btn btn-default w-icon" data-toggle="modal"
                data-target="#modals-slide" title="@lang('global.filter')">
                <i class="fi fi-rr-filter"></i>
                <span>@lang('global.filter')</span>
            </button>
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
                    <th colspan="3">@lang('feature/notification.label.from')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['notifications'] as $item)    
                <tr>
                    <td style="width: 230px;">
                        <a href="{{ route('notification.read', ['id' => $item['id']]) }}" 
                            class="message-sender flex-shrink-1 d-block text-body" title="@lang('global.detail')">
                            <strong>{!! !empty($item['user_from']) ? $item['userFrom']['name'] : __('global.visitor') !!}</strong>
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('notification.read', ['id' => $item['id']]) }}" 
                            class="message-sender flex-shrink-1 d-block text-body" title="@lang('global.detail')">
                            {!! $item->attribute['title'] !!} - <i>{!! $item->attribute['content'] !!}</i>
                        </a>
                    </td>
                    <td style="width: 200px;">{{ $item['created_at']->diffForHumans() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" align="center">
                        <i>
                            <strong class="text-muted">
                                @if ($totalQueryParam > 0)
                                    ! @lang('global.data_attr_not_found', [
                                        'attribute' => __('feature/notification.caption')
                                    ]) !
                                @else
                                    ! @lang('global.data_attr_empty', [
                                        'attribute' => __('feature/notification.caption')
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
    @if ($data['notifications']->total() > 0)
    <div class="card-footer justify-content-center justify-content-lg-between align-items-center flex-wrap">
        <div class="text-muted mb-3 m-lg-0">
            @lang('pagination.showing') 
            <strong>{{ $data['notifications']->firstItem() }}</strong> - 
            <strong>{{ $data['notifications']->lastItem() }}</strong> 
            @lang('pagination.of')
            <strong>{{ $data['notifications']->total() }}</strong>
        </div>
        {{ $data['notifications']->onEachSide(1)->links() }}
    </div>
    @endif
</div>
@endsection