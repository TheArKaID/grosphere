<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'admin',
            'readable_name' => 'Admin',
            'guard_name' => 'api'
        ]);
        
        Role::create([
            'name' => 'tutor',
            'readable_name' => 'Tutor',
            'guard_name' => 'api'
        ]);
        
        Role::create([
            'name' => 'student',
            'readable_name' =>  'Student',
            'guard_name' => 'api'
        ]);
        
        Role::create([
            'name' => 'parent',
            'readable_name' => 'Parent',
            'guard_name' => 'api'
        ]);
    }
}
