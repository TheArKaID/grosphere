<?php

namespace Tests\Feature\Http\Controllers\Api\Admin;

use App\Models\LiveClass;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LiveClassControllerTest extends TestCase
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

        $response = $this->post(route('admin.tutors.store'), $tutor);

        unset($tutor['password']);
        unset($tutor['password_confirmation']);

        $response->assertJson([
            'status' => 200,
            'message' => 'Tutor Created Successfully',
            'data' => $tutor
        ]);
    }
    /**
     * Add Live Class Test
     * 
     * @return void
     */
    public function testAddLiveClass()
    {
        auth()->login((User::find(1)));

        $tutor = Tutor::first();

        $liveClass = [
            'tutor_id' => $tutor->id,
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'start_time' => $this->faker->dateTimeBetween('-1 years', '+1 years')->format('Y-m-d H:i:s'),
            'duration' => $this->faker->numberBetween(1, 10),
            // 'thumbnail' => $this->faker->image(storage_path('app/public/live_classes'), 400, 400, 'cats', false)
        ];

        $response = $this->post(route('admin.live-classes.store'), $liveClass);

        unset($liveClass['thumbnail']);

        $response->assertJson([
            'status' => 200,
            'message' => 'Live Class Created Successfully'
        ]);
    }

    /**
     * Update Live Class Test
     * 
     * @return void
     */
    public function testUpdateLiveClass()
    {
        auth()->login((User::find(1)));
        $liveClass = LiveClass::first();
        $liveClass->name = $this->faker->name;
        $liveClass->description = $this->faker->text;
        $liveClass->start_time = $this->faker->dateTimeBetween('-1 years', '+1 years')->format('Y-m-d H:i:s');
        $liveClass->duration = $this->faker->numberBetween(1, 10);
        // $liveClass->thumbnail = $this->faker->image(storage_path('app/public/live_classes'), 400, 400, 'cats', false);

        $response = $this->put(route('admin.live-classes.update', $liveClass->id), $liveClass->toArray());

        unset($liveClass['thumbnail']);

        $response->assertJson([
            'status' => 200,
            'message' => 'Live Class Updated Successfully'
        ]);
    }

    /**
     * Get All Live Classes
     * 
     * @return void
     */
    public function testGetAllLiveClasses()
    {
        auth()->login((User::find(1)));
        $response = $this->get(route('admin.live-classes.index'));

        $response->assertStatus(200);
        
        $response->assertJson([
            'status' => 200,
            'message' => 'Success'
        ]);
    }

    /**
     * Get Live Class
     * 
     * @return void
     */
    public function testGetLiveClass()
    {
        auth()->login((User::find(1)));
        $liveClass = LiveClass::first();
        $response = $this->get(route('admin.live-classes.show', $liveClass->id));

        $response->assertStatus(200);
        
        $response->assertJson([
            'status' => 200,
            'message' => 'Success'
        ]);
    }

    /**
     * Delete Live Class Test
     * 
     * @return void
     */
    public function testDeleteLiveClass()
    {
        auth()->login((User::find(1)));
        $liveClass = LiveClass::first();

        $response = $this->delete(route('admin.live-classes.destroy', $liveClass->id));

        $response->assertJson([
            'status' => 200,
            'message' => 'Live Class Deleted Successfully'
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

        $response = $this->delete(route('admin.tutors.destroy', $tutor->id));

        $response->assertJson([
            'status' => 200,
            'message' => 'Tutor Deleted Successfully'
        ]);
    }
}
