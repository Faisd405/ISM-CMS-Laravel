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

    'failed' => 'Kredensial ini tidak cocok dengan catatan kami.',
    'password' => 'Kata sandi yang dimasukan salah.',
    'throttle' => 'Terlalu banyak upaya login. Silakan coba lagi dalam :seconds detik.',
    'lock_warning' => 'Anda sudah gagal login sebanyak :attr_failed kali. 
        Jika sudah :attr_failed_def gagal, maka form akan ditutup selama :attr_hour jam.',
    'lock_form_caption' => 'Form login dilock',
    'warning_forgot_password' => 'disarankan untuk mereset password jika lupa',
    'login_request' => 'Untuk mengakses halaman, anda harus login terlebih dahulu',

    'back' => [
        'login' => 'Kembali ke login'
    ],

    'login_backend' => [
        'title' => 'Authentication required',
        'text' => 'Masuk ke akun Anda',
        'label' => [
            'field1' => 'Username / Email',
            'field2' => 'Password',
            'field3' => 'Ingat Saya',
            'signin' => 'Sign In',
        ],
        'placeholder' => [
            'field1' => 'Masukan username / email',
            'field2' => 'Masukan password',
        ],
        'alert' => [
            'exists' => 'Akun yang Anda coba masuki tidak terdaftar atau telah dinonaktifkan',
            'success' => 'Login Berhasil',
            'failed' => 'Username / Kata Sandi salah, silakan coba lagi!'
        ]
    ],

    'login_frontend' => [
        'title' => 'Login',
        'text' => 'Masuk ke akun Anda',
        'label' => [
            'field1' => 'Username / Email',
            'field2' => 'Password',
            'field3' => 'Ingat Saya',
            'signin' => 'Sign In',
        ],
        'placeholder' => [
            'field1' => 'Masukan username / email',
            'field2' => 'Masukan password',
        ],
        'alert' => [
            'exists' => 'Akun yang Anda coba masuki tidak terdaftar atau telah dinonaktifkan',
            'success' => 'Login Berhasil',
            'failed' => 'Username / Kata Sandi salah, silakan coba lagi!'
        ]
    ],

    'logout' => [
        'title' => 'Log Out',
        'alert' => [
            'success' => 'Logout berhasil'
        ],
    ],

    'forgot_password' => [
        'title' => 'Lupa Password',
        'text' => 'Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk mereset kata sandi Anda.',
        'label' => [
            'field1' => 'Email',
            'send' => 'Kirim',
        ],
        'placeholder' => [
            'field1' => 'Masukan email',
        ],
    ],

    'reset_password' => [
        'title' => 'Reset Password',
        'text' => 'Reset Password',
        'label' => [
            'field1' => 'Password Baru',
            'field2' => 'Ulangi Password',
            'reset' => 'Reset Password',
        ],
        'placeholder' => [
            'field1' => 'Masukan password',
            'field2' => 'Ulangi password',
        ],
    ],

    'register' => [
        'title' => 'Register',
        'text' => 'Register Form',
        'label' => [
            'field1' => 'Nama Lengkap',
            'field2' => 'Email',
            'field3' => 'Username',
            'field4' => 'Password',
            'field5' => 'Ulangi Password',
            'field6' => 'Telpon',
            'dont_have_account' => 'Belum memiliki akun?',
            'signup' => 'Daftar',
            'already_account' => 'Sudah memiliki akun?',
            'agree' => 'Dengan Mengklik Daftar, Anda menyetujui Syarat & Ketentuan dan Kebijakan Privasi kami.',
        ],
        'placeholder' => [
            'field1' => 'Masukan nama',
            'field2' => 'Masukan email',
            'field3' => 'Masukan username',
            'field4' => 'Masukan password',
            'field5' => 'Ulangi password',
            'field6' => 'Masukan telpon',
        ],
        'alert' => [
            'success' => 'Register berhasil',
            'info_active' => 'Akun Anda sudah diaktifkan, silakan login',
            'warning_expired' => 'Url aktivasi sudah kedaluarsa',
            'failed' => 'Register gagal, silahkan coba lagi'
        ],
    ],

    'activate' => [
        'title' => 'Aktivasi Akun',
        'text' => 'Silahkan masukan email untuk mengirim link aktivasi akun anda',
        'label' => [
            'field1' => 'Email',
            'send' => 'Kirim',
        ],
        'placeholder' => [
            'field1' => 'Masukan email',
        ],
        'alert' => [
            'info_active' => 'Link aktivasi sudah dikirim, mohon untuk cek inbox / spam di email anda',
            'exists' => 'Email yang coba anda masukan sudah aktif / tidak terdaftar',
        ],
    ],
];
