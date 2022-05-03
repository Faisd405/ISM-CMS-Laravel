<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $seed['user'] = UserSeeder::class;
        $seed['config'] = ConfigurationSeeder::class;
        $seed['language'] = LanguageSeeder::class;
        $seed['master'] = MasterSeeder::class;
        $seed['url'] = UrlSeeder::class;
        $seed['registration'] = RegistrationSeeder::class;
        $seed['menu'] = MenuSeeder::class;
        $seed['content'] = ContentSeeder::class;

        if (config('cms.module.regional.active') == true) {
            // $seed['regional'] = RegionalSeeder::class;
        }

        $this->call($seed);
    }
}
