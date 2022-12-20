<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Keywords_document;
use Faker\Generator as Faker;

$factory->define(Keywords_document::class, function (Faker $faker) {
    return [
        'document_id'   =>  $faker->numberBetween(1,3),
        'name'          =>  $faker->name()
    ];
});
