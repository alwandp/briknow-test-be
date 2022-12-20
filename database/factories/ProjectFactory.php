<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Project;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Project::class, function (Faker $faker) {
    $nama       =   $faker->word();
    $date       =   Carbon::now();
    $flag_mcs   = $faker->randomElement($array = array ('0','1','2','3','4','5'));

    if ($flag_mcs == 0 or $flag_mcs == 1) {
        $c  =   null;
        $s  =   null;
        $r  =   null;
        $p  =   null;
    }elseif($flag_mcs == 2){
        $c  =   $date;
        $s  =   null;
        $r  =   null;
        $p  =   null;
    }elseif($flag_mcs == 3){
        $c  =   $date;
        $s  =   $date;
        $r  =   null;
        $p  =   null;
    }elseif($flag_mcs == 4){
        $c  =   $date;
        $s  =   $date;
        $r  =   $date;
        $p  =   null;
    }elseif($flag_mcs == 5){
        $c  =   $date;
        $s  =   $date;
        $r  =   $date;
        $p  =   $date;
    }
    return [
        'divisi_id'                 =>  $faker->randomElement($array = array ('1','2','3')),
        'project_managers_id'       =>  $faker->randomElement($array = array ('1','2','3')),
        'nama'                      =>  $nama,
        'slug'                      =>  $faker->randomNumber($nbDigits = 8)."-".\Str::slug($nama),
        'thumbnail'                 =>  'thumbnail_project.jpg',
        'metodologi'                =>  '<html><p>Hello world.</p></html>',
        'deskripsi'                 =>  $faker->sentence(3),
        'tanggal_mulai'             =>  $date,
        'tanggal_selesai'           =>  $date->addMonths(1),
        'status_finish'             =>  $faker->randomElement($array = array ('1','0')),
        'is_recomended'             =>  $faker->randomElement($array = array ('1','0')),
        'is_restricted'             =>  $faker->randomElement($array = array ('1','0')),
        'user_maker'                =>  $faker->randomElement($array = array ('11111111','22222222','33333333')),
        'user_checker'              =>  $faker->randomElement($array = array ('1','2','4')),
        'user_signer'               =>  $faker->randomElement($array = array ('1','2','4')),
        'checker_at'                =>  $c,
        'signer_at'                 =>  $s,
        'review_at'                 =>  $r,
        'publish_at'                =>  $p,
    ];
});
