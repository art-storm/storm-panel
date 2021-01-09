<?php

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultMenu = array(
            array(
                'name' => 'admin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),
            array(
                'name' => 'site',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ),

        );

        Menu::insert($defaultMenu);
    }
}
