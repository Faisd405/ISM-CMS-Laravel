<?php

namespace Database\Seeders;

use App\Models\Feature\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = [
            [
                'iso_codes' => 'id',
                'name' => 'Indonesia',
                'time_zone' => 'Asia/Jakarta',
                'gmt' => '+8',
                'code' => 62,
                'active' => 1,
                'locked' => true,
            ],
            [
                'iso_codes' => 'en',
                'name' => 'English',
                'time_zone' => 'UTC',
                'gmt' => null,
                'code' => null,
                'active' => 1,
                'locked' => true,
            ],
        ];

        foreach ($languages as $item) {
            Language::create([
                'iso_codes' => $item['iso_codes'],
                'name' => $item['name'],
                'code' => $item['code'],
                'time_zone' => $item['time_zone'],
                'gmt' => $item['gmt'],
                'active' => $item['active'],
                'locked' => $item['locked'],
            ]);
        }
    }
}
