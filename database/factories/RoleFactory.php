<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Role;
use Faker\Generator as Faker;

$factory->define(Role::class, function (Faker $faker) {
    return [
        'role_name' => $faker->unique()->word,
        'role_display' => $faker->word,
        'created_ip' => 2130706433,
        'updated_ip' => 2130706433,
        'created_by' => 1,
        'updated_by' => 1,
    ];
});
