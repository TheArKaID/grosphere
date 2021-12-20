<?php

namespace Tests\Feature\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TutorControllerTest extends TestCase
{
    use WithFaker;

    /**
     * Add Tutor Test
     * 
     * @return void
     */
    public function testAddTutor()
    {
        Sanctum::actingAs(
            User::find(1),
            ['*']
        );
        $password = $this->faker->password(8);
        $tutor = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber(),
            'password' => $password,
            'password_confirmation' => $password
        ];

        $response = $this->post(route('tutors.store'), $tutor);

        unset($tutor['password']);
        unset($tutor['password_confirmation']);

        $response->assertJson([
            'status' => 200,
            'message' => 'Tutor Created Successfully',
            'response' => $tutor
        ]);
    }

    /**
     * Get All Tutors Test
     * 
     * @return void
     */
    public function testGetAllTutors()
    {
        Sanctum::actingAs(
            User::find(1),
            ['*']
        );

        $response = $this->get(route('tutors.index'));

        $response->assertJson([
            'status' => 200,
            'message' => 'Success',
            'response' => []
        ]);
    }

    /**
     * Get Tutor Test
     * 
     * @return void
     */
    public function testGetTutor()
    {
        Sanctum::actingAs(
            User::find(1),
            ['*']
        );

        $response = $this->get(route('tutors.show', 1));

        $response->assertJson([
            'status' => 200,
            'message' => 'Success',
            'response' => []
        ]);
    }

    /**
     * Update Tutor Test
     * 
     * @return void
     */
    public function testUpdateTutor()
    {
        Sanctum::actingAs(
            User::find(1),
            ['*']
        );

        $response = $this->put(route('tutors.update', 1), [
            'name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber()
        ]);

        $response->assertJson([
            'status' => 200,
            'message' => 'Tutor Updated Successfully',
            'response' => []
        ]);
    }

    /**
     * Change Tutor Password Test
     * 
     * @return void
     */
    public function testChangeTutorPassword()
    {
        Sanctum::actingAs(
            User::find(1),
            ['*']
        );
        $password = $this->faker->password(20);
        $parent = [
            'password' => $password,
            'password_confirmation' => $password
        ];
        $response = $this->put(route('tutors.change-password', 1), $parent);

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
        Sanctum::actingAs(
            User::find(1),
            ['*']
        );

        $response = $this->delete(route('tutors.destroy', 1));

        $response->assertJson([
            'status' => 200,
            'message' => 'Tutor Deleted Successfully'
        ]);
    }
}
