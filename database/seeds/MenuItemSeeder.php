<?php

use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultMenuItem = array(
            array(
                'menu_id' => 1,
                'parent_id' => 0,
                'title' => 'Dashboard',
                'url' => '/admin',
                'target' => '_self',
                'icon_class' => 'fas fa-tachometer-alt',
                'order' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'menu_id' => 1,
                'parent_id' => 0,
                'title' => 'Menus',
                'url' => '/admin/menus',
                'target' => '_self',
                'icon_class' => 'fas fa-bars',
                'order' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'menu_id' => 1,
                'parent_id' => 0,
                'title' => 'Roles',
                'url' => '/admin/roles',
                'target' => '_self',
                'icon_class' => 'fas fa-user-lock',
                'order' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'menu_id' => 1,
                'parent_id' => 0,
                'title' => 'Users',
                'url' => '/admin/users',
                'target' => '_self',
                'icon_class' => 'fas fa-user',
                'order' => 5,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'menu_id' => 1,
                'parent_id' => 0,
                'title' => 'Settings',
                'url' => '/admin/settings',
                'target' => '_self',
                'icon_class' => 'fas fa-cog',
                'order' => 6,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
        );

        MenuItem::insert($defaultMenuItem);
    }
}
