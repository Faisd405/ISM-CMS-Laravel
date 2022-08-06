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
            'username' => 'Username / Email',
            'password' => 'Password',
            'remember' => 'Remember me',
            'signin' => 'Sign In',
        ],
        'placeholder' => [
            'username' => 'Enter username / email',
            'password' => 'Enter password',
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
            'username' => 'Username / Email',
            'password' => 'Password',
            'remember' => 'Remember me',
            'signin' => 'Sign In',
        ],
        'placeholder' => [
            'username' => 'Enter username / email',
            'password' => 'Enter password',
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
            'email' => 'Email',
            'send' => 'Send',
        ],
        'placeholder' => [
            'email' => 'Enter email',
        ],
    ],

    'reset_password' => [
        'title' => 'Reset Password',
        'text' => 'Reset Password',
        'label' => [
            'password' => 'New Password',
            'password_confirmation' => 'Repeat Password',
            'reset' => 'Reset Password',
        ],
        'placeholder' => [
            'password' => 'Enter password',
            'password_confirmation' => 'Repeat password',
        ],
    ],

    'register' => [
        'title' => 'Register',
        'text' => 'Register Form',
        'label' => [
            'name' => 'Full Name',
            'email' => 'Email',
            'username' => 'Username',
            'password' => 'Password',
            'password_confirmation' => 'Repeat Password',
            'phone' => 'Phone',
            'dont_have_account' => 'Dont have an account yet?',
            'signup' => 'Register',
            'already_account' => 'Already have an account?',
            'agree' => 'By Clicking Register, you agree to our Terms & Conditions and Privacy Policy.',
            'form_open' => 'Form will be opened on :attribute',
            'form_close' => 'Form has been closed, please come back later'
        ],
        'placeholder' => [
            'name' => 'Enter name',
            'email' => 'Enter email',
            'username' => 'Enter username',
            'password' => 'Enter password',
            'password_confirmation' => 'Repeat password',
            'phone' => 'Enter phone',
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
            'email' => 'Email',
            'send' => 'Send',
        ],
        'placeholder' => [
            'email' => 'Enter email',
        ],
        'alert' => [
            'info_active' => 'The activation link has been sent, please check your inbox/spam in your email',
            'exists' => 'The email you are trying to enter is already active/not registered',
        ],
    ],
];
