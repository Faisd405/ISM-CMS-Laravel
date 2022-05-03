@extends('layouts.backend.layout')

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
                <h5 class="card-header-title mt-1 mb-0">@lang('feature/notification.text')</h5>
            </div>
            
            {{-- Table --}}
            <div class="table-responsive">
                <table class="table card-table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th colspan="3">@lang('feature/notification.label.from')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['notifications'] as $item)    
                        <tr>
                            <td style="width: 230px;">
                                <a href="{{ route('notification.read', ['id' => $item['id']]) }}" class="message-sender flex-shrink-1 d-block text-body" title="@lang('global.detail')">
                                    <strong>{!! !empty($item['user_from']) ? $item['userFrom']['name'] : __('global.visitor') !!}</strong>
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('notification.read', ['id' => $item['id']]) }}" class="message-sender flex-shrink-1 d-block text-body" title="@lang('global.detail')">
                                    {!! $item->attribute['title'] !!} - <i>{!! $item->attribute['content'] !!}</i>
                                </a>
                            </td>
                            <td style="width: 200px;">{{ $item['created_at']->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" align="center">
                                <i>
                                    <strong style="color:red;">
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
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-lg-6 m--valign-middle">
                            @lang('pagination.showing') : <strong>{{ $data['notifications']->firstItem() }}</strong> - <strong>{{ $data['notifications']->lastItem() }}</strong> @lang('pagination.of')
                            <strong>{{ $data['notifications']->total() }}</strong>
                        </div>
                        <div class="col-lg-6 m--align-right">
                            {{ $data['notifications']->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection