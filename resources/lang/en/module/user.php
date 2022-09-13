<?php

return [
    'user_management_caption' => 'User Management',
    'acl_caption' => 'ACL',
    //--- Role
    'role' => [
        'title' => 'Roles',
        'caption' => 'Role',
        'text' => 'Role List',
        'label' => [
            'name' => 'Name',
            'code' => 'Code',
            'guard_name' => 'Guard Name',
            'level' => 'Level',
            'role_register' => 'Role Register'
        ],
        'placeholder' => [
            'name' => 'Enter name',
            'guard_name' => 'Default web',
            'role_register' => 'Show role during user registration'
        ],
    ],

    //--- Permission
    'permission' => [
        'title' => 'Permissions',
        'caption' => 'Permission',
        'text' => 'Permission List',
        'label' => [
            'parent' => 'Parent',
            'name' => 'Name',
            'code' => 'Code',
            'guard_name' => 'Guard Name'
        ],
        'placeholder' => [
            'name' => 'Enter name',
            'guard_name' => 'Default web',
        ],
    ],

    //--- User
    'title' => 'Users',
    'caption' => 'User',
    'text' => 'User List',
    'label' => [
        'name' => 'Name',
        'email' => 'Email',
        'username' => 'Username',
        'phone' => 'Phone',
        'password' => 'Password',
        'password_confirmation' => 'Repeat Password',
        'password_old' => 'Old Password',
        'last_activity' => 'Last Activity',
        'ip_address' => 'IP Address',
        'photo' => 'Photo',
        'no_activity' => 'No activity yet',
    ],
    'placeholder' => [
        'name' => 'Enter name',
        'email' => 'Enter email',
        'username' => 'Enter username',
        'phone' => '08xxxxxxxxxxx',
        'password' => 'Enter password',
        'password_confirmation' => 'Repeat password',
        'password_old' => 'Enter old password',
    ],

    //--- Log
    'log' => [
        'title' => 'Logs',
        'caption' => 'Log',
        'text' => 'Log List',
        'label' => [
            'ip_address' => 'IP Address',
            'event' => 'Event',
            'description' => 'Description',
            'date' => 'Date',
        ],
        'you' => 'You'
    ],

    //--- Login Failed
    'login_failed' => [
        'title' => 'Login Faileds',
        'caption' => 'Login Failed',
        'text' => 'Login Failed List',
        'label' => [
            'ip_address' => 'IP Address',
            'username' => 'Username',
            'password' => 'Password',
            'date' => 'Date',
            'login_type' => 'Login Type '
        ],
    ],

    //profile
    'profile' => [
        'title' => 'Profile',
        'caption' => 'My Profile',
        'label' => [
            'tab1' => 'Account',
            'tab2' => 'Change Password (if you want to change)'
        ],
    ],

    //verification
    'verification' => [
        'warning' => 'Your email has not been verified & you will not receive an email notification.',
        'btn' => 'Verify Now',
    ],

    //alert
    'alert' => [
        'verification_info' => 'Verification link has been sent, check email in inbox / spam',
        'verification_warning' => 'Send email is disabled, contact the developer to enable send email',
        'verification_success' => 'Email successfully verified',
        'photo_success' => 'Photo changed successfully',
        'warning_password_notmatch' => 'The previous password does not match, please try again',
        'warning_activate_expired' => 'The email activation link has expired',
        'activate_success' => 'User activation is successful'
    ],

    //info
    'password_info' => 'Password must use at least 1 upper & lower case letter, number & character(!@$#%). Example : adMin123#',
    'username_info' => 'Username must be in lowercase & not use spaces. Example: johndoe',
];