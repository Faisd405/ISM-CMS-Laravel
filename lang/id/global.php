<?php

return [
    //header
    'view_frontend' => 'Halaman Website',
    'backend_panel' => 'Backend Panel',

    //filter
    'filter' => 'Filter',
    'limit' => 'Per Halaman',
    'show_all' => 'Tampilkan Semua',
    'search' => 'Pencarian',
    'search_keyword' => 'Masukan kata kunci...',

    //form & data
    'form' => 'Form',
    'form_attr' => 'Form :attribute',
    'add' => 'Tambah',
    'add_attr' => 'Tambah :attribute',
    'add_new' => 'Tambah Baru',
    'add_attr_new' => 'Tambah :attribute Baru ',
    'edit' => 'Ubah',
    'edit_attr' => 'Ubah :attribute',
    'delete' => 'Hapus',
    'delete_attr' => 'Hapus :attribute',
    'data_attr_not_found' => ':attribute Tidak ditemukan :(',
    'data_attr_empty' => 'Data :attribute Kosong',

    //button
    'save' => 'Simpan',
    'save_change' => 'Simpan Perubahan',
    'save_exit' => 'Simpan & Kembali',
    'save_change_exit' => 'Simpan Perubahan & Kembali',
    'reset' => 'Reset',
    'close' => 'Tutup',
    'submit' => 'Submit',
    'cancel' => 'Batal',
    'change' => 'Ubah',
    'back' => 'Kembali',
    'view_all' => 'Lihat Selengkapnya',
    'show_more' => 'Tampilkan Lebih',
    'delete_permanent' => 'Hapus Permanen',
    'move_trash' => 'Pindah ke Tong Sampah',
    'restore' => 'Kembalikan',
    'remove' => 'Hapus',
    'detail' => 'Detail',
    'detail_info' => 'Memiliki halaman detail',
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
    'reply' => 'Balas',
    'drag_drop' => 'Drag / Drop',
    'trash' => 'Tong Sampah',
    'created' => 'Ditambahkan',
    'updated' => 'Diperbarui',
    'deleted' => 'Dihapus',
    'status' => 'Status',
    'public' => 'Publik',
    'approved' => 'Approved',
    'reject' => 'Reject',
    'locked' => 'Locked',
    'locked_info' => 'Data tidak bisa dihapus (dikunci)',
    'position' => 'Posisi',
    'action' => 'Aksi',
    'select' => 'Pilih',
    'type' => 'Tipe',
    'show' => 'Tampilkan',
    'hide' => 'Sembunyikan',
    'by' => 'Oleh',
    'event' => 'Event',
    'you' => 'Anda',
    'field_empty_attr' => '[:attribute Kosong]',
    'visitor' => 'Pengunjung',
    'type_file' => 'File Tipe',
    'max_upload' => 'Maksimal Upload',
    'hits' => 'Hits',
    'forbidden' => 'Anda tidak memiliki hak akses',
    'separated_comma' => 'Dipisahkan dengan koma (,)',
    'view_detail' => 'Lihat Detail',
    'lower_case' => 'Disarankan menggunakan huruf kecil',
    'approval_info' => 'Membutuhkan persetujuan',
    'unread_message' => 'Pesan belum dibaca',

    // form module
    'setting' => 'Setting',
    'custom' => 'Custom',
    'title' => 'Title',
    'alt' => 'ALT',
    'cover' => 'Cover',
    'banner' => 'Banner',
    'browse' => 'Browse',
    'language' => 'Bahasa',

    //maintenance
    'maintenance' => [
        'title' => 'Maintenance',
        'text' => 'Website sedang dalam pemeliharaan',
        'desc' => 'Silahkan kembali lagi nanti...'
    ],

    //alert
    'alert' => [
        'success_caption' => 'Berhasil',
        'failed_caption' => 'Gagal',
        'info_caption' => 'Info',
        'warning_caption' => 'Peringatan',
        'wrong_text' => 'Ada yang tidak beres!',
        'read_failed' => 'Get :attribute gagal',
        'create_success' => ':attribute berhasil ditambahkan',
        'submit_success' => ':attribute berhasil disubmit',
        'create_failed' => 'Tambah :attribute gagal',
        'update_success' => ':attribute berhasil diubah',
        'update_failed' => 'Edit :attribute gagal',
        'delete_success' => ':attribute berhasil dihapus',
        'delete_failed' => 'Hapus :attribute gagal',
        'delete_failed_used' => 'Hapus :attribute gagal, masih memiliki relasi ke data lain / data dilock',
        'restore_success' => ':attribute berhasil di kembalikan',
        'restore_failed' => ':attribute gagal di kembalikan dikarenakan data utama sudah dihapus',
        'reset_success' => 'reset :attribute berhasil',
        'reset_failed' => 'reset :attribute gagal, silahkan coba lagi',
        'exists' => ':attribute sudah ada',
        //default alert modal
        'modal_ok_caption' => 'Ok',
        'modal_cancel_caption' => 'Tutup',
        //delete confirmation alert
        'delete_confirm_title' => "Anda tidak akan dapat mengembalikan ini!",
        'delete_confirm_trash_title' => "Data akan dipindahkan ke sampah!",
        'delete_confirm_restore_title' => "Data akan dikembalikan!",
        'delete_confirm_text' => 'Apakah anda yakin?',
        'delete_attr_confirm_text' => 'Apakah anda yakin akan menghapus :attribute ?',
        'delete_btn_yes' => 'Ya, Hapus',
        'delete_btn_cancel' => 'Tidak, Terima Kasih',
    ],

    //errors
    'errors' => [
        401 => [
            'title' => 'Pembatalan Otoritas',
            'text' => '',
        ],
        403 => [
            'title' => 'Akses Ditolak',
            'text' => 'Anda tidak memiliki izin untuk mengakses / di server ini.',
        ],
        404 => [
            'title' => 'Tidak Ditemukan',
            'text' => 'Maaf, halaman yang anda cari tidak ditemukan',
        ],
        419 => [
            'title' => 'Halaman Kedaluarsa',
            'text' => 'Refresh browser Anda setelah mengklik tombol kembali',
        ],
        429 => [
            'title' => 'Terlalu banyak permintaan',
            'text' => '',
        ],
        500 => [
            'title' => 'Server Error',
            'text' => 'Ada yang tidak beres di server kami',
        ],
        503 => [
            'title' => 'Layanan Tidak Tersedia',
            'text' => '',
        ],
        'maintenance' => [
            'title' => 'Situs Web Dalam Pemeliharaan',
            'text' => 'Silahkan Kembali Nanti',
        ],
    ],

    //label
    'label' => [
        'active' => [
            0 => 'TIDAK AKTIF',
            1 => 'AKTIF'
        ],
        'email_verified' => [
            0 => 'BELUM DIVERIFIKASI',
            1 => 'DIVERIFIKASI',
        ],
        'gender' => [
            0 => 'PEREMPUAN',
            1 => 'LAKI - LAKI',
        ],
        'publish' => [
            1 => 'PUBLISH',
            0 => 'DRAF',
        ],
        'read' => [
            0 => 'BELUM DIBACA',
            1 => 'SUDAH DIBACA',
        ],
        'flags' => [
            0 => 'TIDAK DISETUJUI',
            1 => 'DISETUJUI',
        ],
        'optional' => [
            1 => 'YA',
            0 => 'TIDAK',
        ],
        'event_log' => [
            0 => [
                'title' => 'HAPUS',
                'desc' => 'Menghapus'
            ],
            1 => [
                'title' => 'TAMBAH',
                'desc' => 'Menambahkan'
            ],
            2 => [
                'title' => 'EDIT',
                'desc' => 'Mengedit'
            ],
        ],
        'login_failed_type' => [
            0 => 'BACKEND',
            1 => 'FRONTEND'
        ],
        'select_empty' => 'Pilih :attribute',
    ],
];
