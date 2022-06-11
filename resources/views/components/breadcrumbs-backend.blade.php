@isset($breadcrumbs)
<div class="d-flex justify-content-between align-items-center w-100 mb-2 border-bottom">
    <div class="d-flex align-items-center">
        @isset ($routeBack)
        <a href="{{ $routeBack }}" 
            class="btn py-2 rounded-0 btn btn-outline-default bg-light border-right d-inline-block borderless text-muted text-nowrap btn-secondary" 
            title="@lang('global.back')">
            <span class="ion ion-ios-arrow-back"></span>&nbsp; @lang('global.back')
        </a>
        @endisset
        <h5 class="p-2 pl-4 m-0 d-inline-block text-nowrap font-weight-normal">
            {!! $title !!}
        </h5>
    </div>
    <div class="p-2 pr-4 text-right bread-right">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}" title="@lang('module/dashboard.caption')">@lang('module/dashboard.caption')</a>
            </li>
            @foreach ($breadcrumbs as $key => $val)
            <li class="breadcrumb-item {{ empty($val) ? 'active' : '' }}">
                <a href="{{ $val }}" title="{{ $key }}">{{ $key }}</a>
            </li>
            @endforeach
        </ol>
    </div>
</div>
@endisset
