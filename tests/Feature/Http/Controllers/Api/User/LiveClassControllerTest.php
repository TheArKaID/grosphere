<?php

namespace Tests\Feature\Http\Controllers\Api\User;

use App\Models\Student;
use App\Models\Tutor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LiveClassControllerTest extends TestCase
{
    use WithFaker;

    private static $liveClassId, $tutorId, $student, $isSetUpRun = false, $tokenJoin, $roomJoin;

    /**
     * Set Up
     * 
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        if(!self::$isSetUpRun) {
            self::$student = Student::first();

            // Creating Live Class
            auth()->login(Tutor::first()->user);
            self::$tutorId = Tutor::first()->id;
            $liveClass = [
                'tutor_id' => self::$tutorId,
                'name' => $this->faker->name,
                'description' => $this->faker->text,
                'start_time' => $this->faker->dateTimeBetween('-10 minutes')->format('Y-m-d H:i:s'),
                'duration' => $this->faker->numberBetween(30, 60)
            ];
            $response = $this->post(route('tutor.live-classes.store'), $liveClass);
            self::$liveClassId = $response->original['data']->id;
            self::$isSetUpRun = true;
        }
    }

    /**
     * Test Get All Live Classes
     * 
     * @return void
     */
    public function testGetAllLiveClasses()
    {
        auth()->login(self::$student->user);
        $response = $this->get(route('user.live-classes.index'));

        $response->assertJson([
            'status' => 200,
            'message' => 'Success'
        ]);
    }

    /**
     * Test Get Live Classes
     * 
     * @return void
     */
    public function testGetLiveClasses()
    {
        auth()->login(self::$student->user);
        $response = $this->get(route('user.live-classes.index', self::$liveClassId));

        $response->assertJson([
            'status' => 200,
            'message' => 'Success'
        ]);
    }

    /**
     * Test User Can Join Live Class
     * 
     * @return void
     */
    public function testUserCanJoinLiveClass()
    {
        auth()->login(self::$student->user);
        $response = $this->post(route('user.live-classes.join', self::$liveClassId));

        $data = json_decode($response->getContent());
        self::$tokenJoin = $data->data->token;
        self::$roomJoin = $data->data->room;

        $response->assertJson([
            'status' => 200,
            'message' => 'User joined Live Class',
            'data' => []
        ]);
    }

    /**
     * Test User Can Leave Live Class
     * 
     * @return void
     */
    public function testUserCanLeaveLiveClass()
    {
        auth()->login(self::$student->user);
        $response = $this->post(route('user.live-classes.leave', self::$liveClassId));

        $response->assertJson([
            'status' => 200,
            'message' => 'User left Live Class'
        ]);
    }

    /**
     * test User validate Join Live Class
     * 
     * @return void
     */
    public function testUserValidateJoinLiveClass()
    {
        auth()->login(self::$student->user);

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
}
