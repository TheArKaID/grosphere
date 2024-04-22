<?php

namespace Database\Seeders;

use App\Models\SuperAdmin;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $super = User::create([
            'name' => 'Super Admin',
            'email' => 'super-admin@gmail.com',
            'password' => bcrypt('12345678')
        ]);

        $super->assignRole('super-admin');

        SuperAdmin::create([
            'user_id' => $super->id,
        ]);
    }
}
