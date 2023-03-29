{{-- MENU

    LOOPING :
    $menu['header']

    ATTRIBUTE DIDALAM LOOPING :
        $menu->fieldLang('title') //get title
        $menu->childPublish()->get() //get child (dropdown)
        $menu->routes() //route / url
        $menu['config']['icon'] //icon
        $menu['config']['target_blank'] //target blank
--}}

{{-- LANGUAGE

    LOOPING :
    $languages / $languages->whereNotIn('iso_codes', [App::getLocale()])

    ATTRIBUTE DIDALAM LOOPING :
        $lang['url_switcher'] //route / url
        $lang['name'] // nama negara
        $lang['iso_codes'] // iso code negara
        $lang['flag_icon'] // icon bendera

--}}
<header class="section-header anim-load-down">
    <div class="content-header">
        <div class="container-fluid">
            <nav class="nav-header d-flex align-items-center">
                <div
                    class="burger-menu flex-shrink-0 d-flex align-items-center justify-content-center d-xl-none rounded-circle">
                    <div class="burger-icon d-flex flex-column">
                        <span></span>
                        <span></span>
                    </div>
                </div>
                <a href="{{ route('home') }}" class="logo-header mx-auto mx-xl-0 me-xl-4 me-xxl-5"></a>
                <div class="menu-header ms-xl-5 me-xl-auto">
                    <div class="nav-menu">
                        <ul class="menu nav flex-column flex-xl-row flex-lg-nowrap" id="menu-header">
                            @foreach ($menu['header'] as $header)
                                @if (count($header->childs) == 0)
                                    <li class="nav-item"><a href="{{ $header['module_data']['routes'] }}"
                                            class="nav-link link text-lg-nowrap">
                                            <div class="label-btn">{{ $header['module_data']['title'] }}</div>
                                        </a>
                                    </li>
                                @else
                                    <li class="nav-item dropdown collapse-menu">
                                        <a href="javascript:;" data-bs-target="#collapse-menu-{{ $header['id'] }}"
                                            class="nav-link link text-lg-nowrap">
                                            <div class="label-btn">{{ $header['module_data']['title'] }}</div>
                                        </a>
                                        <div class="dropdown-menu-hover collapse show"
                                            id="collapse-menu-{{ $header['id'] }}">
                                            <ul class="dropdown-content">
                                                @foreach ($header->childPublish()->get() as $child)
                                                    @if (count($child->childs) == 0)
                                                        <li class="dropdown-item"><a
                                                                href="{{ $child['module_data']['routes'] }}"
                                                                class="dropdown-link">
                                                                <div class="label-btn d-inline-flex span-2-white">
                                                                    {{ $child['module_data']['title'] }}
                                                                </div>
                                                            </a>
                                                        </li>
                                                    @else
                                                        <li class="dropdown-item dropdown collapse-menu">
                                                            <a href="javascript:;"
                                                                data-bs-target="#collapse-inner-{{ $child['id'] }}"
                                                                class="dropdown-link">
                                                                <div class="label-btn d-inline-flex span-2-white">
                                                                    {{ $child['module_data']['title'] }}
                                                                </div>
                                                            </a>
                                                            <div class="dropdown-menu-hover sub-dropdown-menu sub-menu collapse show"
                                                                id="collapse-inner-{{ $child['id'] }}"
                                                                data-bs-parent="#collapse-menu-{{ $header['id'] }}">
                                                                <ul class="dropdown-content">
                                                                    @foreach ($child->childPublish()->get() as $subchild)
                                                                        <li class="dropdown-item"><a
                                                                                href="{{ $subchild['module_data']['routes'] }}"
                                                                                class="dropdown-link">
                                                                                <div
                                                                                    class="label-btn d-inline-flex span-2-white">
                                                                                    {{ $subchild['module_data']['title'] }}
                                                                                </div>
                                                                            </a></li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    <div class="lang-header m-0 d-flex d-xl-none">
                        <div class="subtitle me-auto">@lang('text.change_language')</div>
                        <ul class="lang-menu d-flex m-0 list-unstyled">
                            @foreach ($languages as $language)
                                <li>
                                    <a href="{{ $language['url_switcher'] }}"
                                        class="subtitle {{ App::getLocale() == $language['iso_codes'] ? 'active' : '' }}">{{ $language['iso_codes'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="menu-right ms-xl-auto d-flex align-items-center">
                    <div class="lang-header dropdown d-none d-xl-block">
                        <a href="javascript:;" class="lang-toggle" data-bs-toggle="dropdown">
                            <div class="lang-icon ratio ratio-1x1 rounded-circle overflow-hidden">
                                @if (App::getLocale() == 'id')
                                    <img src="{{ asset('assets/frontend/img/ic_id.svg') }}" alt="indonesia"
                                        class="thumb">
                                @elseif (App::getLocale() == 'en')
                                    <img src="{{ asset('assets/frontend/img/ic_eng.svg') }}" alt="english"
                                        class="thumb">
                                @endif
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div class="dropdown-menu-content">
                                <ul class="dropdown-content">
                                    @foreach ($languages as $language)
                                        <li class="dropdown-item">
                                            <a class="dropdown-link {{ App::getLocale() == $language['iso_codes'] ? 'active' : '' }}"
                                                href="{{ $language['url_switcher'] }}">
                                                <div class="label-btn span-2-white">
                                                    {{ $language['name'] }}
                                                </div>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('inquiry.read.contact-us') }}" class="btn btn-contact">
                        <i class="fa-light fa-phone me-xl-3"></i>
                        <div class="label-btn subtitle d-none d-xl-flex">@lang('text.contact_us')</div>
                    </a>
                </div>
            </nav>
        </div>
    </div>
</header>
