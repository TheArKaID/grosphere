<?php

namespace App\Http\Controllers\Api\User;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Resources\LiveClassResource;
use App\Services\LiveClassService;
use App\Services\UserService;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

class LiveClassController extends Controller
{
    private $liveClassService, $userService;

    public function __construct(LiveClassService $liveClassService, UserService $userService)
    {
        $this->liveClassService = $liveClassService;
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $liveClasses = LiveClassResource::collection($this->liveClassService->getAllLiveClasses());

        if ($liveClasses->count() == 0) {
            throw new ModelGetEmptyException("Live Class");
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $liveClasses->response()->getData(true)
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $liveClass = new LiveClassResource($this->liveClassService->getLiveClassById($id));

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $liveClass
        ], 200);
    }

    /**
     * User Join Live Class
     * 
     * @param  int  $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function join($id)
    {
        $liveUser = $this->userService->userJoinLiveClass($id);

        if (!$liveUser) {
            return response()->json([
                'status' => 400,
                'message' => 'Live Class is not started yet or it is already ended'
            ], 400);
        }

        $liveClass = $liveUser->liveClass;

        return response()->json([
            'status' => 200,
            'message' => 'User joined Live Class',
            'data' => [
                'live_class_name' => $liveClass->class->name,
                'room' => $liveClass->uniq_code,
                'token' => Crypt::encrypt([
                    "user_id" => $liveUser->user_id,
                    "live_class_id" => $liveClass->id,
                    "valid_until" => Carbon::now()->addMinutes(5)->toDateTimeString()
                ]),
                'end_time' => Carbon::parse($liveClass->start_time)->addMinutes($liveClass->duration)->toDateTimeString(),
                'thumbnail' => asset('class/thumbnail/' . $liveClass->class->thumbnail),
            ]
        ], 200);
    }

    /**
     * User leave Live Class
     * 
     * @param  int  $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function leave($id)
    {
        $this->userService->userLeaveLiveClass($id);

        return response()->json([
            'status' => 200,
            'message' => 'User left Live Class'
        ], 200);
    }

    /**
     * Validate live class
     * 
     * @return \Illuminate\Http\Response
     */
    public function validateLiveClass()
    {
        try {
            $decrypted = Crypt::decrypt(request('token'));

            if ($decrypted['user_id'] != auth()->user()->id) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Invalid token'
                ], 400);
            }

            if (Carbon::parse($decrypted['valid_until'])->lt(Carbon::now())) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Token is expired'
                ], 400);
            }

            if (!$this->liveClassService->isLiveClassStartedByUniqCode(request('room'))) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Live Class is not started yet or it is already ended'
                ], 400);
            }

            $liveClass = $this->liveClassService->getLiveClassByUniqCode(request('room'));

            return response()->json([
                'status' => 200,
                'message' => 'Success',
                'data' => [
                    'tutor_name' => $liveClass->class->tutor->user->name,
                    'user_name' => auth()->user()->name,
                    'role' => auth()->user()->roles[0]->name
                ],
            ]);
        } catch (DecryptException $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid token'
            ], 400);
        }
    }
}
