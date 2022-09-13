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
                'role' => 'developer|super|support|admin|editor', //role user login,
                'lock_time' => 3, //jam penguncian form login,
                'lock_total' => 5, //jumlah gagal yang akan dilock,
                'lock_warning' => 3, //muncul warning jika gagal

            ],
            'frontend' => [
                'active' => false, //status
                'role' => 'developer|super|support|admin', //role user login
                'lock_time' => 2, //jam penguncian form login
                'lock_total' => 10, //jumlah gagal yang akan dilock
                'lock_warning' => 5, //muncul warning jika gagal
            ],
            'remember' => true, //checkbox ingat saya,
            'lock_failed' => true, //lock form login jika gagal login

        ],
        //-- FORGOT PASSWORD
        'forgot_password' => [
            'active' => false,
        ],
        //-- REGISTER
        'register' => [
            'active' => false,
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
        'active' => false
    ],

    //---------------
    // FEATURE
    //---------------
    'feature' => [
        'configuration' => [
            'active' => true,
            'group' => [
                'file',
                'general',
                'seo',
                'socmed',
                'notif',
                'dev'
            ]
        ],
        'notification' => [
            'active' => true,
            'email' => [
                'login_failed' => true,
                'activate_account' => true,
                'verification_email' => true,
                'register' => true,
                'inquiry' => true,
                'event' => true
            ],
            'apps' => [
                'register' => true,
                'inquiry' => true,
                'event' => true
            ],
        ],
        'language' => [
            'active' => true,
            'multiple' => true,
            'default' => config('app.fallback_locale')
        ],
        'registration' => [
            'active' => false,
            'type' => [
                0 => 'DEFAULT'
            ]
        ],
        'api' => [
            'active' => false,
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
                'document' => [
                    'full' => 'views/frontend/documents/',
                    'custom' => 'custom',
                ],
                'link' => [
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
            'link',
            'inquiry'
        ],
    ],

    //---------------
    // MENU
    //---------------
    'menu' => [
        'approval' => true,
        'mod' => [
            'page',
            'content_section',
            'content_category',
            'content_post',
            'gallery_category',
            'gallery_album',
            'document',
            'link',
            'inquiry',
            'event'
        ],
    ],

    //---------------
    // PAGE
    //---------------
    'page' => [
        'active' => true,
        'list_view' => false,
        'approval' => false,
        'search' => true,
        'ordering' => [
            'position' => 'ASC'
        ]
    ],

    //---------------
    // CONTENT
    //---------------
    'content' => [
        'section' => [
            'active' => true,
            'list_view' => false,
            'approval' => true,
            'search' => true,
            'ordering' => [
                'position' => 'ASC'
            ],
            'addon_field' => [
                'text',
                'textarea',
                'date',
                'checkbox',
            ],
        ],
        'category' => [
            'active' => true,
            'list_view' => false,
            'approval' => false,
            'search' => true
        ],
        'post' => [
            'active' => true,
            'list_view' => false,
            'approval' => false,
            'search' => true,
            'ordering' => [
                'created_at' => 'Created',
                'publish_time' => 'Publish Time',
                'position' => 'Position'
            ],
        ],
    ],

    //---------------
    // BANNER
    //---------------
    'banner' => [
        'active' => true,
        'approval' => true,
        'ordering' => [
            'position' => 'ASC'
        ],
        'file' => [
            'approval' => false,
            'type' => [
                0 => 'IMAGE',
                1 => 'VIDEO',
                2 => 'TEXT',
            ],
            'type_image' => [
                0 => 'UPLOAD',
                1 => 'FILEMANAGER',
                2 => 'URL'
            ],
            'type_video' => [
                0 => 'UPLOAD',
                1 => 'YOUTUBE ID'
            ],
        ],
    ],

    //---------------
    // GALLERY
    //---------------
    'gallery' => [
        'active' => true,
        'list_view' => true,
        'category' => [
            'active' => true,
            'approval' => false,
            'search' => true,
            'ordering' => [
                'position' => 'ASC',
            ]
        ],
        'album' => [
            'approval' => false,
            'search' => true,
            'ordering' => [
                'position' => 'ASC',
            ]
        ],
        'file' => [
            'approval' => false,
            'type' => [
                0 => 'IMAGE',
                1 => 'VIDEO',
            ],
            'type_image' => [
                0 => 'UPLOAD',
                1 => 'FILEMANAGER',
                2 => 'URL'
            ],
            'type_video' => [
                0 => 'UPLOAD',
                1 => 'YOUTUBE ID'
            ],
        ]
    ],

    //---------------
    // DOCUMENT
    //---------------
    'document' => [
        'active' => true,
        'list_view' => false,
        'approval' => false,
        'search' => true,
        'ordering' => [
            'position' => 'ASC'
        ],
        'file' => [
            'approval' => false,
            'type' => [
                0 => 'UPLOAD',
                1 => 'FILEMANAGER',
                2 => 'URL'
            ],
        ]
    ],

    //---------------
    // LINK
    //---------------
    'link' => [
        'active' => true,
        'list_view' => false,
        'approval' => false,
        'search' => true,
        'ordering' => [
            'position' => 'ASC'
        ],
        'media' => [
            'approval' => false,
        ]
    ],

    //---------------
    // INQUIRY
    //---------------
    'inquiry' => [
        'active' => true,
        'list_view' => false,
        'approval' => true,
        'search' => true,
        'ordering' => [
            'position' => 'ASC'
        ],
        'field' => [
            'approval' => true,
            'type' => [
                0 => 'Text',
                1 => 'Textarea',
                // 2 => 'Date',
                // 3 => 'Date Time'
                // 4 => 'Select',
                // 5 => 'Checkbox',
                // 6 => 'Radiobox',
                // 7 => 'File',
            ],
            'input' => [
                'text' => 'Text',
                'number' => 'Number',
                'email' => 'Email'
            ],
        ],
    ],

    //---------------
    // EVENT
    //---------------
    'event' => [
        'active' => true,
        'list_view' => true,
        'approval' => false,
        'search' => true,
        'ordering' => [
            'position' => 'ASC'
        ],
        'type' => [
            0 => 'OFFLINE',
            1 => 'ONLINE'
        ],
        'field' => [
            'approval' => true,
            'type' => [
                0 => 'Text',
                1 => 'Textarea',
                // 2 => 'Date',
                // 3 => 'Date Time'
                // 4 => 'Select',
                // 5 => 'Checkbox',
                // 6 => 'Radiobox',
                // 7 => 'File',
            ],
            'input' => [
                'text' => 'Text',
                'number' => 'Number',
                'email' => 'Email'
            ],
        ],
    ],

    //---------------
    // WIDGET
    //---------------
    'widget' => [
        'active' => true,
        'approval' => true,
        'set' => [
            'home'
        ],
        'type' => [
            'text',
            'page',
            'content_section',
            'content_category',
            'banner',
            'gallery_category',
            'gallery_album',
            'document',
            'link',
            'inquiry',
            'event'
        ],
    ],

    //---------------
    // ORDERING
    //---------------
    'ordering' => [
        'by' => [
            'created_at' => 'Created',
            'position' => 'Position',
        ],
        'type' => [
            'DESC' => 'DESCENDING',
            'ASC' => 'ASCENDING',
        ],
    ],
];