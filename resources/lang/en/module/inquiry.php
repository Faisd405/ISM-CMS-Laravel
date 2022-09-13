<?php

return [
    'title' => 'Inquiries',
    'caption' => 'Inquiry',
    'text' => 'Inquiry List',
    'label' => [
        'name' => 'Name',
        'slug' => 'Slug / URL',
        'body' => 'Body',
        'after_body' => 'After Body',
        'email' => 'Email (for email notification)',
        'longitude' => 'Longitude',
        'latitude' => 'Latitude',
        'lock_form' => 'Lock form after submit ?'
    ],
    'placeholder' => [
        'name' => 'Enter name',
    ],
    'field' => [
        'title' => 'Fields',
        'caption' => 'Field',
        'text' => 'Field List',
        'label' => [
            'label' => 'Label',
            'name' => 'Name',
            'form_type' => 'Form Type',
            'type' => 'Type',
            'id' => 'ID',
            'class' => 'Class',
            'attribute' => 'Attribute',
            'validation' => 'Validation',
            'properties' => 'Properties',
            'placeholder' => 'Placeholder',
            'option' => 'Options',
            'is_unique' => 'Unique Field (unique field checking validation)',
        ],
        'placeholder' => [
            'type' => 'Input type html',
            'id' => 'id html',
            'class' => 'class css html for tag form group',
            'attribute' => 'attribute html for tag form input',
            'validation' => 'laravel validation, example : required|email etc.',
        ],
        'validations' => [
            'required' => [
                'caption' => 'Required',
                'desc' => 'Field is required'
            ],
            'email' => [
                'caption' => 'Email',
                'desc' => 'Field must be email type'
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
        'submit_success' => 'Submit form successfully',
        'unique_warning' => 'The email / phone you entered already exists',
    ],
];