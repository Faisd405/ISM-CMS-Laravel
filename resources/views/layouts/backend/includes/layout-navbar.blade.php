<nav class="layout-navbar navbar navbar-expand align-items-lg-center container-p-x" id="layout-navbar">

    <!-- Brand demo (see assets/css/demo/demo.css) -->
    <div class="navbar-brand app-brand demo d-lg-none py-0 mr-4">
        <!-- Sidenav toggle (see assets/css/demo/demo.css) -->
        <div class="layout-sidenav-toggle navbar-nav align-items-lg-center mr-auto">
            <a class="nav-item nav-link" href="javascript:void(0)">
                <i class="ion ion-md-menu align-middle"></i>
            </a>
        </div>
        <span class="app-brand-logo demo{{ config('cms.setting.layout') != 3 ? ' square' : '' }}">
            <img src="{{ config('cms.setting.layout') == 3 ? config('cmsConfig.file.logo') :  config('cmsConfig.file.logo_2') }}" alt="{{ config('cmsConfig.general.website_name') }}"
                title="{{ config('cmsConfig.general.website_name') }}">
        </span>
        <!-- Divider -->
        <div
            class="nav-item text-big font-weight-light line-height-1 d-none d-sm-block opacity-50 mr-2 ml-3">
            |</div>
        <span class="app-brand-text d-none d-sm-block demo font-weight-normal ml-2">
            <a href="{{ route('home') }}" target="_blank" title="@lang('global.view_frontend')">
                @lang('global.view_frontend') <i class="fi fi-rr-arrow-circle-right" style="font-size: 0.8em;"></i>
            </a>
        </span>
    </div>

    <button class="navbar-toggler" type="button" data-toggle="collapse"
        data-target="#layout-navbar-collapse">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="navbar-collapse collapse" id="layout-navbar-collapse">
        <!-- Divider -->
        <hr class="d-none w-100 my-2">

        <div class="navbar-nav align-items-center ml-auto">

            @if (config('cms.module.feature.notification.active') == true)
            <div class="demo-navbar-notifications nav-item dropdown mr-lg-2" id="notif-bar">
                <a class="nav-link dropdown-toggle hide-arrow" href="#" data-toggle="dropdown" id="click-notif">
                    <i class="fi fi-rr-bell"></i>
                    <span class="badge badge-main badge-dot indicator" id="notif-dot"></span>
                    <span class="d-none align-middle">&nbsp; @lang('feature/notification.title')</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="bg-main text-center text-white font-weight-bold p-3" id="count-new-notif">
                        @lang('feature/notification.label.new_notif')
                    </div>
                    <div class="list-group list-group-flush" id="list-notification">
                        
                    </div>

                    <a href="{{ route('notification') }}" class="d-block text-center text-light small p-2 my-1" title="@lang('global.view_all')">
                        @lang('global.view_all')
                    </a>
                </div>
            </div>

            <!-- Divider -->
            <div class="nav-item text-big font-weight-light line-height-1 opacity-75 mr-2 ml-1">|</div>
            @endif

            <div class="demo-navbar-user nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                    <span class="d-inline-flex flex-lg-row-reverse align-items-center align-middle">
                        <div class="d-block ui-w-30 rounded-circle overflow-hidden box-avatar">
                            <img src="{{ Auth::user()['avatar'] }}" alt="{!! Auth::user()['name'] !!} photo">
                        </div>
                        <div class="px-1 mr-lg-2 ml-2 ml-lg-0 d-none d-lg-block text-right line-height-1">
                            <small class="text-muted mb-0">{{ Auth::user()->roles->count() > 0 ? Auth::user()->roles[0]['name'] : 'No Role' }}</small>
                            <span class="font-weight-bold d-block">{!! Str::limit(Auth::user()['name'], 30) !!}</span>
                        </div>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="{{ route('profile') }}" class="dropdown-item" title="@lang('module/user.profile.title')">
                        <i class="fi fi-rr-man-head text-primary"></i> @lang('module/user.profile.title')
                    </a>
                    @if (config('cms.module.feature.notification.active') == true)
                    <a href="{{ route('notification') }}" class="dropdown-item" title="@lang('feature/notification.caption')">
                        <i class="fi fi-rr-bell text-warning"></i> @lang('feature/notification.caption')
                    </a>
                    @endif
                    @can ('configurations')
                    <a href="{{ route('configuration.website') }}" class="dropdown-item" title="@lang('feature/configuration.caption')">
                        <i class="fi fi-rr-settings-sliders text-dark"></i> @lang('feature/configuration.caption')
                    </a>
                    @endcan
                    @role ('developer|super')
                    <a href="{{ route('cache.clear') }}" class="dropdown-item" title="Clear Cache">
                        <i class="fi fi-rr-cookie text-secondary"></i> Clear Cache
                    </a>
                    <a href="{{ route('optimize.clear') }}" class="dropdown-item" title="Optimize Clear">
                        <i class="fi fi-rr-rocket-lunch text-main"></i> Optimize Clear
                    </a>
                    @endrole
                    <a href="{{ URL::current() }}" class="dropdown-item" title="Reload">
                        <i class="fi fi-rr-refresh text-info"></i> Reload
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="javascript:void(0)" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        title="@lang('auth.logout.title')">
                        <i class="fi fi-rr-sign-out-alt text-danger"></i> @lang('auth.logout.title')
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>