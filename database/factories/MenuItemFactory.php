<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\MenuItem;
use Faker\Generator as Faker;

$factory->define(MenuItem::class, function (Faker $faker) {
    return [
        'menu_id' => 1,
        'parent_id' => 0,
        'title' => $faker->unique()->word,
        'url' => $faker->url,
        'target' => '_self',
        'icon_class' => 'icon_class_test',
        'color' => '#000000',
        'divider' => '1',
        'order' => 50,
    ];
});
