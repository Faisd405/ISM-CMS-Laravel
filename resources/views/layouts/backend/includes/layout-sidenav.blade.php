<div id="layout-sidenav" class="layout-sidenav sidenav-vertical sidenav bg-sidenav-theme">

    <!-- Brand demo (see assets/css/demo/demo.css) -->
    <div class="app-brand demo">
        <span class="app-brand-logo demo">
            <img src="{{ $config['logo'] }}" alt="{{ $config['website_name'] }} Logo">
        </span>
        <a href="javascript:void(0)" class="layout-sidenav-toggle sidenav-link text-large ml-auto">
          <i class="las la-thumbtack"></i>
        </a>
    </div>

    <div class="sidenav-divider mt-0"></div>

    <!-- Inner -->
    <ul class="sidenav-inner{{ empty($layout_sidenav_horizontal) ? ' py-1' : '' }}">

        <!-- Dashboard -->
        <li class="sidenav-item {{ Request::is('admin/dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="sidenav-link" title="@lang('module/dashboard.caption')">
              <i class="sidenav-icon las la-tachometer-alt"></i><div>@lang('module/dashboard.caption')</div>
            </a>
        </li>

        @if (Auth::user()->can('users') && config('cms.module.user.active') == true)
        <!-- User Management -->
        <li class="sidenav-item {{ (Request::is('admin/acl*') || Request::is('admin/user*')) ? 'open active' : '' }}">
            <a href="javascript:void(0)" class="sidenav-link sidenav-toggle" title="@lang('module/user.user_management_caption')">
              <i class="sidenav-icon ion las la-users"></i>
              <div>@lang('module/user.user_management_caption')</div>
            </a>

            <ul class="sidenav-menu">
                @role('super')
                <!-- ACL -->
                <li class="sidenav-item {{ Request::is('admin/acl*') ? 'open active' : '' }}">
                    <a href="javascript:void(0)" class="sidenav-link sidenav-toggle" title="@lang('module/user.acl_caption')">
                        <div>@lang('module/user.acl_caption')</div>
                    </a>

                    <ul class="sidenav-menu">
                        <li class="sidenav-item {{ Request::is('admin/acl/role*') ? 'active' : '' }}">
                            <a href="{{ route('role.index') }}" class="sidenav-link" title="@lang('module/user.role.caption')">
                            <div>@lang('module/user.role.caption')</div>
                            </a>
                        </li>
                        <li class="sidenav-item {{ Request::is('admin/acl/permission*') ? 'active' : '' }}">
                            <a href="{{ route('permission.index') }}" class="sidenav-link" title="@lang('module/user.permission.caption')">
                            <div>@lang('module/user.permission.caption')</div>
                            </a>
                        </li>
                    </ul>
                </li>
                @endrole
                <li class="sidenav-item {{ (Request::is('admin/user*') && Request::segment(3) != 'log' && Request::segment(3) != 'login-failed') ? 'active' : '' }}">
                    <a href="{{ route('user.index') }}" class="sidenav-link" title="@lang('module/user.caption')">
                        <div>@lang('module/user.caption')</div>
                    </a>
                </li>
                <li class="sidenav-item {{ Request::is('admin/user/log*') && Request::segment(3) != 'login-failed' ? 'active' : '' }}">
                    <a href="{{ route('user.log') }}" class="sidenav-link" title="@lang('module/user.log.caption')">
                        <div>@lang('module/user.log.caption')</div>
                    </a>
                </li>
                @role('super')
                <li class="sidenav-item {{ Request::is('admin/user/login-failed*') ? 'active' : '' }}">
                    <a href="{{ route('user.login-failed') }}" class="sidenav-link" title="@lang('module/user.login_failed.caption')">
                        <div>@lang('module/user.login_failed.caption')</div>
                    </a>
                </li>
                @endrole
            </ul>
        </li>
        @endif

        @if (Auth::user()->can('regionals') || Auth::user()->can('templates'))
        {{-- Master Data --}}
        <li class="sidenav-divider mb-1"></li>
        <li class="sidenav-header small font-weight-semibold">MASTER DATA</li>
        @endif

        @if (Auth::user()->can('regionals') && config('cms.module.regional.active') == true)
        <!-- Regional -->
        <li class="sidenav-item {{ Request::is('admin/regional*') ? 'active' : '' }}">
            <a href="{{ route('province.index') }}" class="sidenav-link" title="@lang('module/regional.caption')">
              <i class="sidenav-icon las la-map-marked-alt"></i><div>@lang('module/regional.caption')</div>
            </a>
        </li>
        @endif
        @if (Auth::user()->can('templates') && config('cms.module.master.template.active') == true)
        <!-- Template -->
        <li class="sidenav-item {{ Request::is('admin/template*') ? 'active' : '' }}">
            <a href="{{ route('template.index') }}" class="sidenav-link" title="@lang('master/template.caption')">
              <i class="sidenav-icon lab la-wpforms"></i><div>@lang('master/template.caption')</div>
            </a>
        </li>
        @endif
        @if (Auth::user()->can('tags') && config('cms.module.master.tags.active') == true)
        <!-- Template -->
        <li class="sidenav-item {{ Request::is('admin/tag*') ? 'active' : '' }}">
            <a href="{{ route('tags.index') }}" class="sidenav-link" title="@lang('master/tags.caption')">
              <i class="sidenav-icon las la-tags"></i><div>@lang('master/tags.caption')</div>
            </a>
        </li>
        @endif

        @if (Auth::user()->can('pages') || Auth::user()->can('content_sections') || Auth::user()->can('menus') || Auth::user()->can('banner_categories')
            || Auth::user()->can('gallery_albums') || Auth::user()->can('document_categories') || Auth::user()->can('link_categories')
            || Auth::user()->can('inquiries'))
        {{-- Module --}}
        <li class="sidenav-divider mb-1"></li>
        <li class="sidenav-header small font-weight-semibold">MODULE</li>
        @endif

        @if (Auth::user()->can('pages') && config('cms.module.page.active') == true)
        <!-- Page -->
        <li class="sidenav-item {{ Request::is('admin/page*') ? 'active' : '' }}">
            <a href="{{ route('page.index') }}" class="sidenav-link" title="@lang('module/page.caption')">
              <i class="sidenav-icon las la-bars"></i><div>@lang('module/page.caption')</div>
            </a>
        </li>
        @endif
        @if (Auth::user()->can('content_sections') && config('cms.module.content.section.active') == true)
        <!-- Content -->
        <li class="sidenav-item {{ Request::is('admin/content*') ? 'active' : '' }}">
            <a href="{{ route('content.section.index') }}" class="sidenav-link" title="@lang('module/content.caption')">
              <i class="sidenav-icon las la-newspaper"></i><div>@lang('module/content.caption')</div>
            </a>
        </li>
        @endif
        @if (Auth::user()->can('banner_categories') && config('cms.module.banner.active') == true)
        <!-- Banner -->
        <li class="sidenav-item {{ Request::is('admin/banner*') ? 'active' : '' }}">
            <a href="{{ route('banner.category.index') }}" class="sidenav-link" title="@lang('module/banner.caption')">
              <i class="sidenav-icon las la-images"></i><div>@lang('module/banner.caption')</div>
            </a>
        </li>
        @endif
        @if (Auth::user()->can('gallery_albums') && config('cms.module.gallery.active') == true)
        <!-- Gallery -->
        <li class="sidenav-item {{ Request::is('admin/gallery*') ? 'active' : '' }}">
            <a href="{{ route('gallery.album.index') }}" class="sidenav-link" title="@lang('module/gallery.caption')">
              <i class="sidenav-icon las la-photo-video"></i><div>@lang('module/gallery.caption')</div>
            </a>
        </li>
        @endif
        @if (Auth::user()->can('document_categories') && config('cms.module.document.active') == true)
        <!-- Document -->
        <li class="sidenav-item {{ Request::is('admin/document*') ? 'active' : '' }}">
            <a href="{{ route('document.category.index') }}" class="sidenav-link" title="@lang('module/document.caption')">
              <i class="sidenav-icon las la-folder"></i><div>@lang('module/document.caption')</div>
            </a>
        </li>
        @endif
        @if (Auth::user()->can('link_categories') && config('cms.module.link.active') == true)
        <!-- Link -->
        <li class="sidenav-item {{ Request::is('admin/link*') ? 'active' : '' }}">
            <a href="{{ route('link.category.index') }}" class="sidenav-link" title="@lang('module/link.caption')">
              <i class="sidenav-icon las la-link"></i><div>@lang('module/link.caption')</div>
            </a>
        </li>
        @endif
        @if (Auth::user()->can('inquiries') && config('cms.module.inquiry.active') == true)
        <!-- Inquiry -->
        <li class="sidenav-item {{ Request::is('admin/inquiry*') ? 'active' : '' }}">
            <a href="{{ route('inquiry.index') }}" class="sidenav-link" title="@lang('module/inquiry.caption')">
              <i class="sidenav-icon las la-envelope"></i><div>@lang('module/inquiry.caption')</div>
            </a>
        </li>
        @endif
        @can ('menus')
        <!-- Menu -->
        <li class="sidenav-item {{ Request::is('admin/menu*') ? 'active' : '' }}">
            <a href="{{ route('menu.category.index') }}" class="sidenav-link" title="@lang('module/menu.caption')">
              <i class="sidenav-icon las la-ellipsis-h"></i><div>@lang('module/menu.caption')</div>
            </a>
        </li>
        @endcan
        @role('super')
        <!-- Index URL -->
        <li class="sidenav-item {{ Request::is('admin/url*') ? 'active' : '' }}">
            <a href="{{ route('url.index') }}" class="sidenav-link" title="@lang('module/url.caption')">
              <i class="sidenav-icon las la-external-link-square-alt"></i><div>@lang('module/url.caption')</div>
            </a>
        </li>
        @endrole

        @if (Auth::user()->can('languages') || Auth::user()->can('registration') || Auth::user()->can('apis'))
        <li class="sidenav-divider mb-1"></li>
        <li class="sidenav-header small font-weight-semibold">FEATURE</li>
        @endif

        @if (Auth::user()->can('languages') && config('cms.module.feature.language.active') == true)
        <!-- Language -->
        <li class="sidenav-item {{ Request::is('admin/language*') ? 'active' : '' }}">
            <a href="{{ route('language.index') }}" class="sidenav-link" title="@lang('feature/language.caption')">
              <i class="sidenav-icon las la-language"></i><div>@lang('feature/language.caption')</div>
            </a>
        </li>
        @endif

        @if (Auth::user()->can('registrations') && config('cms.module.feature.registration.active') == true)
        <!-- Registration -->
        <li class="sidenav-item {{ Request::is('admin/registration*') ? 'active' : '' }}">
            <a href="{{ route('registration.index') }}" class="sidenav-link" title="@lang('feature/registration.caption')">
              <i class="sidenav-icon las la-edit"></i><div>@lang('feature/registration.caption')</div>
            </a>
        </li>
        @endif

        @if (Auth::user()->can('apis') && config('cms.module.feature.api.active') == true)
        <!-- API -->
        <li class="sidenav-item {{ Request::is('admin/api*') ? 'active' : '' }}">
            <a href="{{ route('api.index') }}" class="sidenav-link" title="@lang('feature/api.caption')">
              <i class="sidenav-icon las la-sitemap"></i><div>@lang('feature/api.caption')</div>
            </a>
        </li>
        @endif


        @if (config('cms.module.feature.configuration.active') == true && Auth::user()->can('configurations') 
            || Auth::user()->can('visitor') || Auth::user()->can('filemanager') )
        <li class="sidenav-divider mb-1"></li>
        <li class="sidenav-header small font-weight-semibold">EXTRA</li>

        @can('configurations')
        <!-- Configuration -->
        <li class="sidenav-item {{ Request::is('admin/configuration*') ? 'open active' : '' }}">
            <a href="javascript:void(0)" class="sidenav-link sidenav-toggle" title="@lang('feature/configuration.caption')">
              <i class="sidenav-icon las la-cogs"></i> <div>@lang('feature/configuration.caption')</div>
            </a>

            <ul class="sidenav-menu">
                <li class="sidenav-item {{ Request::is('admin/configuration/website') ? 'active' : '' }}">
                    <a href="{{ route('configuration.website') }}" class="sidenav-link" title="@lang('feature/configuration.website.caption')">
                        <div>@lang('feature/configuration.website.caption')</div>
                    </a>
                </li> 
                <li class="sidenav-item {{ Request::is('admin/configuration/text*') ? 'active' : '' }}">
                    <a href="{{ route('configuration.text', ['lang' => App::getLocale()]) }}" class="sidenav-link" title="@lang('feature/configuration.text.caption')">
                        <div>@lang('feature/configuration.text.caption')</div>
                    </a>
                </li>
            </ul>
        </li>
        @endcan

        @can('visitor')
        <!-- Visitor -->
        <li class="sidenav-item {{ Request::is('admin/visitor') ? 'active' : '' }}">
            <a href="{{ route('visitor') }}" class="sidenav-link" title="@lang('feature/configuration.visitor.caption')">
              <i class="sidenav-icon las la-user-plus"></i><div>@lang('feature/configuration.visitor.caption')</div>
            </a>
        </li>
        @endcan

        @can('filemanager')
        <!-- Filemanager -->
        <li class="sidenav-item {{ Request::is('admin/filemanager') ? 'active' : '' }}">
            <a href="{{ route('filemanager') }}" class="sidenav-link" title="@lang('feature/configuration.filemanager.caption')">
              <i class="sidenav-icon las la-folder"></i><div>@lang('feature/configuration.filemanager.caption')</div>
            </a>
        </li>
        @endcan
        @endif
        
    </ul>
</div>
