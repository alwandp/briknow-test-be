<?php

use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);
        $this->call(LevelSeeder::class);
        $this->call(AvatarSeeder::class);
        $this->call(ActivitySeeder::class);
        $this->call(AchievementSeeder::class);
        $this->call(DivisiSeeder::class);
        $this->call(ConsultantSeeder::class);
        // factory(\App\Document::class,3)->create();
        // factory(\App\Favorite_project::class,3)->create();
        // factory(\App\Favorite_consultant::class,3)->create();
        // factory(\App\Keywords::class,3)->create();
        // factory(\App\Search_log::class,3)->create();
        // factory(\App\Project_managers::class,3)->create();
        // factory(\App\Keywords_document::class,3)->create();
        // factory(\App\Consultant::class,3)->create();
        // factory(\App\Divisi::class,3)->create();
        // factory(\App\Project::class,20)->create();
    }
}