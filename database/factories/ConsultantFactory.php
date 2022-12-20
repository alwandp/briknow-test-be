<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Consultant;
use Faker\Generator as Faker;

$factory->define(Consultant::class, function (Faker $faker) {
    return [
        'nama'      =>  $faker->name(),
        'tentang'   =>  $faker->sentence(5),
        'bidang'   =>  $faker->sentence(1),
        'website'   =>  $faker->domainName(),
        'telepon'   =>  $faker->phoneNumber,
        'email'     =>  $faker->safeEmail,
        'facebook'  =>  $faker->word,
        'instagram' =>  $faker->word,
        'lokasi'    =>  $faker->address,
    ];
});
