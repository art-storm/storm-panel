<?php

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $appPermissions = array(
            // Upper level
            array(
                'key' => 'admin_admin',
                'name' => 'Admin panel',
                'parent_id' => 0,
                'order_id' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'key' => 'admin_users',
                'name' => 'Users',
                'parent_id' => 0,
                'order_id' => 20,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'key' => 'admin_roles',
                'name' => 'Roles',
                'parent_id' => 0,
                'order_id' => 30,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'key' => 'admin_menus',
                'name' => 'Menus',
                'parent_id' => 0,
                'order_id' => 40,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            // Nested level
            array(
                'key' => 'admin_login',
                'name' => 'Login to admin panel',
                'parent_id' => 1,
                'order_id' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'key' => 'admin_users-view',
                'name' => 'View users',
                'parent_id' => 2,
                'order_id' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'key' => 'admin_users-create',
                'name' => 'Add users',
                'parent_id' => 2,
                'order_id' => 20,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'key' => 'admin_users-update',
                'name' => 'Edit users',
                'parent_id' => 2,
                'order_id' => 30,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'key' => 'admin_users-delete',
                'name' => 'Delete users',
                'parent_id' => 2,
                'order_id' => 40,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'key' => 'admin_roles-view',
                'name' => 'View roles',
                'parent_id' => 3,
                'order_id' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'key' => 'admin_roles-create',
                'name' => 'Add roles',
                'parent_id' => 3,
                'order_id' => 20,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'key' => 'admin_roles-update',
                'name' => 'Edit roles',
                'parent_id' => 3,
                'order_id' => 30,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'key' => 'admin_roles-delete',
                'name' => 'Delete roles',
                'parent_id' => 3,
                'order_id' => 40,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'key' => 'admin_menus-view',
                'name' => 'View menus',
                'parent_id' => 4,
                'order_id' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'key' => 'admin_menus-update',
                'name' => 'Edit menus',
                'parent_id' => 4,
                'order_id' => 20,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
        );

        Permission::insert($appPermissions);
    }
}
