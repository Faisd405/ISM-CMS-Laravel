<?php

return [
    'caption' => 'Gallery',
    'category' => [
        'title' => 'Kategori Galeri',
        'caption' => 'Kategori Galeri',
        'text' => 'List Kategori Galeri',
        'manage' => 'Manage Kategori',
        'label' => [
            'field1' => 'Nama',
            'field2' => 'Slug',
            'field3' => 'Deskripsi',
            'field4' => 'Preview Gambar',
            'field5' => 'Template List',
            'field6' => 'Template Detail',
            'field7' => 'Album Limit',
            'field8' => 'File Limit',
        ],
        'placeholder' => [
            'field1' => 'Masukan nama',
            'field2' => '',
            'field3' => '',
            'field4' => '',
            'field5' => '',
            'field6' => '',
            'field7' => ''
        ],
    ],
    'album' => [
        'title' => 'Album Galeri',
        'caption' => 'Album Galeri',
        'text' => 'List Album Galeri',
        'label' => [
            'field1' => 'Nama',
            'field2' => 'Slug',
            'field3' => 'Deskripsi',
            'field4' => 'Preview Gambar',
            'field5' => 'File Limit',
        ],
        'placeholder' => [
            'field1' => 'Masukan nama',
            'field2' => '',
            'field3' => '',
            'field4' => '',
            'field5' => '',
        ],
    ],
    'file' => [
        'title' => 'File Galeri',
        'caption' => 'File Galeri',
        'text' => 'List File Galeri',
        'label' => [
            'field1' => 'Judul',
            'field2' => 'Deskripsi',
            'field3' => 'File',
            'field4' => 'Thumbnail',
            'field5' => 'Youtube ID',
            'field6' => 'URL Gambar',
            'image' => 'Gambar',
            'video' => 'Video'
        ],
        'placeholder' => [
            'field1' => 'Masukan judul',
            'field2' => '',
            'field3' => '',
            'field4' => '',
        ],
        'type' => [
            0 => 'Gambar',
            1 => 'Video',
        ],
        'type_image' => [
            0 => 'Upload',
            1 => 'Filemanager',
            2 => 'URL'
        ],
        'type_video' => [
            0 => 'Upload',
            1 => 'Youtube ID'
        ],
    ],
];