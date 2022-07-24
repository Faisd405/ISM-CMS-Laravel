<?php

return [
    'title' => 'Inquiry',
    'caption' => 'Inquiry',
    'text' => 'Inquiry List',
    'label' => [
        'field1' => 'Name',
        'field2' => 'Slug / URL',
        'field3' => 'Body',
        'field4' => 'After Body',
        'field5' => 'Show Form',
        'field6' => 'Email (for message notification)',
        'field7' => 'Show Map',
        'field8' => 'Longitude',
        'field9' => 'Latitude',
        'lock_form' => 'Lock the form after submit?'
    ],
    'placeholder' => [
        'field1' => 'Enter name',
        'field2' => '',
        'field3' => '',
        'field4' => '',
        'field5' => 'YES',
        'field6' => '',
        'field7' => 'YES',
        'field8' => '',
        'field9' => '',
        'field10' => '',
        'field11' => '',
        'field12' => '',
    ],
    'field' => [
        'title' => 'Fields',
        'caption' => 'Field',
        'text' => 'List Field',
        'label' => [
            'field1' => 'Label',
            'field2' => 'Name',
            'field3' => 'Form Type',
            'field4' => 'Type',
            'field5' => 'ID',
            'field6' => 'Class',
            'field7' => 'Attribute',
            'field8' => 'Validation',
            'field9' => 'Properties',
            'field10' => 'Placeholder',
            'field11' => 'Options',
            'is_unique' => 'Unique Field (uniq field check validation)',
        ],
        'placeholder' => [
            'field1' => '',
            'field2' => '',
            'field3' => '',
            'field4' => 'Input type html',
            'field5' => 'id html',
            'field6' => 'class css html for tag form group',
            'field7' => 'attribute html form tag input',
            'field8' => 'laravel validation, example : required|email etc.',
            'field9' => '',
            'field10' => '',
        ],
        'validations' => [
            'required' => [
                'caption' => 'Required',
                'desc' => 'Field is required'
            ],
            'email' => [
                'caption' => 'Email',
                'desc' => 'Field must email type'
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
        'submit_success' => 'Submit form success',
        'unique_warning' => 'Email / Phone already exists',
    ],
];