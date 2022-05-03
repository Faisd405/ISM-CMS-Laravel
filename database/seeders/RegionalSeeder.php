<?php

namespace Database\Seeders;

use App\Models\Regional\City;
use App\Models\Regional\District;
use App\Models\Regional\Province;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createProvince();
        $this->createRegency();
        $this->createDistrict();
    }

    public function createProvince()
    {
        $csvFile = fopen(base_path("database/seeders/CSV/provinces.csv"), "r");
        $firstline = true;

        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {

            if (!$firstline) {
                Province::create([
                    'code' => $data['0'],
                    'name' => $data['1'],
                    'latitude' => $data['2'],
                    'longitude' => $data['3'],
                    'locked' => true
                ]);
            }

            $firstline = false;
        }

        fclose($csvFile);
    }

    public function createRegency()
    {
        $csvFile = fopen(base_path("database/seeders/CSV/regencies.csv"), "r");
        $firstline = true;

        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {

            if (!$firstline) {
                City::create([
                    'code' => $data['0'],
                    'province_code' => $data['1'],
                    'name' => $data['2'],
                    'latitude' => $data['3'],
                    'longitude' => $data['4'],
                    'locked' => true
                ]);
            }

            $firstline = false;
        }

        fclose($csvFile);
    }

    public function createDistrict()
    {
        $csvFile = fopen(base_path("database/seeders/CSV/districts.csv"), "r");
        $firstline = true;

        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {

            if (!$firstline) {
                District::create([
                    'code' => $data['0'],
                    'province_code' => City::firstWhere('code', $data['1'])['province_code'],
                    'city_code' => $data['1'],
                    'name' => $data['2'],
                    'latitude' => $data['3'],
                    'longitude' => $data['4'],
                    'locked' => true
                ]);
            }

            $firstline = false;
        }
        
        fclose($csvFile);
    }
}
