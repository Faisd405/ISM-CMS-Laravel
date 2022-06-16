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

    'layout' => 0, // 0 = vertical, 1 = horizontal

    // Copyright
    'copyright' => [
        'show' => true,
        'developer' => '4 Vision Media',
        'title' => 'Perusahaan Jasa Pembuatan Website, Aplikasi, Digital Marketing, Design Company Profile, Software',
        'url' => 'https://www.4visionmedia.com',
    ],
];