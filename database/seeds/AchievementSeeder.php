<?php

use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Achievement::create([
            'badge'         => 'assets\img\achievement_badges\003-unyielding-soldier.png',
            'name'          => 'Unyielding Hero',
            'activity_id'   => 1,
            'value'         => 5,
        ]);

        \App\Achievement::create([
            'badge'         => 'assets\img\achievement_badges\002-bravely-knight.png',
            'name'          => 'Bravely Knight',
            'activity_id'   => 1,
            'value'         => 15,
        ]);

        \App\Achievement::create([
            'badge'         => 'assets\img\achievement_badges\001-prime-hero.png',
            'name'          => 'Prime Hero',
            'activity_id'   => 1,
            'value'         => 30,
        ]);

        \App\Achievement::create([
            'badge'         => 'assets\img\achievement_badges\006-the-appraiser.png',
            'name'          => 'The Appraiser',
            'activity_id'   => 2,
            'value'         => 5,
        ]);

        \App\Achievement::create([
            'badge'         => 'assets\img\achievement_badges\005-the-investigator.png',
            'name'          => 'The Investigator',
            'activity_id'   => 2,
            'value'         => 15,
        ]);
        
        \App\Achievement::create([
            'badge'         => 'assets\img\achievement_badges\004-the-true-auditor.png',
            'name'          => 'The True Auditor',
            'activity_id'   => 2,
            'value'         => 30,
        ]);

        \App\Achievement::create([
            'badge'         => 'assets\img\achievement_badges\009-attention-seeker.png',
            'name'          => 'Attention Seeker',
            'activity_id'   => 8,
            'value'         => 100,
        ]);

        \App\Achievement::create([
            'badge'         => 'assets\img\achievement_badges\008-the-rising-star.png',
            'name'          => 'The Rising Star',
            'activity_id'   => 8,
            'value'         => 500,
        ]);

        \App\Achievement::create([
            'badge'         => 'assets\img\achievement_badges\007-glorious-artist.png',
            'name'          => 'Glorious Artist',
            'activity_id'   => 8,
            'value'         => 1000,
        ]);

        \App\Achievement::create([
            'badge'         => 'assets\img\achievement_badges\011-the-deligent.png',
            'name'          => 'The Diligent',
            'activity_id'   => 6,
            'value'         => 7,
        ]);

        \App\Achievement::create([
            'badge'         => 'assets\img\achievement_badges\010-the-dedicated.png',
            'name'          => 'The Dedicated',
            'activity_id'   => 6,
            'value'         => 30,
        ]);
    }
}
