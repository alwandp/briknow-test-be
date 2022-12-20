<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Document;
use Faker\Generator as Faker;

$factory->define(Document::class, function (Faker $faker) {
    return [
        'project_id'        =>  $faker->numberBetween(1,3),
        'nama'              =>  $faker->name(),
        'jenis_file'        =>  'pdf',
        'url_file'          =>  'https://www.google.co.id/?hl=id',
        'size'              =>  '31400'
    ];  
});
