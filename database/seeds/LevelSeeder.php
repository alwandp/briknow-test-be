<?php

use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Level::create([
            'name' => 'Junior Grade',
            'xp'   => 0,
            'badge'=> 'assets\img\level_badges\1.png',
        ]);

        \App\Level::create([
            'name' => 'Master Grade',
            'xp'   => 100,
            'badge'=> 'assets\img\level_badges\2.png',
        ]);

        \App\Level::create([
            'name' => 'Grandmaster Grade',
            'xp'   => 250,
            'badge'=> 'assets\img\level_badges\3.png',
        ]);

        \App\Level::create([
            'name' => 'Legendary Grade',
            'xp'   => 1000,
            'badge'=> 'assets\img\level_badges\4.png',
        ]);
    }
}
