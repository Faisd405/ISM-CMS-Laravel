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
            'field1' => 'Nama',
            'field2' => 'Penulisan',
            'field3' => 'Guard Name',
            'field4' => 'Level',
            'field5' => 'Role Register'
        ],
        'placeholder' => [
            'field1' => 'Masukan nama',
            'field2' => '',
            'field3' => '',
            'field4' => '',
            'field5' => ''
        ],
    ],

    //--- Permission
    'permission' => [
        'title' => 'Permissions',
        'caption' => 'Permission',
        'text' => 'List Permission',
        'label' => [
            'field1' => 'Parent',
            'field2' => 'Nama',
            'field3' => 'Penulisan',
            'field4' => 'Guard Name'
        ],
        'placeholder' => [
            'field1' => '',
            'field2' => 'Masukan nama',
            'field3' => '',
            'field4' => '',
        ],
    ],

    //--- User
    'title' => 'Users',
    'caption' => 'User',
    'text' => 'List User',
    'label' => [
        'field1' => 'Nama',
        'field2' => 'Email',
        'field3' => 'Username',
        'field4' => 'Telpon',
        'field5' => 'Password',
        'field6' => 'Ulangi Password',
        'field7' => 'Password Lama',
        'last_activity' => 'Terakhir Aktivitas',
        'ip_address' => 'IP Address',
        'photo' => 'Foto',
        'no_activity' => 'Belum ada aktivitas',
    ],
    'placeholder' => [
        'field1' => 'Masukan nama',
        'field2' => 'Masukan email',
        'field3' => 'Masukan username',
        'field4' => 'xxxxxxxxxxxx',
        'field5' => 'Masukan password',
        'field6' => 'Ulangi password',
        'field7' => 'Masukan password lama',
    ],

    //--- Log
    'log' => [
        'title' => 'Logs',
        'caption' => 'Log',
        'text' => 'List Logs',
        'label' => [
            'field1' => 'IP Address',
            'field2' => 'Event',
            'field3' => 'Deskripsi',
            'field4' => 'Tanggal',
        ],
        'you' => 'Anda'
    ],

    //--- Login Failed
    'login_failed' => [
        'title' => 'Login Gagal',
        'caption' => 'Login Gagal',
        'text' => 'List Login Gagal',
        'label' => [
            'field1' => 'IP Address',
            'field2' => 'Username',
            'field3' => 'Password',
            'field4' => 'Tanggal',
            'login_type' => 'Tipe Login '
        ],
    ],

    //profile
    'profile' => [
        'title' => 'Profile',
        'caption' => 'Profile Saya',
        'label' => [
            'tab1' => 'Akun',
            'tab2' => 'Ganti Password (jika ingin diubah)'
        ],
    ],

    //verification
    'verification' => [
        'warning' => 'Email anda belum di verifikasi & anda tidak akan mendapatkan notifikasi lewat email.',
        'btn' => 'Verifikasi Sekarang',
    ],

    //alert
    'alert' => [
        'verification_info' => 'Link verifikasi sudah dikirm, periksa email di inbox / spam',
        'verification_warning' => 'Kirim email dinonaktifkan, hubungi developer untuk mengaktifkan kirim email',
        'verification_success' => 'Email berhasil diverifikasi',
        'photo_success' => 'Foto berhasil diubah',
        'warning_password_notmatch' => 'Password sebelumnya tidak sesuai, silahkan coba lagi',
        'warning_activate_expired' => 'Link aktivasi email sudah kedaluarsa',
        'activate_success' => 'Aktivasi user berhasil'
    ],

    //info
    'password_info' => 'password minimal harus menggunakan 1 huruf besar & kecil, angka & karakter. Contoh : adMin123#',
    'username_info' => 'username harus menggunakan huruf kecil & tidak menggunakan spasi. Contoh : johndoe',
];