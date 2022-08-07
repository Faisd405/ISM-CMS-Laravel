<?php

return [
    'title' => 'Event',
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
        'email' => 'Email (for submit event notifications)',
        'lock_form' => 'Lock the form after submit?'
    ],
    'placeholder' => [
        'name' => 'Enter name',
    ],
    'field' => [
        'title' => 'Fields',
        'caption' => 'Field',
        'text' => 'List Field',
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
            'is_unique' => 'Unique Field (uniq field check validation)',
        ],
        'placeholder' => [
            'type' => 'Input type html',
            'id' => 'id html',
            'class' => 'class css html for tag form group',
            'attribute' => 'attribute html form tag input',
            'validation' => 'laravel validation, example : required|email etc.',
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
            'ip_address' => 'IP Address',
            'exported' => 'Exported',
            'submit_time' => 'Submit Time',
        ],
        'placeholder' => [
            
        ],
        'submit_success' => 'Submit form success',
        'unique_warning' => 'Email / Phone already exists',
        'form_open_warning' => 'The form hasnt been opened yet',
        'form_close_warning' => 'The form has been closed',
    ],
];