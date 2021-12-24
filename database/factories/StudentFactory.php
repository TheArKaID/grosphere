<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => 0,
            'parent_id' => null,
            'id_number' => $this->faker->unique()->randomNumber(6),
            'birth_date' => $this->faker->date('Y-m-d'),
            'birth_place' => $this->faker->city,
            'address' => $this->faker->address,
            'gender' => 1
        ];
    }
}
