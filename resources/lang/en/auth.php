<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    'lock_warning' => 'You have failed to login :attr_failed time. 
        If you have failed :attr_failed_def times, the form will be closed for :attr_hour hours.',
    'lock_form_caption' => 'Login form is locked',
    'warning_forgot_password' => 'it is recommended to reset the password if you forget it',
    'login_request' => 'To access the page, you must login first',

    'back' => [
        'login' => 'Back to login'
    ],

    'login_backend' => [
        'title' => 'Authentication required',
        'text' => 'Login to your account',
        'label' => [
            'field1' => 'Username / Email',
            'field2' => 'Password',
            'field3' => 'Remember me',
            'signin' => 'Sign In',
        ],
        'placeholder' => [
            'field1' => 'Enter username / email',
            'field2' => 'Enter password',
        ],
        'alert' => [
            'exists' => 'The account you are trying to log into is not registered or has been deactivated',
            'success' => 'Login Successfully',
            'failed' => 'Username / Password is wrong, please try again!'
        ]
    ],

    'login_frontend' => [
        'title' => 'Login',
        'text' => 'Login to your account',
        'label' => [
            'field1' => 'Username / Email',
            'field2' => 'Password',
            'field3' => 'Remember me',
            'signin' => 'Sign In',
        ],
        'placeholder' => [
            'field1' => 'Enter username / email',
            'field2' => 'Enter password',
        ],
        'alert' => [
            'exists' => 'The account you are trying to log into is not registered or has been deactivated',
            'success' => 'Login Successfully',
            'failed' => 'Username / Password is wrong, please try again!'
        ]
    ],

    'logout' => [
        'title' => 'Log Out',
        'alert' => [
            'success' => 'Logout Successfully'
        ],
    ],

    'forgot_password' => [
        'title' => 'Forgot Password',
        'text' => 'Enter your email address and we will send you a link to reset your password.',
        'label' => [
            'field1' => 'Email',
            'send' => 'Send',
        ],
        'placeholder' => [
            'field1' => 'Enter email',
        ],
    ],

    'reset_password' => [
        'title' => 'Reset Password',
        'text' => 'Reset Password',
        'label' => [
            'field1' => 'New Password',
            'field2' => 'Repeat Password',
            'reset' => 'Reset Password',
        ],
        'placeholder' => [
            'field1' => 'Enter password',
            'field2' => 'Repeat password',
        ],
    ],

    'register' => [
        'title' => 'Register',
        'text' => 'Register Form',
        'label' => [
            'field1' => 'Full Name',
            'field2' => 'Email',
            'field3' => 'Username',
            'field4' => 'Password',
            'field5' => 'Repeat Password',
            'field6' => 'Phone',
            'dont_have_account' => 'Dont have an account yet?',
            'signup' => 'Register',
            'already_account' => 'Already have an account?',
            'agree' => 'By Clicking Register, you agree to our Terms & Conditions and Privacy Policy.',
        ],
        'placeholder' => [
            'field1' => 'Enter name',
            'field2' => 'Enter email',
            'field3' => 'Enter username',
            'field4' => 'Enter password',
            'field5' => 'Repeat password',
            'field6' => 'Enter phone',
        ],
        'alert' => [
            'success' => 'Register successfully',
            'info_active' => 'Your account has been activated, please login',
            'warning_expired' => 'The activation url has expired',
            'failed' => 'Registration failed, please try again'
        ],
    ],

    'activate' => [
        'title' => 'Activate Account',
        'text' => 'Please Enter email to send your account activation link',
        'label' => [
            'field1' => 'Email',
            'send' => 'Send',
        ],
        'placeholder' => [
            'field1' => 'Enter email',
        ],
        'alert' => [
            'info_active' => 'The activation link has been sent, please check your inbox/spam in your email',
            'exists' => 'The email you are trying to enter is already active/not registered',
        ],
    ],
];
