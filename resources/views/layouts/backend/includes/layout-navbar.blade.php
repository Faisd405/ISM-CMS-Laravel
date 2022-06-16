<nav class="layout-navbar navbar navbar-expand-lg align-items-lg-center bg-navbar-theme container-p-x" id="layout-navbar">

    <!-- Brand demo (see assets/css/demo/demo.css) -->
    <a href="{{ route('dashboard') }}" class="navbar-brand app-brand demo d-lg-none py-0 mr-0" title="@lang('module/dashboard.caption')">
        <span class="app-brand-text demo font-weight-normal ml-2">@lang('global.backend_panel')</span>
    </a>

    @if (config('cms.setting.layout') == 1)
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#layout-navbar-collapse">
      <span class="navbar-toggler-icon"></span>
    </button>
    @else
    <div class="layout-sidenav-toggle navbar-nav d-lg-none align-items-lg-center mr-3">
        <a class="nav-item nav-link text-large px-0 mr-lg-4" href="javascript:void(0)">
        <i class="las la-bars"></i>
        </a>
    </div>
    @endif

    <div class="navbar-collapse collapse" id="layout-navbar-collapse">
        <!-- Divider -->
        <!-- <hr class="d-lg-none w-100 my-2"> -->
        <!-- Sidenav toggle (see assets/css/demo/demo.css) -->
        <div class="navbar-nav align-items-lg-center d-none d-lg-block">
          <!-- Search -->
          <label class="nav-item navbar-text navbar-search-box p-0 active {{ config('cms.setting.layout') == 1 ? 'pl-2' : '' }}">
            @lang('global.view_frontend') &nbsp;
            <a href="{{ route('home') }}" target="_blank" title="@lang('global.view_frontend')">
              <i class="las la-external-link-alt"></i>
            </a>
          </label>
        </div>

        <div class="navbar-nav align-items-lg-center ml-lg-auto">
          <div class="demo-navbar-notifications nav-item dropdown mr-lg-3" id="notif-bar">
            <a class="nav-link dropdown-toggle hide-arrow" href="#" data-toggle="dropdown" id="click-notif">
              <i class="las la-bell navbar-icon align-middle"></i>
              <span class="badge badge-primary badge-dot indicator" id="notif-dot"></span>
              <span class="d-lg-none align-middle">&nbsp; @lang('feature/notification.title')</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
              <div class="bg-primary text-center text-white font-weight-bold p-3" id="count-new-notif">
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
          <div class="nav-item d-none d-lg-block text-big font-weight-light line-height-1 opacity-25 mr-3 ml-1">|</div>

          <div class="demo-navbar-user nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
              <span class="d-inline-flex flex-lg-row-reverse align-items-center align-middle">
                <img src="{{ Auth::user()->avatars() }}" alt="{!! Auth::user()['name'] !!} photo" class="d-block ui-w-30 rounded-circle">
                <span class="px-1 mr-lg-2 ml-2 ml-lg-0 d-none d-lg-block">{!! Str::limit(Auth::user()['name'], 30) !!}</span>
              </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
              <span class="dropdown-item name-user d-block d-lg-none">{{ Auth::user()['name'] }}</span>
              <a href="{{ route('profile') }}" class="dropdown-item" title="@lang('module/user.profile.title')">
                <i class="las la-user-circle"></i>
                &nbsp; @lang('module/user.profile.title')
              </a>
              <a href="{{ route('notification') }}" class="dropdown-item" title="@lang('feature/notification.caption')">
                <i class="las la-bell"></i>
                &nbsp; @lang('feature/notification.caption')
              </a>
              @can ('configurations')
              <a href="{{ route('configuration.website') }}" class="dropdown-item" title="@lang('feature/configuration.caption')">
                <i class="las la-cog"></i>
                &nbsp; @lang('feature/configuration.caption')
              </a>
              @endcan
              @role ('super')
              <a href="{{ route('cache.clear') }}" class="dropdown-item" title="Clear Cache">
                <i class="las la-cookie-bite"></i>
                &nbsp; Clear Cache
              </a>
              <a href="{{ route('optimize.clear') }}" class="dropdown-item" title="Optimize Clear">
                <i class="las la-cookie"></i>
                &nbsp; Optimize Clear
              </a>
              @endrole
              <div class="dropdown-divider"></div>
              <a href="{{ URL::current() }}" class="dropdown-item" title="Reload">
                <i class="las la-sync-alt"></i>
                &nbsp; Reload
              </a>
              <a href="javascript:void(0)" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                title="@lang('auth.logout.title')">
                <i class="las la-sign-out-alt"></i> &nbsp; @lang('auth.logout.title')
              </a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                  @csrf
              </form>
            </div>
          </div>
        </div>
    </div>

</nav>
