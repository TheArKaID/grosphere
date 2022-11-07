<?php

namespace App\Http\Controllers\Api\Student;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Resources\LiveClassResource;
use App\Services\LiveClassService;

class LiveClassController extends Controller
{
    private $liveClassService;

    public function __construct(LiveClassService $liveClassService)
    {
        $this->liveClassService = $liveClassService;
    }

    /**
     * Enroll a Live Class.
     * 
     * @param  int  $liveClassId
     * @return \Illuminate\Http\Response
     */
    public function enroll(int $liveClassId)
    {
        $liveClassStudent = $this->liveClassService->enrollByLiveClassIdAndStudentId($liveClassId, auth()->user()->detail->id);

        if (!$liveClassStudent) {
            return response()->json([
                'status' => 400,
                'message' => 'Failed to enroll live class'
            ], 400);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Enrollment Successfully',
            'data' => new LiveClassResource($liveClassStudent->liveClass)
        ], 200);
    }
}
