<?php

namespace Tests\Feature\Http\Controllers\Api\Admin;

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
            'start_time' => $this->faker->dateTimeBetween('-10 minutes')->format('Y-m-d H:i:s'),
            'duration' => $this->faker->numberBetween(30, 60),
            // 'thumbnail' => $this->faker->image(storage_path('app/public/live_classes'), 400, 400, 'cats', false)
        ];

        $response = $this->post(route('admin.live-classes.store'), $liveClass);

        self::$liveClassId = $response->original['data']->id;

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
        $liveClass = [
            "name" => $this->faker->name,
            "description" => $this->faker->text,
            "start_time" => $this->faker->dateTimeBetween('-10 minutes')->format('Y-m-d H:i:s'),
            "duration" => $this->faker->numberBetween(30, 60),
            // "thumbnail" => $this->faker->image(storage_path('app/public/live_classes'), 400, 400, 'cats', false)
        ];

        $response = $this->put(route('admin.live-classes.update', self::$liveClassId), $liveClass);

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

        $response = $this->get(route('admin.live-classes.show', self::$liveClassId));

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

        $response = $this->delete(route('admin.live-classes.destroy', self::$liveClassId));

        $response->assertJson([
            'status' => 200,
            'message' => 'Live Class Deleted Successfully'
        ]);
    }
}
