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
            'name' => 'Nama',
            'code' => 'Kode',
            'guard_name' => 'Guard Name',
            'level' => 'Level',
            'role_register' => 'Role Register'
        ],
        'placeholder' => [
            'name' => 'Masukan nama',
            'guard_name' => 'Default web',
            'role_register' => 'Tampilkan role saat registrasi user'
        ],
    ],

    //--- Permission
    'permission' => [
        'title' => 'Permissions',
        'caption' => 'Permission',
        'text' => 'List Permission',
        'label' => [
            'parent' => 'Parent',
            'name' => 'Nama',
            'code' => 'Kode',
            'guard_name' => 'Guard Name'
        ],
        'placeholder' => [
            'name' => 'Masukan nama',
            'guard_name' => 'Default web',
        ],
    ],

    //--- User
    'title' => 'Users',
    'caption' => 'User',
    'text' => 'List User',
    'label' => [
        'name' => 'Nama',
        'email' => 'Email',
        'username' => 'Username',
        'phone' => 'Telpon',
        'password' => 'Password',
        'password_confirmation' => 'Ulangi Password',
        'password_old' => 'Password Lama',
        'last_activity' => 'Terakhir Aktivitas',
        'ip_address' => 'IP Address',
        'photo' => 'Foto',
        'no_activity' => 'Belum ada aktivitas',
    ],
    'placeholder' => [
        'name' => 'Masukan nama',
        'email' => 'Masukan email',
        'username' => 'Masukan username',
        'phone' => '08xxxxxxxxxxx',
        'password' => 'Masukan password',
        'password_confirmation' => 'Ulangi password',
        'password_old' => 'Masukan password lama',
    ],

    //--- Log
    'log' => [
        'title' => 'Logs',
        'caption' => 'Log',
        'text' => 'List Logs',
        'label' => [
            'ip_address' => 'IP Address',
            'event' => 'Event',
            'description' => 'Deskripsi',
            'date' => 'Tanggal',
        ],
        'you' => 'Anda'
    ],

    //--- Login Failed
    'login_failed' => [
        'title' => 'Login Gagal',
        'caption' => 'Login Gagal',
        'text' => 'List Login Gagal',
        'label' => [
            'ip_address' => 'IP Address',
            'username' => 'Username',
            'password' => 'Password',
            'date' => 'Tanggal',
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
    'password_info' => 'Password minimal harus menggunakan 1 huruf besar & kecil, angka & karakter(!@$#%). Contoh : adMin123#',
    'username_info' => 'Username harus menggunakan huruf kecil & tidak menggunakan spasi. Contoh : johndoe',
];