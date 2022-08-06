@isset($breadcrumbs)
<!-- Breadcrumb -->
<div class="d-flex align-items-center justify-content-between flex-wrap mb-lg-3">
    <div>
        @isset ($routeBack)
        <a href="{{ $routeBack }}" class="btn btn-sm btn-outline-secondary w-icon" title="@lang('global.back')">
            <i class="fi fi-rr-arrow-left"></i> <span>@lang('global.back')</span>
        </a>
        @endisset
        <h4 class="font-weight-bold py-3 m-0">
            {!! $title !!}
        </h4>
    </div>
    <ol class="breadcrumb my-lg-3">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}" title="@lang('module/dashboard.caption')">@lang('module/dashboard.caption')</a>
        </li>
        @foreach ($breadcrumbs as $key => $val)
        <li class="breadcrumb-item {{ empty($val) ? 'active' : '' }}">
            <a href="{{ $val }}" title="{{ $key }}">{{ Str::limit($key, 15) }}</a>
        </li>
        @endforeach
    </ol>
</div>
@endisset
