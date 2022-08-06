<div id="layout-sidenav" class="layout-sidenav{{ config('cms.setting.layout') == 3 ? '-horizontal' : '' }} sidenav sidenav-{{ config('cms.setting.layout') == 3 ? 'horizontal' : 'vertical' }}">

    @if (config('cms.setting.layout') != 3)    
    <!-- Brand demo (see assets/css/demo/demo.css) -->
    <div class="app-brand demo">
        @if (config('cms.setting.layout') == 2)
        <a href="{{ route('dashboard') }}" class="app-brand-logo demo">
            <img src="{{ config('cmsConfig.logo') }}" alt="{{ config('cmsConfig.website_name') }}">
        </a>
        <a href="{{ route('dashboard') }}" class="app-brand-text demo sidenav-text">{{ config('cmsConfig.website_name') }}</a>
        @endif
        <a href="javascript:void(0)" class="layout-sidenav-toggle sidenav-link ml-auto">
            <i class="fi fi-rr-caret-left"></i>
        </a>
    </div>
    @endif


    <!-- Links -->
    <ul class="sidenav-inner{{ config('cms.setting.layout') != 3 ? ' py-1' : '' }}">

        <!-- Dashboard -->
        <li class="sidenav-item{{ Request::is('admin/dashboard') ? ' active' : '' }}">
            <a href="{{ route('dashboard') }}" class="sidenav-link" title="@lang('module/dashboard.caption')">
                <div class="sidenav-icon">
                    <i class="fi fi-rr-dashboard"></i>
                </div>
                <div>@lang('module/dashboard.caption')</div>
            </a>
        </li>

        @if (Auth::user()->can('users') && config('cms.module.user.active') == true)
        <!-- Users -->
        <li class="sidenav-item{{ (Request::is('admin/acl*') || Request::is('admin/user*')) ? ' active open' : '' }}">
            <a href="javascript:void(0)" class="sidenav-link sidenav-toggle" title="@lang('module/user.user_management_caption')">
                <div class="sidenav-icon">
                    <i class="fi fi-rr-users-alt"></i>
                </div>
                <div>@lang('module/user.user_management_caption')</div>
            </a>

            <ul class="sidenav-menu">
                @role ('developer|super')
                <!-- ACL -->
                <li class="sidenav-item{{ Request::is('admin/acl*') ? ' active open' : '' }}">
                    <a href="javascript:void(0)" class="sidenav-link sidenav-toggle" title="@lang('module/user.acl_caption')">
                        <div>@lang('module/user.acl_caption')</div>
                    </a>
                    <ul class="sidenav-menu">
                        <li class="sidenav-item{{ Request::is('admin/acl/role*') ? ' active' : '' }}">
                            <a href="{{ route('role.index') }}" class="sidenav-link" title="@lang('module/user.role.caption')">
                                <div>@lang('module/user.role.caption')</div>
                            </a>
                        </li>
                        <li class="sidenav-item{{ Request::is('admin/acl/permission*') ? ' active' : '' }}">
                            <a href="{{ route('permission.index') }}" class="sidenav-link" title="@lang('module/user.permission.caption')">
                                <div>@lang('module/user.permission.caption')</div>
                            </a>
                        </li>
                    </ul>
                </li>
                @endrole
                <li class="sidenav-item{{ (Request::is('admin/user*') && Request::segment(3) != 'log' && Request::segment(3) != 'login-failed') ? ' active' : '' }}">
                    <a href="{{ route('user.index') }}" class="sidenav-link" title="@lang('module/user.caption')">
                        <div>@lang('module/user.caption')</div>
                    </a>
                </li>
                <li class="sidenav-item{{ Request::is('admin/user/log*') && Request::segment(3) != 'login-failed' ? ' active' : '' }}">
                    <a href="{{ route('user.log') }}" class="sidenav-link" title="@lang('module/user.log.caption')">
                        <div>@lang('module/user.log.caption')</div>
                    </a>
                </li>
                @role('developer|super')
                <li class="sidenav-item{{ Request::is('admin/user/login-failed*') ? ' active' : '' }}">
                    <a href="{{ route('user.login-failed') }}" class="sidenav-link" title="@lang('module/user.login_failed.caption')">
                        <div>@lang('module/user.login_failed.caption')</div>
                    </a>
                </li>
                @endrole
            </ul>
        </li>
        @endif

        @if (Auth::user()->can('regionals') && config('cms.module.regional.active') == true 
            || Auth::user()->can('templates') && config('cms.module.master.template.active') == true
            || Auth::user()->can('tags') && config('cms.module.master.tags.active') == true)
        <!-- Master Data -->
        <li class="sidenav-header small font-weight-semibold">
            <span>MASTER DATA</span>
        </li>
        @endif

        @if (Auth::user()->can('regionals') && config('cms.module.regional.active') == true)
        <!-- Regional -->
        <li class="sidenav-item{{ Request::is('admin/regional*') ? ' active' : '' }}">
            <a href="{{ route('province.index') }}" class="sidenav-link" title="@lang('module/regional.caption')">
                <div class="sidenav-icon">
                    <i class="fi fi-rr-map-marker"></i>
                </div>
                <div>@lang('module/regional.caption')</div>
            </a>
        </li>
        @endif
        @if (Auth::user()->can('templates') && config('cms.module.master.template.active') == true)
        <!-- Template -->
        <li class="sidenav-item{{ Request::is('admin/template*') ? ' active' : '' }}">
            <a href="{{ route('template.index') }}" class="sidenav-link" title="@lang('master/template.caption')">
                <div class="sidenav-icon">
                    <i class="fi fi-rr-form"></i>
                </div>
                <div>@lang('master/template.caption')</div>
            </a>
        </li>
        @endif
        @if (Auth::user()->can('tags') && config('cms.module.master.tags.active') == true)
        <!-- Tags -->
        <li class="sidenav-item{{ Request::is('admin/tag*') ? ' active' : '' }}">
            <a href="{{ route('tags.index') }}" class="sidenav-link" title="@lang('master/tags.caption')">
                <div class="sidenav-icon">
                    <i class="fi fi-rr-tags"></i>
                </div>
                <div>@lang('master/tags.caption')</div>
            </a>
        </li>
        @endif

        @if (Auth::user()->can('pages') || Auth::user()->can('content_sections') || Auth::user()->can('banners')
            || Auth::user()->can('gallery_albums') || Auth::user()->can('documents') || Auth::user()->can('links')
            || Auth::user()->can('inquiries') || Auth::user()->can('events') || Auth::user()->can('menus') 
            || Auth::user()->can('widgets'))
        <!-- Module -->
        <li class="sidenav-header small font-weight-semibold">
            <span>MODULE</span>
        </li>
        @endif

        @if (Auth::user()->can('pages') && config('cms.module.page.active') == true)
        <!-- Page -->
        <li class="sidenav-item{{ Request::is('admin/page*') ? ' active' : '' }}">
            <a href="{{ route('page.index') }}" class="sidenav-link" title="@lang('module/page.caption')">
                <div class="sidenav-icon">
                    <i class="fi fi-rr-menu-burger"></i>
                </div>
                <div>@lang('module/page.caption')</div>
            </a>
        </li>
        @endif
        @if (Auth::user()->can('content_sections') && config('cms.module.content.section.active') == true)
        <!-- Content -->
        <li class="sidenav-item{{ Request::is('admin/content*') ? ' active' : '' }}">
            <a href="{{ route('content.section.index') }}" class="sidenav-link" title="@lang('module/content.caption')">
                <div class="sidenav-icon">
                    <i class="fi fi-rr-edit"></i>
                </div>
                <div>@lang('module/content.caption')</div>
            </a>
        </li>
        @endif
        @if (Auth::user()->can('banners') && config('cms.module.banner.active') == true)
        <!-- Banner -->
        <li class="sidenav-item{{ Request::is('admin/banner*') ? ' active' : '' }}">
            <a href="{{ route('banner.index') }}" class="sidenav-link" title="@lang('module/banner.caption')">
                <div class="sidenav-icon">
                    <i class="fi fi-rr-picture"></i>
                </div>
                <div>@lang('module/banner.caption')</div>
            </a>
        </li>
        @endif
        @if (Auth::user()->can('gallery_albums') && config('cms.module.gallery.active') == true)
        <!-- Gallery -->
        <li class="sidenav-item{{ Request::is('admin/gallery*') ? ' active' : '' }}">
            <a href="{{ route('gallery.album.index') }}" class="sidenav-link" title="@lang('module/gallery.caption')">
                <div class="sidenav-icon">
                    <i class="fi fi-rr-photo-video"></i>
                </div>
              <div>@lang('module/gallery.caption')</div>
            </a>
        </li>
        @endif
        @if (Auth::user()->can('documents') && config('cms.module.document.active') == true)
        <!-- Document -->
        <li class="sidenav-item{{ Request::is('admin/document*') ? ' active' : '' }}">
            <a href="{{ route('document.index') }}" class="sidenav-link" title="@lang('module/document.caption')">
                <div class="sidenav-icon">
                    <i class="fi fi-rr-folder-download"></i>
                </div>
                <div>@lang('module/document.caption')</div>
            </a>
        </li>
        @endif
        @if (Auth::user()->can('links') && config('cms.module.link.active') == true)
        <!-- Link -->
        <li class="sidenav-item{{ Request::is('admin/link*') ? ' active' : '' }}">
            <a href="{{ route('link.index') }}" class="sidenav-link" title="@lang('module/link.caption')">
                <div class="sidenav-icon">
                    <i class="fi fi-rr-link-alt"></i>
                </div>
                <div>@lang('module/link.caption')</div>
            </a>
        </li>
        @endif
        @if (Auth::user()->can('inquiries') && config('cms.module.inquiry.active') == true)
        <!-- Inquiry -->
        <li class="sidenav-item{{ Request::is('admin/inquiry*') ? ' active' : '' }}">
            <a href="{{ route('inquiry.index') }}" class="sidenav-link" title="@lang('module/inquiry.caption')">
                <div class="sidenav-icon">
                    <i class="fi fi-rr-envelope"></i>
                </div>
                <div>@lang('module/inquiry.caption')</div>
                <div class="pl-1 ml-auto" id="inquiry-form">
                    <div class="badge badge-main" id="total-inquiry-unread">0</div>
                </div>
            </a>
        </li>
        @endif
        @if (Auth::user()->can('events') && config('cms.module.event.active') == true)
        <!-- Event -->
        <li class="sidenav-item{{ Request::is('admin/event*') ? ' active' : '' }}">
            <a href="{{ route('event.index') }}" class="sidenav-link" title="@lang('module/event.caption')">
                <div class="sidenav-icon">
                    <i class="fi fi-rr-calendar-clock"></i>
                </div>
              <div>@lang('module/event.caption')</div>
            </a>
        </li>
        @endif
        @if (Auth::user()->can('widgets') && config('cms.module.widget.active') == true)
        <!-- Widget -->
        <li class="sidenav-item{{ Request::is('admin/widget*') ? ' active' : '' }}">
            <a href="{{ route('widget.index') }}" class="sidenav-link" title="@lang('module/widget.caption')">
                <div class="sidenav-icon">
                    <i class="fi fi-rr-apps-sort"></i>
                </div>
                <div>@lang('module/widget.caption')</div>
            </a>
        </li>
        @endif
        @can ('menus')
        <!-- Menu -->
        <li class="sidenav-item{{ Request::is('admin/menu*') ? ' active' : '' }}">
            <a href="{{ route('menu.category.index') }}" class="sidenav-link" title="@lang('module/menu.caption')">
                <div class="sidenav-icon">
                    <i class="fi fi-rr-menu-dots"></i>
                </div>
                <div>@lang('module/menu.caption')</div>
            </a>
        </li>
        @endcan
        @role('developer|super')
        <!-- Index URL -->
        <li class="sidenav-item{{ Request::is('admin/url*') ? ' active' : '' }}">
            <a href="{{ route('url.index') }}" class="sidenav-link" title="@lang('module/url.caption')">
                <div class="sidenav-icon">
                    <i class="fi fi-rr-link-slash"></i>
                </div>
                <div>@lang('module/url.caption')</div>
            </a>
        </li>
        @endrole

        
        @if (Auth::user()->can('languages') && config('cms.module.feature.language.active') == true
            || Auth::user()->can('registration') && config('cms.module.feature.registration.active') == true
            || Auth::user()->can('apis') && config('cms.module.feature.api.active') == true)
        <!-- Extras -->
        <li class="sidenav-header small font-weight-semibold">
            <span>FEATURE</span>
        </li>
        @endif

        @if (Auth::user()->can('languages') && config('cms.module.feature.language.active') == true)
        <!-- Language -->
        <li class="sidenav-item{{ Request::is('admin/language*') ? ' active' : '' }}">
            <a href="{{ route('language.index') }}" class="sidenav-link" title="@lang('feature/language.caption')">
                <div class="sidenav-icon">
                    <i class="fi fi-rr-flag"></i>
                </div>
                <div>@lang('feature/language.caption')</div>
            </a>
        </li>
        @endif
        @if (Auth::user()->can('registrations') && config('cms.module.feature.registration.active') == true)
        <!-- Registration -->
        <li class="sidenav-item{{ Request::is('admin/registration*') ? ' active' : '' }}">
            <a href="{{ route('registration.index') }}" class="sidenav-link" title="@lang('feature/registration.caption')">
                <div class="sidenav-icon">
                    <i class="fi fi-rr-user-add"></i>
                </div>
                <div>@lang('feature/registration.caption')</div>
            </a>
        </li>
        @endif

        @if (Auth::user()->can('apis') && config('cms.module.feature.api.active') == true)
        <!-- API -->
        <li class="sidenav-item{{ Request::is('admin/api*') ? ' active' : '' }}">
            <a href="{{ route('api.index') }}" class="sidenav-link" title="@lang('feature/api.caption')">
                <div class="sidenav-icon">
                    <i class="fi fi-rr-chart-tree"></i>
                </div>
                <div>@lang('feature/api.caption')</div>
            </a>
        </li>
        @endif

        @if (config('cms.module.feature.configuration.active') == true && Auth::user()->can('configurations') 
            || Auth::user()->can('visitor') || Auth::user()->can('filemanager') )
        <!-- Extras -->
        <li class="sidenav-header small font-weight-semibold">
            <span>EXTRA</span>
        </li>

        @can('configurations')
        <!-- Configuration -->
        <li class="sidenav-item{{ Request::is('admin/configuration*') ? ' open active' : '' }}">
            <a href="javascript:void(0)" class="sidenav-link sidenav-toggle" title="@lang('feature/configuration.caption')">
                <div class="sidenav-icon">
                    <i class="fi fi-rr-settings-sliders"></i>
                </div>
                <div>@lang('feature/configuration.caption')</div>
            </a>

            <ul class="sidenav-menu">
                <li class="sidenav-item{{ Request::is('admin/configuration/website') ? ' active' : '' }}">
                    <a href="{{ route('configuration.website') }}" class="sidenav-link" title="@lang('feature/configuration.website.caption')">
                        <div>@lang('feature/configuration.website.caption')</div>
                    </a>
                </li> 
                <li class="sidenav-item{{ Request::is('admin/configuration/text*') ? ' active' : '' }}">
                    <a href="{{ route('configuration.text', ['lang' => App::getLocale()]) }}" class="sidenav-link" title="@lang('feature/configuration.text.caption')">
                        <div>@lang('feature/configuration.text.caption')</div>
                    </a>
                </li>
            </ul>
        </li>
        @endcan
        @can('visitor')
        <!-- Visitor -->
        <li class="sidenav-item{{ Request::is('admin/visitor') ? ' active' : '' }}">
            <a href="{{ route('visitor') }}" class="sidenav-link" title="@lang('feature/configuration.visitor.caption')">
                <div class="sidenav-icon">
                    <i class="fi fi-rr-users"></i>
                </div>
                <div>@lang('feature/configuration.visitor.caption')</div>
            </a>
        </li>
        @endcan
        @can('filemanager')
        <!-- Filemanager -->
        <li class="sidenav-item{{ Request::is('admin/filemanager') ? ' active' : '' }}">
            <a href="{{ route('filemanager') }}" class="sidenav-link" title="@lang('feature/configuration.filemanager.caption')">
                <div class="sidenav-icon">
                    <i class="fi fi-rr-folder"></i>
                </div>
                <div>@lang('feature/configuration.filemanager.caption')</div>
            </a>
        </li>
        @endcan
        @endif

    </ul>

    @if (config('cms.setting.layout') != 3)
    <!-- Switcer -->
    <div class="box-sw">
        <div id="my_switcher" class="btn-sw">
            <ul class="list-sw">
                <li class="item-sw">
                    <div data-theme="light" class="setColor light">
                        <div class="spin-sw">
                            <span><span></span></span>
                        </div>
                        <div class="text-sw">Dark Mode</div>
                    </div>
                </li>
                <li class="item-sw">
                    <div data-theme="dark" class="setColor dark">
                        <div class="spin-sw">
                            <span><span></span></span>
                        </div>
                        <div class="text-sw">Light Mode</div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    @endif
</div>