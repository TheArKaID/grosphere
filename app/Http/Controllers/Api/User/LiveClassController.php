<?php

namespace App\Http\Controllers\Api\User;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Resources\LiveClassResource;
use App\Http\Resources\ValidatedLiveClassResource;
use App\Services\LiveClassService;
use App\Services\LiveUserService;
use App\Services\UserService;
use Illuminate\Support\Carbon;

class LiveClassController extends Controller
{
    private $liveClassService, $userService, $liveUserService;

    public function __construct(
        LiveClassService $liveClassService,
        UserService $userService,
        LiveUserService $liveUserService
    ) {
        $this->liveClassService = $liveClassService;
        $this->userService = $userService;
        $this->liveUserService = $liveUserService;
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
            'data' => $liveClasses
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

        if (gettype($liveUser) == 'string') {
            return response()->json([
                'status' => 400,
                'message' => $liveUser
            ], 400);
        }

        $liveClass = $liveUser->liveClass;

        return response()->json([
            'status' => 200,
            'message' => 'User joined Live Class',
            'data' => [
                'live_class_name' => $liveClass->class->name,
                'token' => $liveUser->token,
                'end_time' => Carbon::parse($liveClass->start_time)->addMinutes($liveClass->duration)->toDateTimeString(),
                'duration' => $liveClass->duration,
                'thumbnail' => $liveClass->class->thumbnail ? asset('storage/class/thumbnail/' . $liveClass->class->thumbnail) : asset('storage/class/thumbnail/default.png'),
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
        $liveUser = $this->liveUserService->getLiveUserByToken(request('token'));

        if (!$this->liveClassService->isLiveClassStarted($liveUser->liveClass->id)) {
            return response()->json([
                'status' => 400,
                'message' => 'Live Class is not started yet or it is already ended'
            ], 400);
        }

        if (!$this->liveUserService->invalidateLiveUserToken($liveUser->id)) {
            return response()->json([
                'status' => 400,
                'message' => 'Token is invalid'
            ], 400);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Live Class validated',
            'data' => new ValidatedLiveClassResource($liveUser),
        ]);
    }
}
