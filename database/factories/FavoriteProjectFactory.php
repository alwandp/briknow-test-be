<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Favorite_project;
use Faker\Generator as Faker;

$factory->define(Favorite_project::class, function (Faker $faker) {
    return [
        'user_id'           =>  $faker->numberBetween(1,10),
        'project_id'        =>  $faker->numberBetween(1,3),
    ];
});
