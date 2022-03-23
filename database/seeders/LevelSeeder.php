<?php

namespace Database\Seeders;

use App\Models\Level;
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
        Level::create([
            'name' => 'Beginner',
            'level' => 1,
        ]);
        Level::create([
            'name' => 'Intermediate',
            'level' => 2,
        ]);
        Level::create([
            'name' => 'Advanced',
            'level' => 3,
        ]);
        Level::create([
            'name' => 'Expert',
            'level' => 4,
        ]);
    }
}
