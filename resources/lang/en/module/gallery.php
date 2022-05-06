<?php

return [
    'caption' => 'Gallery',
    'category' => [
        'title' => 'Gallery Category',
        'caption' => 'Gallery Category',
        'text' => 'Gallery Category List',
        'manage' => 'Manage Categorry',
        'label' => [
            'field1' => 'Name',
            'field2' => 'Slug',
            'field3' => 'Description',
            'field4' => 'Preview Image',
            'field5' => 'Template List',
            'field6' => 'Template Detail',
            'field7' => 'Album Limit',
            'field8' => 'File Limit',
        ],
        'placeholder' => [
            'field1' => 'Enter name',
            'field2' => '',
            'field3' => '',
            'field4' => '',
            'field5' => '',
            'field6' => '',
            'field7' => ''
        ],
    ],
    'album' => [
        'title' => 'Album Gallery',
        'caption' => 'Album Gallery',
        'text' => 'List Album Gallery',
        'label' => [
            'field1' => 'Name',
            'field2' => 'Slug',
            'field3' => 'Description',
            'field4' => 'Image Preview',
            'field5' => 'File Limit',
        ],
        'placeholder' => [
            'field1' => 'Enter name',
            'field2' => '',
            'field3' => '',
            'field4' => '',
            'field5' => '',
        ],
    ],
    'file' => [
        'title' => 'Gallery File',
        'caption' => 'Gallery File',
        'text' => 'Gallery File List',
        'label' => [
            'field1' => 'Title',
            'field2' => 'Description',
            'field3' => 'File',
            'field4' => 'Thumbnail',
            'field5' => 'Youtube ID',
            'field6' => 'URL Image',
            'image' => 'Image',
            'video' => 'Video'
        ],
        'placeholder' => [
            'field1' => 'Enter title',
            'field2' => '',
            'field3' => '',
            'field4' => '',
        ],
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
    ],
];