<?php

return [
    //---------------
    // AUTHENTICATION
    //---------------
    'auth' => [
        //-- LOGIN
        'login' => [
            'backend' => [
                'active' => true, //status
                'role' => 'super|support|admin|editor', //role user login,
                'lock_time' => 3, //jam penguncian form login,
                'lock_total' => 5, //jumlah gagal yang akan dilock,
                'lock_warning' => 3, //muncul warning jika gagal

            ],
            'frontend' => [
                'active' => true, //status
                'role' => 'super|support|admin', //role user login
                'lock_time' => 2, //jam penguncian form login
                'lock_total' => 10, //jumlah gagal yang akan dilock
                'lock_warning' => 5, //muncul warning jika gagal
            ],
            'remember' => true, //checkbox ingat saya,
            'lock_failed' => true, //lock form login jika gagal login

        ],
        //-- FORGOT PASSWORD
        'forgot_password' => [
            'active' => true,
        ],
        //-- REGISTER
        'register' => [
            'active' => true,
            'agree' => true, //persetujuan pendaftaran
            'is_login' => false, //otomatis login setelah register,
            'activate_account' => true
        ]
    ],

    //---------------
    // USER
    //---------------
    'user' => [
        'active' => true,
    ],

    //---------------
    // REGIONAL
    //---------------
    'regional' => [
        'active' => true
    ],

    //---------------
    // FEATURE
    //---------------
    'feature' => [
        'configuration' => [
            'active' => true
        ],
        'notification' => [
            'active' => true,
            'email' => [
                'activate_account' => true,
                'verification_email' => true,
                'register' => true,
                'inquiry' => true,
                'login_failed' => true,
            ],
            'apps' => [
                'register' => true,
                'inquiry' => true,
            ],
        ],
        'language' => [
            'active' => true,
            'multiple' => true,
            'default' => config('app.fallback_locale')
        ],
        'registration' => [
            'active' => true
        ],
        'api' => [
            'active' => true,
            'mod' => [],
        ],
        'maintenance' => [
            'url_unblock' => ['backend', 'admin', 'maintenance']
        ]
    ],
    
    //---------------
    // MASTER
    //---------------
    'master' => [
        'media' => [
            'active' => true,
            'mod' => [
                'page',
                'content_post'
            ],
        ],
        'template' => [
            'active' => true,
            'mod' => [
                'page' => [
                    'full' => 'views/frontend/pages/',
                    'custom' => 'custom',
                ],
                'content_section' => [
                    'full' => 'views/frontend/contents/section/',
                    'list' => 'list',
                    'detail' => 'detail',
                ],
                'content_category' => [
                    'full' => 'views/frontend/contents/category/',
                    'custom' => 'custom',
                ],
                'content_post' => [
                    'full' => 'views/frontend/contents/post/',
                    'custom' => 'custom',
                ],
                'gallery_category' => [
                    'full' => 'views/frontend/galleries/category/',
                    'list' => 'list',
                    'detail' => 'detail',
                ],
                'gallery_album' => [
                    'full' => 'views/frontend/galleries/album/',
                    'custom' => 'custom',
                ],
                'document_category' => [
                    'full' => 'views/frontend/documents/',
                    'custom' => 'custom',
                ],
                'link_category' => [
                    'full' => 'views/frontend/links/',
                    'custom' => 'custom',
                ]
            ],
            'type' => [
                0 => 'custom',
                1 => 'list',
                2 => 'detail',
            ],
        ],
        'tags' => [
            'active' => true
        ],
    ],

    //---------------
    // URL
    //---------------
    'url' => [
        'mod' => [
            'page',
            'content_section',
            'inquiry'
        ],
    ],

    //---------------
    // MENU
    //---------------
    'menu' => [
        'approval' => true,
        'mod' => [
        ],
    ],

    //---------------
    // PAGE
    //---------------
    'page' => [
        'active' => true,
        'list_view' => true,
        'approval' => true,
    ],

    //---------------
    // CONTENT
    //---------------
    'content' => [
        'section' => [
            'active' => true,
            'list_view' => true,
            'approval' => true,
        ],
        'category' => [
            'active' => true,
            'list_view' => true,
            'approval' => true,
        ],
        'post' => [
            'active' => true,
            'list_view' => true,
            'approval' => true,
        ],
    ],

    //---------------
    // BANNER
    //---------------
    'banner' => [
        'active' => true,
        'approval' => true,
        'category' => [
            'approval' => true,
        ]
    ],

    //---------------
    // GALLERY
    //---------------
    'gallery' => [
        'active' => true,
        'category' => [
            'approval' => true,
            'active' => true,
        ],
        'album' => [
            'approval' => true,
        ],
        'file' => [
            'approval' => true,
        ]
    ],

    //---------------
    // DOCUMENT
    //---------------
    'document' => [
        'list_view' => true,
        'active' => true,
        'category' => [
            'approval' => true,
        ],
        'file' => [
            'approval' => true,
        ]
    ],

    //---------------
    // LINK
    //---------------
    'link' => [
        'list_view' => true,
        'active' => true,
        'category' => [
            'approval' => true,
        ],
        'media' => [
            'approval' => true,
        ]
    ],

    //---------------
    // INQUIRY
    //---------------
    'inquiry' => [
        'list_view' => true,
        'approval' => true,
        'active' => true,
        'field' => [
            'approval' => true,
        ]
    ],
];