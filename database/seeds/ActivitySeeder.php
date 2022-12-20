<?php

use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Activity::create([
            'name'  => 'Membuat halaman proyek',
            'xp'    => 15,
        ]);
        
        \App\Activity::create([
            'name'  => 'Proyek telah terpublish',
            'xp'    => 10,
        ]);
        \App\Activity::create([
            'name'  => 'Membuat postingan forum',
            'xp'    => 5,
        ]);
        \App\Activity::create([
            'name'  => 'Memberikan komentar di sebuah proyek',
            'xp'    => 3,
        ]);
        \App\Activity::create([
            'name'  => 'Memberikan komentar di sebuah postingan forum',
            'xp'    => 3,
        ]);
        \App\Activity::create([
            'name'  => 'Login Harian',
            'xp'    => 2,
        ]);
        \App\Activity::create([
            'name'  => 'Mendapatkan Sebuah Achievement',
            'xp'    => 10,
        ]);
        \App\Activity::create([
            'name'  => 'Mengunjungi Proyek',
            'xp'    => 2,
        ]);
        \App\Activity::create([
            'name'  => 'Mengunjungi Forum',
            'xp'    => 2,
        ]);
    }
}
