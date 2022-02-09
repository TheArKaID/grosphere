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

    private static $liveClassId, $tokenJoin, $roomJoin;

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
            'start_time' => $this->faker->dateTimeBetween('-10 minutes')->format('d-m-Y H:i:s'),
            'duration' => $this->faker->numberBetween(30, 60),
            // 'thumbnail' => $this->faker->image(storage_path('app/public/live_classes'), 400, 400, 'cats', false)
        ];

        $response = $this->post(route('tutor.live-classes.store'), $liveClass);

        self::$liveClassId = $response->json('data.id');

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
            "start_time" => $this->faker->dateTimeBetween('-10 minutes')->format('d-m-Y H:i:s'),
            "duration" => $this->faker->numberBetween(30, 60),
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
     * Test Tutor Can Join Live Class
     * 
     * @return void
     */
    public function testTutorCanJoinLiveClass()
    {
        auth()->login(Tutor::first()->user);
        $response = $this->post(route('tutor.live-classes.join', self::$liveClassId));

        $data = json_decode($response->getContent());

        self::$tokenJoin = $data->data->token;
        self::$roomJoin = $data->data->room;

        $response->assertJson([
            'status' => 200,
            'message' => 'Tutor joined Live Class',
            'data' => []
        ]);
    }

    // /**
    //  * Test Tutor Can Leave Live Class
    //  * 
    //  * @return void
    //  */
    // public function testTutorCanLeaveLiveClass()
    // {
    //     auth()->login(Tutor::first()->user);
    //     $response = $this->post(route('tutor.live-classes.leave', self::$liveClassId));

    //     $response->assertJson([
    //         'status' => 200,
    //         'message' => 'Tutor left Live Class'
    //     ]);
    // }

    /**
     * test Tutor validate Join Live Class
     * 
     * @return void
     */
    public function testValidateJoinLiveClass()
    {
        auth()->login(Tutor::first()->user);

        $response = $this->post(route('user.live-classes.validate', self::$liveClassId), [
            'token' => self::$tokenJoin,
            'room' => self::$roomJoin
        ]);

        $response->assertJson([
            'status' => 200,
            'message' => 'Live Class validated',
            'data' => []
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
