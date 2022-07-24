<?php

return [
    'title' => 'Inquiry',
    'caption' => 'Inquiry',
    'text' => 'List Inquiry',
    'label' => [
        'field1' => 'Nama',
        'field2' => 'Slug / URL',
        'field3' => 'Body',
        'field4' => 'After Body',
        'field5' => 'Tampilkan Form',
        'field6' => 'Email (untuk notifikasi pesan)',
        'field7' => 'Tampilkan Map',
        'field8' => 'Longitude',
        'field9' => 'Latitude',
        'lock_form' => 'Lock form setelah submit ?'
    ],
    'placeholder' => [
        'field1' => 'Masukan nama',
        'field2' => '',
        'field3' => '',
        'field4' => '',
        'field5' => 'YA',
        'field6' => '',
        'field7' => 'YA',
        'field8' => '',
        'field9' => '',
    ],
    'field' => [
        'title' => 'Fields',
        'caption' => 'Field',
        'text' => 'List Field',
        'label' => [
            'field1' => 'Label',
            'field2' => 'Nama',
            'field3' => 'Tipe Form',
            'field4' => 'Tipe',
            'field5' => 'ID',
            'field6' => 'Class',
            'field7' => 'Attribute',
            'field8' => 'Validasi',
            'field9' => 'Properties',
            'field10' => 'Placeholder',
            'field11' => 'Options',
            'is_unique' => 'Unique Field (validasi pengecekan field unik)',
        ],
        'placeholder' => [
            'field1' => '',
            'field2' => '',
            'field3' => '',
            'field4' => 'Tipe input html',
            'field5' => 'id html',
            'field6' => 'class css html untuk tag form group',
            'field7' => 'attribute html untuk tag form input',
            'field8' => 'validasi laravel, contoh : required|email etc.',
            'field9' => '',
            'field10' => '',
            'field11' => '',
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
            'field1' => 'IP Address',
            'field2' => 'Exported',
            'field3' => 'Submit Time',
        ],
        'placeholder' => [
            
        ],
        'submit_success' => 'Submit form berhasil',
        'unique_warning' => 'Email / Telpon yang anda masukan sudah ada',
    ],
];