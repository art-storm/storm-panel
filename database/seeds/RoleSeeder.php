<?php

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultRole = array(
            array(
                'role_name' => 'admin_super',
                'role_display' => 'Super administrator',
                'created_ip' => ip2long('127.0.0.1'),
                'updated_ip' => ip2long('127.0.0.1'),
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'role_name' => 'user_registered',
                'role_display' => 'Registered user',
                'created_ip' => ip2long('127.0.0.1'),
                'updated_ip' => ip2long('127.0.0.1'),
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),

        );

        Role::insert($defaultRole);
    }
}
