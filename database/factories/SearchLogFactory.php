<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Search_log;
use Faker\Generator as Faker;

$factory->define(Search_log::class, function (Faker $faker) {
    return [
        'user_id'       =>  $faker->numberBetween(1,3),
        'project_id'    =>  $faker->numberBetween(1,3),
    ];
});
