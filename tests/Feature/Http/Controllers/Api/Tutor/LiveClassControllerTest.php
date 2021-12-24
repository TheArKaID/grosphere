<?php

namespace Tests\Feature\Http\Controllers\Api\Tutor;

use App\Models\Tutor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LiveClassControllerTest extends TestCase
{
    use WithFaker;

    private static $liveClassId;

    /**
     * Test create live class as tutor
     * 
     * @return void
     */
    public function testCreateLiveClassAsTutor()
    {
        auth()->login((Tutor::first()->user));

        $tutor = Tutor::first();

        $liveClass = [
            'tutor_id' => $tutor->id,
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'start_time' => $this->faker->dateTimeBetween('-1 years', '+1 years')->format('Y-m-d H:i:s'),
            'duration' => $this->faker->numberBetween(1, 10),
            // 'thumbnail' => $this->faker->image(storage_path('app/public/live_classes'), 400, 400, 'cats', false)
        ];

        $response = $this->post(route('tutor.live-classes.store'), $liveClass);

        self::$liveClassId = $response->original['data']->id;

        $response->assertJson([
            'status' => 200,
            'message' => 'Live Class Created Successfully'
        ]);
    }

    /**
     * Test get live classes as Tutor
     * 
     * @return void
     */
    public function testGetLiveClassesAsTutor()
    {
        auth()->login((Tutor::first()->user));

        $response = $this->get(route('tutor.live-classes.show', self::$liveClassId));

        $response->assertJson([
            'status' => 200,
            'message' => 'Success'
        ]);
    }

    /**
     * Test get all live classes As Tutor
     * 
     * @return void
     */
    public function testGetAllLiveClassesAsTutor()
    {
        auth()->login(Tutor::first()->user);

        $response = $this->get(route('tutor.live-classes.index'));

        $response->assertStatus(200);
    }

    /**
     * Test update live class as tutor
     * 
     * @return void
     */
    public function testUpdateLiveClassAsTutor()
    {
        auth()->login((Tutor::first()->user));

        $liveClass = [
            "name" => $this->faker->name,
            "description" => $this->faker->text,
            "start_time" => $this->faker->dateTimeBetween('-1 years', '+1 years')->format('Y-m-d H:i:s'),
            "duration" => $this->faker->numberBetween(1, 10),
            // "thumbnail" => $this->faker->image(storage_path('app/public/live_classes'), 400, 400, 'cats', false)
        ];

        $response = $this->put(route('tutor.live-classes.update', self::$liveClassId), $liveClass);

        unset($liveClass['thumbnail']);

        $response->assertJson([
            'status' => 200,
            'message' => 'Live Class Updated Successfully'
        ]);
    }

    /**
     * Test delete live class as tutor
     * 
     * @return void
     */
    public function testDeleteLiveClassAsTutor()
    {
        auth()->login((Tutor::first()->user));

        $response = $this->delete(route('tutor.live-classes.destroy', self::$liveClassId));

        $response->assertJson([
            'status' => 200,
            'message' => 'Live Class Deleted Successfully'
        ]);
    }
}
