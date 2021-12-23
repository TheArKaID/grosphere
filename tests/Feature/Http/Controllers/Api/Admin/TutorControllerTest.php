<?php

namespace Tests\Feature\Http\Controllers\Api\Admin;

use App\Models\Tutor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
        auth()->login((User::find(1)));
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

        $response = $this->get(route('tutors.index'));

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

        $tutor = Tutor::first();
        $response = $this->get(route('tutors.show', $tutor->id));

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

        $tutor = Tutor::first();

        $response = $this->put(route('tutors.update', $tutor->id), [
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
        $tutor = Tutor::first();
        $response = $this->put(route('tutors.change-password', $tutor->id), $parent);

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

        $tutor = Tutor::first();
        $response = $this->delete(route('tutors.destroy', $tutor->id));

        $response->assertJson([
            'status' => 200,
            'message' => 'Tutor Deleted Successfully'
        ]);
    }
}
