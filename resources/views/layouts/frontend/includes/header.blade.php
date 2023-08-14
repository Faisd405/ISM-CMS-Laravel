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

<header>
    <div class="main-header">
        <div class="logo-header">
            <div class="container">
                <a href="{{ route('home') }}" class="logo">
                    <img src="{{ asset('assets/frontend/images/logo.png') }}" alt="">
                </a>
            </div>
        </div>
        <div class="top-header">
            <div class="container">
                <div class="top-header-flex">
                    <div class="top-header-left">
                        <div class="nav-item tagline">
                            <ul>
                                <li>
                                    <span>The Indonesian Iron & Steel Industry Association</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="top-header-right">
                        <div class="nav-item language">
                            <span>Language</span>
                            <ul>
                                @foreach ($languages as $lang)
                                    <li class="{{ App::getLocale() == $lang['iso_codes'] ? 'active' : '' }}">
                                        <a href="{{ $lang['url_switcher'] }}" title="{{ $lang['name'] }}">
                                            <img src="{{ asset('assets/frontend/images/language/'.$lang['iso_codes'].'.jpg') }}" alt="">
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                    </div>
                </div>

            </div>

        </div>
        <div class="bottom-header">
            <div class="container">
                <div class="bottom-header-flex">
                    <div class="nav-item main-nav">
                        <ul class="list-nav">
                            @foreach ($menu['header'] as $header)
                                @if (count($header->childs) == 0)
                                    @if (isset($header['config']['event']) && $header['config']['event'])
                                        <li class="nav-event">
                                            <a href="{{ $header['module_data']['routes'] }}" target="_blank">
                                                <div class="box-icon-event">
                                                    @if (isset($header['config']['icon']) && $header->isImageLink($header['config']['icon']))
                                                        <img src="{{ $header['config']['icon'] }}" alt="">
                                                    @else
                                                        <i class="{{ $header['config']['icon'] }}"></i>
                                                    @endif
                                                </div>
                                                <span>{{ $header['module_data']['title'] }}</span>
                                            </a>
                                        </li>
                                    @else
                                        <li><a
                                                href="{{ $header['module_data']['routes'] }}">{{ $header['module_data']['title'] }}</a>
                                        </li>
                                    @endif
                                @else
                                    <li class="has-dropdown">
                                        <a href="#!">{{ $header['module_data']['title'] }}</a>
                                        <ul class="dropdown">
                                            @foreach ($header->childs()->where('publish', 1)->get() as $child)
                                                <li><a
                                                        href="{{ $child['module_data']['routes'] }}">{{ $child['module_data']['title'] }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    <div class="nav-item widget">
                        <ul>
                            <li class="search-nav">
                                <span class="search-btn" data-selector=".search-nav"><i
                                        class="las la-search"></i></span>
                            </li>
                            <li><a href="{{ route('login') }}" title="Login/Register"><i
                                        class="las la-user-tie"></i></a></li>
                            <li class="d-lg-none"><a href="#!" class="navigation-burger"><i
                                        class="las la-bars"></i></a>
                            </li>
                        </ul>
                        <div class="search-box">
                            <form action="{{ route('home.search') }}">
                                <div class="form-group">
                                    <input id="search-field" type="search" class="form-control" name="keyword"
                                        placeholder="Type here to search">
                                    <button class="btn btn-icon-abslt" type="submit"><i
                                            class="las la-search"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
