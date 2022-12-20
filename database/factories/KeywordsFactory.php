<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Keywords;
use Faker\Generator as Faker;

$factory->define(Keywords::class, function (Faker $faker) {
    return [
        'project_id'    =>  $faker->numberBetween(1,3),
        'nama'          =>  $faker->name(),
    ];
});
