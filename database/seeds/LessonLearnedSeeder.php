<?php

use Illuminate\Database\Seeder;

class LessonLearnedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Lesson_learned::class, 10)->create();
    }
}
