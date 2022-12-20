<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class MyProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Project::class, 10)->create();
    }
}
