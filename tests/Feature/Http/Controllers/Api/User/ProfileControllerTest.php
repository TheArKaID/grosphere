<?php

namespace Tests\Feature\Http\Controllers\Api\User;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use WithFaker;

    private static $student, $isSetUpRun = false;

    /**
     * Set Up
     * 
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        if (!self::$isSetUpRun) {
            self::$student = Student::first();

            self::$isSetUpRun = true;
        }
    }

    /**
     * Test User Get Profile
     * 
     * @return void
     */
    public function testUserGetProfile()
    {
        auth()->login(self::$student->user);
        $response = $this->get(route('user.profile'));

        $response->assertJson([
            'status' => 200,
            'message' => 'Success',
            'data' => []
        ]);
    }

    /**
     * Test User Update Profile
     * 
     * @return void
     */
    public function testUserUpdateProfile()
    {
        auth()->login(self::$student->user);
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'address' => $this->faker->address,
            'birth_date' => $this->faker->date('d-m-Y'),
            'birth_place' => $this->faker->city,
            'gender' => 1
        ];
        $response = $this->put(route('user.profile.update'), $data);

        $response->assertJson([
            'status' => 200,
            'message' => 'Profile Updated Successfully'
        ]);
    }

    /**
     * Test User Update Password
     * 
     * @return void
     */
    public function testUserUpdatePassword()
    {
        auth()->login(self::$student->user);

        $data = [
            'old_password' => '@Student1234',
            'new_password' => '@Meet12345',
            'new_password_confirmation' => '@Meet12345'
        ];
        $response = $this->put(route('user.profile.update.password'), $data);

        $response->assertJson([
            'status' => 200,
            'message' => 'Password Updated Successfully'
        ]);
    }
}
