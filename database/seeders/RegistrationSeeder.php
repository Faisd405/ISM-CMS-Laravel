<?php

namespace Database\Seeders;

use App\Models\Feature\Registration;
use Illuminate\Database\Seeder;

class RegistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $registrations = [
            [
                'name' => 'Default',
                'type' => 0,
                'roles' => null,
                'start_date' => null,
                'end_date' => null,
                'active' => config('cms.module.auth.register.active') == true ? 1 : 0,
                'locked' => true
            ],
        ];

        foreach ($registrations as $itm) {
            Registration::create([
                'name' => $itm['name'],
                'type' => $itm['type'],
                'roles' => $itm['roles'],
                'start_date' => $itm['start_date'],
                'end_date' => $itm['end_date'],
                'active' => $itm['active'],
                'locked' => $itm['locked']
            ]);
        }
    }
}
