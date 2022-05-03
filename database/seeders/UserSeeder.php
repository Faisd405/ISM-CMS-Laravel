<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * ROLES
         */
        $roles = [
            [
                'name' => 'super',
                'level' => 1,
                'locked' => true
            ],
            [
                'name' => 'support',
                'level' => 2,
                'locked' => true
            ],
            [
                'name' => 'admin',
                'level' => 3,
                'locked' => true
            ],
            [
                'name' => 'editor',
                'level' => 4,
                'locked' => true
            ],
        ];

        foreach ($roles as $item) {
            Role::create([
                'name' => $item['name'],
                'level' => $item['level'],
                'guard_name' => 'web',
                'locked' => $item['locked']
            ]);
        }

        /**
         * PERMISSION
         */
        $permissions = [
            0 => [
                'parent' => 0,
                'name' => 'users',
                'locked' => true
            ],
            1 => [
                'parent' => 1,
                'name' => 'user_create',
                'locked' => true
            ],
            2 => [
                'parent' => 1,
                'name' => 'user_update',
                'locked' => true
            ],
            3 => [
                'parent' => 1,
                'name' => 'user_delete',
                'locked' => true
            ],
            4 => [
                'parent' => 0,
                'name' => 'regionals',
                'locked' => true
            ],
            5 => [
                'parent' => 5,
                'name' => 'regional_create',
                'locked' => true
            ],
            6 => [
                'parent' => 5,
                'name' => 'regional_update',
                'locked' => true
            ],
            7 => [
                'parent' => 5,
                'name' => 'regional_delete',
                'locked' => true
            ],
            8 => [
                'parent' => 0,
                'name' => 'configurations',
                'locked' => true
            ],
            9 => [
                'parent' => 0,
                'name' => 'visitor',
                'locked' => true
            ],
            10 => [
                'parent' => 0,
                'name' => 'filemanager',
                'locked' => true
            ],
            11 => [
                'parent' => 0,
                'name' => 'languages',
                'locked' => true
            ],
            12 => [
                'parent' => 12,
                'name' => 'language_create',
                'locked' => true
            ],
            13 => [
                'parent' => 12,
                'name' => 'language_update',
                'locked' => true
            ],
            14 => [
                'parent' => 12,
                'name' => 'language_delete',
                'locked' => true
            ],
            15 => [
                'parent' => 0,
                'name' => 'registrations',
                'locked' => true
            ],
            16 => [
                'parent' => 16,
                'name' => 'registration_create',
                'locked' => true
            ],
            17 => [
                'parent' => 16,
                'name' => 'registration_update',
                'locked' => true
            ],
            18 => [
                'parent' => 16,
                'name' => 'registration_delete',
                'locked' => true
            ],
            19 => [
                'parent' => 0,
                'name' => 'apis',
                'locked' => true
            ],
            20 => [
                'parent' => 20,
                'name' => 'api_create',
                'locked' => true
            ],
            21 => [
                'parent' => 20,
                'name' => 'api_update',
                'locked' => true
            ],
            22 => [
                'parent' => 20,
                'name' => 'api_delete',
                'locked' => true
            ],
            23 => [
                'parent' => 0,
                'name' => 'menus',
                'locked' => true
            ],
            24 => [
                'parent' => 24,
                'name' => 'menu_create',
                'locked' => true
            ],
            25 => [
                'parent' => 24,
                'name' => 'menu_update',
                'locked' => true
            ],
            26 => [
                'parent' => 24,
                'name' => 'menu_delete',
                'locked' => true
            ],
            27 => [
                'parent' => 0,
                'name' => 'templates',
                'locked' => true
            ],
            28 => [
                'parent' => 28,
                'name' => 'template_create',
                'locked' => true
            ],
            29 => [
                'parent' => 28,
                'name' => 'template_update',
                'locked' => true
            ],
            30 => [
                'parent' => 28,
                'name' => 'template_delete',
                'locked' => true
            ],
            31 => [
                'parent' => 0,
                'name' => 'tags',
                'locked' => true
            ],
            32 => [
                'parent' => 32,
                'name' => 'tag_create',
                'locked' => true
            ],
            33 => [
                'parent' => 32,
                'name' => 'tag_update',
                'locked' => true
            ],
            34 => [
                'parent' => 32,
                'name' => 'tag_delete',
                'locked' => true
            ],
            35 => [
                'parent' => 0,
                'name' => 'medias',
                'locked' => true
            ],
            36 => [
                'parent' => 36,
                'name' => 'media_create',
                'locked' => true
            ],
            37 => [
                'parent' => 36,
                'name' => 'media_update',
                'locked' => true
            ],
            38 => [
                'parent' => 36,
                'name' => 'media_delete',
                'locked' => true
            ],
            39 => [
                'parent' => 0,
                'name' => 'pages',
                'locked' => true
            ],
            40 => [
                'parent' => 40,
                'name' => 'page_create',
                'locked' => true
            ],
            41 => [
                'parent' => 40,
                'name' => 'page_update',
                'locked' => true
            ],
            42 => [
                'parent' => 40,
                'name' => 'page_delete',
                'locked' => true
            ],
            43 => [
                'parent' => 0,
                'name' => 'content_sections',
                'locked' => true
            ],
            44 => [
                'parent' => 44,
                'name' => 'content_section_create',
                'locked' => true
            ],
            45 => [
                'parent' => 44,
                'name' => 'content_section_update',
                'locked' => true
            ],
            46 => [
                'parent' => 44,
                'name' => 'content_section_delete',
                'locked' => true
            ],
            47 => [
                'parent' => 0,
                'name' => 'content_categories',
                'locked' => true
            ],
            48 => [
                'parent' => 48,
                'name' => 'content_category_create',
                'locked' => true
            ],
            49 => [
                'parent' => 48,
                'name' => 'content_category_update',
                'locked' => true
            ],
            50 => [
                'parent' => 48,
                'name' => 'content_category_delete',
                'locked' => true
            ],
            51 => [
                'parent' => 0,
                'name' => 'content_posts',
                'locked' => true
            ],
            52 => [
                'parent' => 52,
                'name' => 'content_post_create',
                'locked' => true
            ],
            53 => [
                'parent' => 52,
                'name' => 'content_post_update',
                'locked' => true
            ],
            54 => [
                'parent' => 52,
                'name' => 'content_post_delete',
                'locked' => true
            ],
            55 => [
                'parent' => 0,
                'name' => 'banner_categories',
                'locked' => true
            ],
            56 => [
                'parent' => 56,
                'name' => 'banner_category_create',
                'locked' => true
            ],
            57 => [
                'parent' => 56,
                'name' => 'banner_category_update',
                'locked' => true
            ],
            58 => [
                'parent' => 56,
                'name' => 'banner_category_delete',
                'locked' => true
            ],
            59 => [
                'parent' => 0,
                'name' => 'banners',
                'locked' => true
            ],
            60 => [
                'parent' => 60,
                'name' => 'banner_create',
                'locked' => true
            ],
            61 => [
                'parent' => 60,
                'name' => 'banner_update',
                'locked' => true
            ],
            62 => [
                'parent' => 60,
                'name' => 'banner_delete',
                'locked' => true
            ],
            63 => [
                'parent' => 0,
                'name' => 'gallery_categories',
                'locked' => true
            ],
            64 => [
                'parent' => 64,
                'name' => 'gallery_category_create',
                'locked' => true
            ],
            65 => [
                'parent' => 64,
                'name' => 'gallery_category_update',
                'locked' => true
            ],
            66 => [
                'parent' => 64,
                'name' => 'gallery_category_delete',
                'locked' => true
            ],
            67 => [
                'parent' => 0,
                'name' => 'gallery_albums',
                'locked' => true
            ],
            68 => [
                'parent' => 68,
                'name' => 'gallery_album_create',
                'locked' => true
            ],
            69 => [
                'parent' => 68,
                'name' => 'gallery_album_update',
                'locked' => true
            ],
            70 => [
                'parent' => 68,
                'name' => 'gallery_album_delete',
                'locked' => true
            ],
            71 => [
                'parent' => 0,
                'name' => 'gallery_files',
                'locked' => true
            ],
            72 => [
                'parent' => 72,
                'name' => 'gallery_file_create',
                'locked' => true
            ],
            73 => [
                'parent' => 72,
                'name' => 'gallery_file_update',
                'locked' => true
            ],
            74 => [
                'parent' => 72,
                'name' => 'gallery_file_delete',
                'locked' => true
            ],
            75 => [
                'parent' => 0,
                'name' => 'document_categories',
                'locked' => true
            ],
            76 => [
                'parent' => 76,
                'name' => 'document_category_create',
                'locked' => true
            ],
            77 => [
                'parent' => 76,
                'name' => 'document_category_update',
                'locked' => true
            ],
            78 => [
                'parent' => 76,
                'name' => 'document_category_delete',
                'locked' => true
            ],
            79 => [
                'parent' => 0,
                'name' => 'document_files',
                'locked' => true
            ],
            80 => [
                'parent' => 80,
                'name' => 'document_file_create',
                'locked' => true
            ],
            81 => [
                'parent' => 80,
                'name' => 'document_file_update',
                'locked' => true
            ],
            82 => [
                'parent' => 80,
                'name' => 'document_file_delete',
                'locked' => true
            ],
            83 => [
                'parent' => 0,
                'name' => 'link_categories',
                'locked' => true
            ],
            84 => [
                'parent' => 84,
                'name' => 'link_category_create',
                'locked' => true
            ],
            85 => [
                'parent' => 84,
                'name' => 'link_category_update',
                'locked' => true
            ],
            86 => [
                'parent' => 84,
                'name' => 'link_category_delete',
                'locked' => true
            ],
            87 => [
                'parent' => 0,
                'name' => 'link_medias',
                'locked' => true
            ],
            88 => [
                'parent' => 88,
                'name' => 'link_media_create',
                'locked' => true
            ],
            89 => [
                'parent' => 88,
                'name' => 'link_media_update',
                'locked' => true
            ],
            90 => [
                'parent' => 88,
                'name' => 'link_media_delete',
                'locked' => true
            ],
            91 => [
                'parent' => 0,
                'name' => 'inquiries',
                'locked' => true
            ],
            92 => [
                'parent' => 92,
                'name' => 'inquiry_create',
                'locked' => true
            ],
            93 => [
                'parent' => 92,
                'name' => 'inquiry_update',
                'locked' => true
            ],
            94 => [
                'parent' => 92,
                'name' => 'inquiry_delete',
                'locked' => true
            ],
            95 => [
                'parent' => 0,
                'name' => 'inquiry_fields',
                'locked' => true
            ],
            96 => [
                'parent' => 96,
                'name' => 'inquiry_field_create',
                'locked' => true
            ],
            97 => [
                'parent' => 96,
                'name' => 'inquiry_field_update',
                'locked' => true
            ],
            98 => [
                'parent' => 96,
                'name' => 'inquiry_field_delete',
                'locked' => true
            ],
        ];

        foreach ($permissions as $item) {
            Permission::create([
                'parent' => $item['parent'],
                'name' => $item['name'],
                'guard_name' => 'web',
                'locked' => $item['locked'],
            ]);
        }

        /**
         * USER
         */
        $users = [
            [
                'name' => 'Developer 4VM',
                'email' => 'developer@4visionmedia.com',
                'email_verified' => 1,
                'email_verified_at' => now(),
                'username' => '4vmSuper',
                'password' => Hash::make('cmsSup3r#'),
                'active' => 1,
                'active_at' => now(),
                'roles' => 'super',
                'locked' => true
            ],
            [
                'name' => 'Support 4VM',
                'email' => 'support@4visionmedia.com',
                'email_verified' => 1,
                'email_verified_at' => now(),
                'username' => '4vmSupport',
                'password' => Hash::make('cmsSupp0rt#'),
                'active' => 1,
                'active_at' => now(),
                'roles' => 'support',
                'locked' => true
            ],
            [
                'name' => 'Admin Website',
                'email' => 'admin@admin.com',
                'email_verified' => 0,
                'email_verified_at' => null,
                'username' => 'adminWeb',
                'password' => Hash::make('adm1nWeb#'),
                'active' => 1,
                'active_at' => now(),
                'roles' => 'admin',
                'locked' => true
            ],
            [
                'name' => 'Editor Website',
                'email' => 'editor@editor.com',
                'email_verified' => 0,
                'email_verified_at' => null,
                'username' => 'editorWeb',
                'password' => Hash::make('edit0rWeb#'),
                'active' => 1,
                'active_at' => now(),
                'roles' => 'editor',
                'locked' => true
            ]
        ];

        foreach ($users as $value) {
            
            $user = User::create([
                'name' => $value['name'],
                'email' => $value['email'],
                'email_verified' => $value['email_verified'],
                'email_verified_at' => $value['email_verified_at'],
                'username' => $value['username'],
                'password' => $value['password'],
                'active' => $value['active'],
                'active_at' => $value['active_at'],
                'locked' => $value['locked'],
            ]);

            $user->assignRole($value['roles']);
        }

        /**
         * ROLE & PERMISSION
         */
        foreach (Permission::all() as $value) {
            DB::table('role_has_permissions')->insert([
                'role_id' => 1,
                'permission_id' => $value->id
            ]);
        }

        //custom permission
        // $permissions = [];
        // foreach ($permissions as $key => $value) {
        //     DB::table('role_has_permissions')->insert([
        //         'role_id' => 2,
        //         'permission_id' => $value
        //     ]);
        // }

        // foreach ($permissions as $key => $value) {
        //     DB::table('role_has_permissions')->insert([
        //         'role_id' => 3,
        //         'permission_id' => $value
        //     ]);
        // }
    }
}
