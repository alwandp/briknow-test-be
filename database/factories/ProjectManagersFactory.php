<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Project_managers;
use Faker\Generator as Faker;

$factory->define(Project_managers::class, function (Faker $faker) {
    return [
        'nama'  =>  $faker->name(),
        'email' =>  $faker->safeEmail,
    ];
});
