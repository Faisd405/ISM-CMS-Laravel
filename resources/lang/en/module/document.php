<?php

return [
    'caption' => 'Document',
    'category' => [
        'title' => 'Document Category',
        'caption' => 'Document Category',
        'text' => 'Document Category List',
        'label' => [
            'field1' => 'Name',
            'field2' => 'Slug',
            'field3' => 'Description',
            'field4' => 'Role Download',
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
        'title' => 'Document File',
        'caption' => 'Document File',
        'text' => 'Document File List',
        'label' => [
            'field1' => 'Title',
            'field2' => 'Description',
            'field3' => 'File',
            'field4' => 'URL File',
            'file' => 'File'
        ],
        'placeholder' => [
            'field1' => 'Enter title',
        ],
        'type' => [
            0 => 'UPLOAD',
            1 => 'FILEMANAGER',
            2 => 'URL'
        ],
    ]
];