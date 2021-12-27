<?php

namespace App\Http\Controllers\Api\User;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Resources\LiveClassResource;
use App\Models\LiveClass;
use App\Services\LiveClassService;

class LiveClassController extends Controller
{
    private $liveClassService;

    public function __construct(LiveClassService $liveClassService)
    {
        $this->liveClassService = $liveClassService;
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
        $liveUser = $this->liveClassService->userJoinLiveClass($id);

        if (!$liveUser) {
            return response()->json([
                'status' => 400,
                'message' => 'Live Class is not started yet or it is already ended'
            ], 400);
        }

        return response()->json([
            'status' => 200,
            'message' => 'User joined Live Class',
            'data' => [
                'url' => '',
                'tutor' => '',
                'end_time' => '',
                '' => ''
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
        $this->liveClassService->userLeaveLiveClass($id);

        return response()->json([
            'status' => 200,
            'message' => 'User left Live Class'
        ], 200);
    }
}
