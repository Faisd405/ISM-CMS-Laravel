<?php

return [
    'index_url' => true, //untuk mengaktifkan permalink short
    'locales' => true,  //untuk mengaktifkan auto redirect ke ISO CODE bahasa

    // Security form dengan recaptcha
    'recaptcha' => true,

    // url
    'url' => [
        'landing' => true,
        'search' => true,
        'sitemap' => true
    ],

    //icon refernce
    'icon_refernces' => [
        'https://icons8.com/line-awesome',
    ],

    // Limit default
    'limit' => [
        10 => '10',
        20 => '20',
        50 => '50',
        100 => '100',
    ],

    // design / halaman
    'theme_setting' => false,
    'layout_auth' => 1, // 1 = default, 2 = + background
    'layout' => 1, // 1 = vertical 1, 2 = vertical 2, 3 = horizontal
    'layout_logo' => 1, // 1 = small, 2 = default
    'layout_fixed' => 1,

    // Copyright
    'copyright' => [
        'show' => true,
        'developer' => '4 Vision Media',
        'title' => 'Perusahaan Jasa Pembuatan Website, Aplikasi, Digital Marketing, Design Company Profile, Software',
        'url' => 'https://www.4visionmedia.com',
    ],
];