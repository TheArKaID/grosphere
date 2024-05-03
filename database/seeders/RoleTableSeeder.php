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
            'name' => 'teacher',
            'readable_name' => 'Teacher',
            'guard_name' => 'api'
        ]);
        
        Role::create([
            'name' => 'student',
            'readable_name' =>  'Student',
            'guard_name' => 'api'
        ]);
        
        Role::create([
            'name' => 'guardian',
            'readable_name' => 'Guardian',
            'guard_name' => 'api'
        ]);
    }
}
