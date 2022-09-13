<?php

return [
    //header
    'view_frontend' => 'View Frontend',
    'backend_panel' => 'Backend Panel',

    //filter
    'filter' => 'Filter',
    'limit' => 'Per Page',
    'show_all' => 'Show All',
    'search' => 'Search',
    'search_keyword' => 'Enter keyword...',

    //form & data
    'form' => 'Form',
    'form_attr' => 'Form :attribute',
    'add' => 'Add',
    'add_attr' => 'Add :attribute',
    'add_new' => 'Add New',
    'add_attr_new' => 'Add :attribute New ',
    'edit' => 'Edit',
    'edit_attr' => 'Edit :attribute',
    'delete' => 'Delete',
    'delete_attr' => 'Delete :attribute',
    'data_attr_not_found' => ':attribute Not Found :(',
    'data_attr_empty' => 'Data :attribute empty',

    //button
    'save' => 'Save',
    'save_change' => 'Save Change',
    'save_exit' => 'Save & Back',
    'save_change_exit' => 'Save Change & Back',
    'reset' => 'Reset',
    'close' => 'Close',
    'submit' => 'Submit',
    'cancel' => 'Cancel',
    'change' => 'Edit',
    'back' => 'Back',
    'view_all' => 'View More',
    'show_more' => 'Show More',
    'delete_permanent' => 'Delete Permanene',
    'move_trash' => 'Move to trash',
    'restore' => 'Restore',
    'remove' => 'Delete',
    'detail' => 'Detail',
    'detail_info' => 'Have a detail page',
    'upload' => 'Upload',
    'download' => 'Download',
    'preview' => 'Preview',
    'import' => 'Import',
    'export' => 'Export',
    'template' => 'Template',
    'seo' => 'SEO',
    'meta_title' => 'Meta Title',
    'meta_description' => 'Meta Description',
    'meta_keywords' => 'Meta Keywords',

    //label
    'reply' => 'Reply',
    'drag_drop' => 'Drag / Drop',
    'trash' => 'Trash',
    'created' => 'Added',
    'updated' => 'Edited',
    'deleted' => 'Deleted',
    'status' => 'Status',
    'public' => 'Public',
    'approved' => 'Approved',
    'reject' => 'Reject',
    'locked' => 'Locked',
    'locked_info' => 'Data cannot be deleted',
    'position' => 'Position',
    'action' => 'Action',
    'select' => 'Select',
    'type' => 'Type',
    'show' => 'Show',
    'hide' => 'Hide',
    'by' => 'By',
    'event' => 'Event',
    'you' => 'You',
    'field_empty_attr' => '[:attribute empty]',
    'visitor' => 'Visitor',
    'type_file' => 'File Type',
    'max_upload' => 'Max Upload',
    'hits' => 'Hits',
    'forbidden' => 'You do not have access rights',
    'separated_comma' => 'Separated by comma (,)',
    'view_detail' => 'View Details',
    'lower_case' => 'It is recommended to use lowercase',
    'approval_info' => 'Requires approval',

    // form module
    'setting' => 'Setting',
    'custom' => 'Custom',
    'title' => 'Title',
    'alt' => 'ALT',
    'cover' => 'Cover',
    'banner' => 'Banner',
    'browse' => 'Browse',
    'language' => 'Language',

    //maintenance
    'maintenance' => [
        'title' => 'Maintenance',
        'text' => 'Website is under maintenance',
        'desc' => 'Please come back later...'
    ],

    //alert
    'alert' => [
        'success_caption' => 'Success',
        'failed_caption' => 'Failed',
        'info_caption' => 'Info',
        'warning_caption' => 'Warning',
        'wrong_text' => 'Somethings wrong!',
        'read_failed' => 'Get :attribute failed',
        'create_success' => ':attribute successfully added',
        'submit_success' => ':attribute successfully submited',
        'create_failed' => 'Add :attribute failed',
        'update_success' => ':attribute successfully edited',
        'update_failed' => 'Edit :attribute faileed',
        'delete_success' => ':attribute successfully deleted',
        'delete_failed' => 'Delete :attribute failed',
        'delete_failed_used' => 'Delete :attribute failed, still have relation to other data / data locked',
        'restore_success' => ':attribute successfully returned',
        'restore_failed' => ':attribute failed to restore because the main data has been deleted',
        'reset_success' => 'reset :attribute successfully',
        'reset_failed' => 'reset :attribute failed, please try again',
        'exists' => ':attribute already exists',
        //default alert modal
        'modal_ok_caption' => 'Ok',
        'modal_cancel_caption' => 'Close',
        //delete confirmation alert
        'delete_confirm_title' => "You will not be able to return this!",
        'delete_confirm_trash_title' => "The data will be moved to the trash!",
        'delete_confirm_restore_title' => "Data will be returned!",
        'delete_confirm_text' => 'Are you sure?',
        'delete_attr_confirm_text' => 'Are you sure you want to delete :attribute ?',
        'delete_btn_yes' => 'Yes, Delete',
        'delete_btn_cancel' => 'No, Thank You',
    ],

    //errors
    'errors' => [
        401 => [
            'title' => 'Unauthorize',
            'text' => '',
        ],
        403 => [
            'title' => 'Forbidden',
            'text' => 'You do not have permission to access / on this server.',
        ],
        404 => [
            'title' => 'Not Found',
            'text' => 'Sorry, the page you were looking for could not be found',
        ],
        419 => [
            'title' => 'Page Expired',
            'text' => 'Refresh your browser after clicking the back button',
        ],
        429 => [
            'title' => 'Too Many Requests',
            'text' => '',
        ],
        500 => [
            'title' => 'Server Error',
            'text' => 'Something went wrong on our server',
        ],
        503 => [
            'title' => 'Service Unavailable',
            'text' => '',
        ],
        'maintenance' => [
            'title' => 'Website is under maintenance',
            'text' => 'Please come back later...',
        ],
    ],

    //label
    'label' => [
        'active' => [
            0 => 'DEACTIVE',
            1 => 'ACTIVE'
        ],
        'email_verified' => [
            0 => 'NOT VERIFIED',
            1 => 'VERIFIED',
        ],
        'gender' => [
            0 => 'FEMALE',
            1 => 'MALE',
        ],
        'publish' => [
            1 => 'PUBLISH',
            0 => 'DRAF',
        ],
        'read' => [
            0 => 'NOT READ',
            1 => 'READ',
        ],
        'flags' => [
            0 => 'DISAPPROVED',
            1 => 'APPROVED',
        ],
        'optional' => [
            1 => 'YES',
            0 => 'NO',
        ],
        'event_log' => [
            0 => [
                'title' => 'DELETE',
                'desc' => 'Deleted'
            ],
            1 => [
                'title' => 'CREATE',
                'desc' => 'Added'
            ],
            2 => [
                'title' => 'EDIT',
                'desc' => 'Edited'
            ],
        ],
        'login_failed_type' => [
            0 => 'BACKEND',
            1 => 'FRONTEND'
        ],
    ],
];