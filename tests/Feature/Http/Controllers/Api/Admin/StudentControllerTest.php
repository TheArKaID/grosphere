<?php

namespace Tests\Feature\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StudentControllerTest extends TestCase
{
    use withFaker;

    /**
     * Add Student Test
     * 
     * @return void
     */
    public function testAddStudent()
    {
        auth()->login((User::find(1)));
        $password = $this->faker->password(8);
        $student = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber(),
            'gender' => rand(0, 1),
            'address' => $this->faker->address,
            'id_number' => (string)$this->faker->unique()->numberBetween(100000000, 999999999),
            'birth_date' => $this->faker->date(),
            'birth_place' => $this->faker->city,
            'password' => $password,
            'password_confirmation' => $password
        ];
        $response = $this->post(route('students.store'), $student);

        unset($student['password']);
        unset($student['password_confirmation']);
        unset($student['id_number']);
        unset($student['birth_date']);
        unset($student['birth_place']);

        $response->assertJson([
            'status' => 200,
            'message' => 'Student Created Successfully',
            'data' => $student
        ]);
    }

    /**
     * Get All Students Test
     * 
     * @return void
     */
    public function testGetAllStudents()
    {
        auth()->login((User::find(1)));
        $response = $this->get(route('students.index'));

        $response->assertJson([
            'status' => 200,
            'message' => 'Success',
            'data' => []
        ]);
    }

    /**
     * Get Student Test
     * 
     * @return void
     */
    public function testGetStudent()
    {
        auth()->login((User::find(1)));

        $response = $this->get(route('students.show', 1));

        $response->assertJson([
            'status' => 200,
            'message' => 'Success',
            'data' => []
        ]);
    }

    /**
     * Update Student Test
     * 
     * @return void
     */
    public function testUpdateStudent()
    {
        auth()->login((User::find(1)));
        $student = [
            'name' => $this->faker->name,
            'birth_place' => $this->faker->city,
            'birth_date' => $this->faker->date(),
            'address' => $this->faker->address,
            'gender' => rand(0, 1)
        ];
        $response = $this->put(route('students.update', 1), $student);

        unset($student['birth_date']);
        unset($student['birth_place']);

        $response->assertJson([
            'status' => 200,
            'message' => 'Student Updated Successfully',
            'data' => $student
        ]);
    }

    /**
     * Change Student Password Test
     * 
     * @return void
     */
    public function testChangeStudentPassword()
    {
        auth()->login((User::find(1)));
        $password = $this->faker->password(8). "1aA!";
        $student = [
            'password' => $password,
            'password_confirmation' => $password
        ];
        $response = $this->put(route('students.change-password', 1), $student);

        $response->assertJson([
            'status' => 200,
            'message' => 'Student Password Changed Successfully'
        ]);
    }

    /**
     * Delete Student Test
     * 
     * @return void
     */
    public function testDeleteStudent()
    {
        auth()->login((User::find(1)));
        $response = $this->delete(route('students.destroy', 1));

        $response->assertJson([
            'status' => 200,
            'message' => 'Student Deleted Successfully'
        ]);
    }
}
