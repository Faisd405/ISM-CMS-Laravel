<?php

return [
    'title' => 'Event',
    'caption' => 'Event',
    'text' => 'List Event',
    'label' => [
        'field1' => 'Nama',
        'field2' => 'Slug',
        'field3' => 'Description',
        'field4' => 'Form Description',
        'field5' => 'Register Code',
        'field6' => 'Tempat',
        'field7' => 'Tanggal Mulai',
        'field8' => 'Tanggal Selesai',
        'field9' => 'Email (untuk notifikasi submit event)',
    ],
    'placeholder' => [
        'field1' => 'Masukan nama',
        'field2' => '',
        'field3' => '',
        'field4' => '',
        'field5' => 'YA',
        'field6' => '',
    ],
    'type' => [
        0 => 'OFFLINE',
        1 => 'ONLINE'
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
            'field10' => 'Placeholder'
        ],
        'placeholder' => [
            'field1' => '',
            'field2' => '',
            'field3' => '',
            'field4' => '',
            'field5' => 'id html',
            'field6' => 'class css html',
            'field7' => '',
            'field8' => 'validasi laravel, contoh : required|email etc.',
            'field9' => '',
            'field10' => '',
        ],
    ],
    'form' => [
        'title' => 'Pesan',
        'caption' => 'Pesan',
        'text' => 'List Pesan',
        'label' => [
            'field1' => 'IP Address',
            'field2' => 'Exported',
            'field3' => 'Submit Time',
        ],
        'placeholder' => [
            
        ],
        'submit_success' => 'Submit form berhasil',
    ],
];