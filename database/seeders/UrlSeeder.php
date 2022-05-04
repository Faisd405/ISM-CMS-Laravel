<?php

namespace Database\Seeders;

use App\Models\IndexingUrl;
use Illuminate\Database\Seeder;

class UrlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $indexing = [
            [
                'slug' => 'backend',
                'module' => null,
                'id' => null,
                'type' => null,
                'locked' => true
            ],
            [
                'slug' => 'login',
                'module' => null,
                'id' => null,
                'type' => null,
                'locked' => true
            ],
            [
                'slug' => 'sign-in',
                'module' => null,
                'id' => null,
                'type' => null,
                'locked' => true
            ],
            [
                'slug' => 'register',
                'module' => null,
                'id' => null,
                'type' => null,
                'locked' => true
            ],
            [
                'slug' => 'sign-up',
                'module' => null,
                'id' => null,
                'type' => null,
                'locked' => true
            ],
            [
                'slug' => 'forgot-password',
                'module' => null,
                'id' => null,
                'type' => null,
                'locked' => true
            ],
            [
                'slug' => 'reset-password',
                'module' => null,
                'id' => null,
                'type' => null,
                'locked' => true
            ],
            [
                'slug' => 'admin',
                'module' => null,
                'id' => null,
                'type' => null,
                'locked' => true
            ],
            [
                'slug' => 'feed',
                'module' => null,
                'id' => null,
                'type' => null,
                'locked' => true
            ],
            [
                'slug' => 'landing',
                'module' => null,
                'id' => null,
                'type' => null,
                'locked' => true
            ],
            [
                'slug' => 'search',
                'module' => null,
                'id' => null,
                'type' => null,
                'locked' => true
            ],
            [
                'slug' => config('cms.module.feature.language.default'),
                'module' => null,
                'id' => null,
                'type' => null,
                'locked' => true,
            ],
            [
                'slug' => 'page',
                'module' => null,
                'id' => null,
                'type' => null,
                'locked' => true
            ],
            [
                'slug' => 'content',
                'module' => null,
                'id' => null,
                'type' => null,
                'locked' => true
            ],
            [
                'slug' => 'gallery',
                'module' => null,
                'id' => null,
                'type' => null,
                'locked' => true
            ],
            [
                'slug' => 'document',
                'module' => null,
                'id' => null,
                'type' => null,
                'locked' => true
            ],
            [
                'slug' => 'link',
                'module' => null,
                'id' => null,
                'type' => null,
                'locked' => true
            ],
            [
                'slug' => 'inquiry',
                'module' => null,
                'id' => null,
                'type' => null,
                'locked' => true
            ],
            [
                'slug' => 'event',
                'module' => null,
                'id' => null,
                'type' => null,
                'locked' => true
            ],
        ];

        foreach ($indexing as $value) {
            IndexingUrl::create([
                'slug' => $value['slug'],
                'module' => $value['module'],
                'urlable_id' => $value['id'],
                'urlable_type' => $value['type'],
                'locked' => $value['locked'],
            ]);
        }
    }
}
