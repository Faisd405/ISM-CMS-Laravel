<?php

return [
    'name' => '4VM',
    'manifest' => [
        'name' => env('APP_NAME', 'Four Vision Media'),
        'short_name' => '4VM',
        'start_url' => '/',
        'background_color' => '#0084ff',
        'theme_color' => '#0084ff',
        'display' => 'standalone',
        'orientation'=> 'any',
        'status_bar'=> 'black',
        'icons' => [
            '72x72' => [
                'path' => '/assets/favicon/pwa/icon-72x72.png',
                'purpose' => 'any'
            ],
            '96x96' => [
                'path' => '/assets/favicon/pwa/icon-96x96.png',
                'purpose' => 'any'
            ],
            '128x128' => [
                'path' => '/assets/favicon/pwa/icon-128x128.png',
                'purpose' => 'any'
            ],
            '144x144' => [
                'path' => '/assets/favicon/pwa/icon-144x144.png',
                'purpose' => 'any'
            ],
            '152x152' => [
                'path' => '/assets/favicon/pwa/icon-152x152.png',
                'purpose' => 'any'
            ],
            '192x192' => [
                'path' => '/assets/favicon/pwa/icon-192x192.png',
                'purpose' => 'any'
            ],
            '384x384' => [
                'path' => '/assets/favicon/pwa/icon-384x384.png',
                'purpose' => 'any'
            ],
            '512x512' => [
                'path' => '/assets/favicon/pwa/icon-512x512.png',
                'purpose' => 'any'
            ],
        ],
        'splash' => [
            '640x1136' => '/assets/favicon/pwa/splash-640x1136.png',
            '750x1334' => '/assets/favicon/pwa/splash-750x1334.png',
            '828x1792' => '/assets/favicon/pwa/splash-828x1792.png',
            '1125x2436' => '/assets/favicon/pwa/splash-1125x2436.png',
            '1242x2208' => '/assets/favicon/pwa/splash-1242x2208.png',
            '1242x2688' => '/assets/favicon/pwa/splash-1242x2688.png',
            '1536x2048' => '/assets/favicon/pwa/splash-1536x2048.png',
            '1668x2224' => '/assets/favicon/pwa/splash-1668x2224.png',
            '1668x2388' => '/assets/favicon/pwa/splash-1668x2388.png',
            '2048x2732' => '/assets/favicon/pwa/splash-2048x2732.png',
        ],
        'shortcuts' => [
            [
                'name' => 'Four Vision Media',
                'description' => 'Perusahaan Jasa Pembuatan Website Software Aplikasi Desain Video & Konsultan IT',
                'url' => env('APP_URL', '/'),
                'icons' => [
                    "src" => "/assets/favicon/pwa/icon-72x72.png",
                    "purpose" => "any"
                ]
            ],
            [
                'name' => 'Four Vision Media',
                'description' => 'Perusahaan Jasa Pembuatan Website Software Aplikasi Desain Video & Konsultan IT',
                'url' => env('APP_URL', '/'),
            ]
        ],
        'custom' => []
    ]
];
