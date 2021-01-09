<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultUser = array(
            array(
                'name' => 'admin',
                'email' => 'admin@admin.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password'),
                'is_activate' => 1,
                'role_id' => 1,
                'created_ip' => ip2long('127.0.0.1'),
                'updated_ip' => ip2long('127.0.0.1'),
                'updated_by' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
        );

        User::insert($defaultUser);
    }
}
