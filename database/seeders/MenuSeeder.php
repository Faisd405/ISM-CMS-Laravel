<?php

namespace Database\Seeders;

use App\Models\Menu\Menu;
use App\Models\Menu\MenuCategory;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // catgory
        $categories = [
            [
                'name' => 'header',
                'active' => true,
                'locked' => true
            ],
            [
                'name' => 'sidebar',
                'active' => false,
                'locked' => true
            ],
            [
                'name' => 'footer',
                'active' => true,
                'locked' => true
            ],
        ];

        foreach ($categories as $item) {
            MenuCategory::create([
                'name' => $item['name'],
                'active' => $item['active'],
                'locked' => $item['locked']
            ]);
        }

        // Menu
        $menus = [
            [
                'menu_category_id' => 1,
                'parent' => 0,
                'title' => [
                    'id' => 'Beranda',
                    'en' => 'Home'
                ],
                'module' => null,
                'menuable_id' => null,
                'menuable_type' => null,
                'publish' => 1,
                'public' => 1,
                'config' => [
                    'url' => '/',
                    'target_blank' => false,
                    'not_from_module' => true,
                    'icon' => null,
                    'edit_public_menu' => 1,
                ],
                'position' => 1,
            ],
        ];

        foreach ($menus as $item) {
            Menu::create([
                'menu_category_id' => $item['menu_category_id'],
                'parent' => $item['parent'],
                'title' => $item['title'],
                'module' => $item['module'],
                'menuable_id' => $item['menuable_id'],
                'menuable_type' => $item['menuable_type'],
                'publish' => $item['publish'],
                'public' => $item['public'],
                'config' => $item['config'],
                'position' => $item['position']
            ]);
        }
    }
}
