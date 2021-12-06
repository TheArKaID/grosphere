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
            'name' => 'super-admin',
            'readable_name' => 'Super Admin',
            'guard_name' => 'web'
        ]);

        Role::create([
            'name' => 'admin',
            'readable_name' => 'Admin Lembaga',
            'guard_name' => 'web'
        ]);
        
        Role::create([
            'name' => 'teacher',
            'readable_name' => 'Guru',
            'guard_name' => 'web'
        ]);
        
        Role::create([
            'name' => 'student',
            'readable_name' =>  'Siswa',
            'guard_name' => 'web'
        ]);
        
        Role::create([
            'name' => 'parent',
            'readable_name' => 'Orang Tua',
            'guard_name' => 'web'
        ]);
    }
}
