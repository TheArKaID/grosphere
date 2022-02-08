<?php

namespace Tests\Feature\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TutorControllerTest extends TestCase
{
    use WithFaker;

    private static $tutorId;

    /**
     * Add Tutor Test
     * 
     * @return void
     */
    public function testAddTutor()
    {
        auth()->login((User::find(1)));
        $password = '@Tutor1234';
        $tutor = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber(),
            'password' => $password,
            'password_confirmation' => $password
        ];

        $response = $this->post(route('admin.tutors.store'), $tutor);

        self::$tutorId = $response->json('data.id');

        unset($tutor['password']);
        unset($tutor['password_confirmation']);

        $response->assertJson([
            'status' => 200,
            'message' => 'Tutor Created Successfully',
            'data' => $tutor
        ]);
    }

    /**
     * Get All Tutors Test
     * 
     * @return void
     */
    public function testGetAllTutors()
    {
        auth()->login((User::find(1)));

        $response = $this->get(route('admin.tutors.index'));

        $response->assertJson([
            'status' => 200,
            'message' => 'Success',
            'data' => []
        ]);
    }

    /**
     * Get Tutor Test
     * 
     * @return void
     */
    public function testGetTutor()
    {
        auth()->login((User::find(1)));

        $response = $this->get(route('admin.tutors.show', self::$tutorId));

        $response->assertJson([
            'status' => 200,
            'message' => 'Success',
            'data' => []
        ]);
    }

    /**
     * Update Tutor Test
     * 
     * @return void
     */
    public function testUpdateTutor()
    {
        auth()->login((User::find(1)));

        $response = $this->put(route('admin.tutors.update', self::$tutorId), [
            'name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber()
        ]);

        $response->assertJson([
            'status' => 200,
            'message' => 'Tutor Updated Successfully',
            'data' => []
        ]);
    }

    /**
     * Change Tutor Password Test
     * 
     * @return void
     */
    public function testChangeTutorPassword()
    {
        auth()->login((User::find(1)));
        $password = $this->faker->password(8) . "1aA!";
        $parent = [
            'password' => $password,
            'password_confirmation' => $password
        ];

        $response = $this->put(route('admin.tutors.change-password', self::$tutorId), $parent);

        $response->assertJson([
            'status' => 200,
            'message' => 'Tutor Password Changed Successfully'
        ]);
    }

    /**
     * Delete Tutor Test
     * 
     * @return void
     */
    public function testDeleteTutor()
    {
        auth()->login((User::find(1)));

        $response = $this->delete(route('admin.tutors.destroy', self::$tutorId));

        $response->assertJson([
            'status' => 200,
            'message' => 'Tutor Deleted Successfully'
        ]);
    }
}
