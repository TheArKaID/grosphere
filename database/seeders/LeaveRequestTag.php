<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeaveRequestTag extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['name' => 'Sick'],
            ['name' => 'Vacation', 'type' => 'multiple'],
            ['name' => 'Personal', 'type' => 'multiple'],
            ['name' => 'Family', 'type' => 'multiple'],
            ['name' => 'Other', 'type' => 'multiple'],
        ];

        foreach ($tags as $tag) {
            \App\Models\LeaveRequestTag::create($tag);
        }
    }
}
