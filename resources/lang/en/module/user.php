<?php

return [
    'user_management_caption' => 'User Management',
    'acl_caption' => 'ACL',
    //--- Role
    'role' => [
        'title' => 'Roles',
        'caption' => 'Role',
        'text' => 'List Role',
        'label' => [
            'field1' => 'Name',
            'field2' => 'Code',
            'field3' => 'Guard Name',
            'field4' => 'Level',
            'field5' => 'Role Register'
        ],
        'placeholder' => [
            'field1' => 'Enter namee',
            'field2' => '',
            'field3' => '',
            'field4' => '',
            'field5' => 'Show role during user registration'
        ],
    ],

    //--- Permission
    'permission' => [
        'title' => 'Permissions',
        'caption' => 'Permission',
        'text' => 'List Permission',
        'label' => [
            'field1' => 'Parent',
            'field2' => 'Name',
            'field3' => 'Code',
            'field4' => 'Guard Name'
        ],
        'placeholder' => [
            'field1' => '',
            'field2' => 'Enter name',
            'field3' => '',
            'field4' => '',
        ],
    ],

    //--- User
    'title' => 'Users',
    'caption' => 'User',
    'text' => 'List User',
    'label' => [
        'field1' => 'Name',
        'field2' => 'Email',
        'field3' => 'Username',
        'field4' => 'Phonee',
        'field5' => 'Password',
        'field6' => 'Repeat Password',
        'field7' => 'Current Password',
        'last_activity' => 'Last Activity',
        'ip_address' => 'IP Address',
        'photo' => 'Photo',
        'no_activity' => 'not activity yet',
    ],
    'placeholder' => [
        'field1' => 'Enter name',
        'field2' => 'Enter email',
        'field3' => 'Enter username',
        'field4' => 'xxxxxxxxxxxx',
        'field5' => 'Enter password',
        'field6' => 'Repeat password',
        'field7' => 'Enter current password',
    ],

    //--- Log
    'log' => [
        'title' => 'Logs',
        'caption' => 'Log',
        'text' => 'List Logs',
        'label' => [
            'field1' => 'IP Address',
            'field2' => 'Event',
            'field3' => 'Description',
            'field4' => 'Date',
        ],
        'you' => 'You'
    ],

    //--- Login Failed
    'login_failed' => [
        'title' => 'Login Failed',
        'caption' => 'Login Failed',
        'text' => 'List Login Failed',
        'label' => [
            'field1' => 'IP Address',
            'field2' => 'Username',
            'field3' => 'Password',
            'field4' => 'Date',
            'login_type' => 'Login Type'
        ],
    ],

    //profile
    'profile' => [
        'title' => 'Profile',
        'caption' => 'My Profile',
        'label' => [
            'tab1' => 'Account',
            'tab2' => 'Change Password (if you want)'
        ],
    ],

    //verification
    'verification' => [
        'warning' => 'Your email has not been verified & you will not receive an email notification.',
        'btn' => 'Verification now',
    ],

    //alert
    'alert' => [
        'verification_info' => 'Verification link has been sent, check email in inbox / spam',
        'verification_warning' => 'Send email is disabled, contact the developer to enable send email',
        'verification_success' => 'Email verified successfully',
        'photo_success' => 'Photo changed successfully',
        'warning_password_notmatch' => 'The previous password does not match, please try again',
        'warning_activate_expired' => 'The email activation link has expired',
        'activate_success' => 'User activation is successful'
    ],

    //info
    'password_info' => 'Password must use at least 1 upper & lower case letter, number & character(!@$#%). Example : adMin123#',
    'username_info' => 'Username must use lowercase & no spaces. Example: johndoe',
];