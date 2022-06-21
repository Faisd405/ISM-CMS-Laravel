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