<?php

return [
    'title' => 'Inquiry',
    'caption' => 'Inquiry',
    'text' => 'List Inquiry',
    'label' => [
        'name' => 'Nama',
        'slug' => 'Slug / URL',
        'body' => 'Body',
        'after_body' => 'After Body',
        'email' => 'Email (untuk notifikasi pesan)',
        'longitude' => 'Longitude',
        'latitude' => 'Latitude',
        'lock_form' => 'Lock form setelah submit ?'
    ],
    'placeholder' => [
        'name' => 'Masukan nama',
    ],
    'field' => [
        'title' => 'Fields',
        'caption' => 'Field',
        'text' => 'List Field',
        'label' => [
            'label' => 'Label',
            'name' => 'Nama',
            'form_type' => 'Tipe Form',
            'type' => 'Tipe',
            'id' => 'ID',
            'class' => 'Class',
            'attribute' => 'Attribute',
            'validation' => 'Validasi',
            'properties' => 'Properties',
            'placeholder' => 'Placeholder',
            'option' => 'Options',
            'is_unique' => 'Unique Field (validasi pengecekan field unik)',
        ],
        'placeholder' => [
            'type' => 'Tipe input html',
            'id' => 'id html',
            'class' => 'class css html untuk tag form group',
            'attribute' => 'attribute html untuk tag form input',
            'validation' => 'validasi laravel, contoh : required|email etc.',
        ],
        'validations' => [
            'required' => [
                'caption' => 'Required',
                'desc' => 'Field wajib diisi'
            ],
            'email' => [
                'caption' => 'Email',
                'desc' => 'Field harus isian email'
            ]
        ],
    ],
    'form' => [
        'title' => 'Submit Form',
        'caption' => 'Submit Form',
        'text' => 'List Submit Form',
        'label' => [
            'ip_address' => 'IP Address',
            'exported' => 'Exported',
            'submit_time' => 'Submit Time',
        ],
        'placeholder' => [
            
        ],
        'submit_success' => 'Submit form berhasil',
        'unique_warning' => 'Email / Telpon yang anda masukan sudah ada',
    ],
];