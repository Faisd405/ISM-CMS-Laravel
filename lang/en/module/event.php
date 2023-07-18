<?php

return [
    'title' => 'Events',
    'caption' => 'Event',
    'text' => 'Event List',
    'label' => [
        'name' => 'Name',
        'slug' => 'Slug / URL',
        'descrition' => 'Description',
        'form_description' => 'Form Description',
        'register_code' => 'Register Code',
        'place' => 'Place',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'email' => 'Email (for notification submit event)',
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
        'title' => 'Submit Forms',
        'caption' => 'Submit Form',
        'text' => 'Submit Form List',
        'label' => [
            'ip_address' => 'IP Address',
            'exported' => 'Exported',
            'submit_time' => 'Submit Time',
        ],
        'placeholder' => [
            
        ],
        'submit_success' => 'Submit form successfully',
        'unique_warning' => 'The email / phone you entered already exists',
        'form_open_warning' => 'Form not open yet',
        'form_close_warning' => 'Form closed',
    ],
];