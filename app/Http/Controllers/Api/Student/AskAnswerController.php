<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentAskAnswerRequest;
use App\Services\AskAnswerService;
use Illuminate\Support\Facades\Auth;

class AskAnswerController extends Controller
{
    private $service;

    public function __construct(AskAnswerService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  int  $courseWorkId
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(int $courseWorkId)
    {
        $askAnswer = $this->service->getAllFormatted($courseWorkId, Auth::user()->detail->id);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $askAnswer
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAskAnswerRequest  $request
     * @param  int  $courseWorkId
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStudentAskAnswerRequest $request, int $courseWorkId)
    {
        $validated = $request->validated();
        $validated['student_id'] = Auth::user()->detail->id;
        $validated['course_work_id'] = $courseWorkId;
        $askAnswer = $this->service->store($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Ask has been created',
            'data' => $askAnswer
        ], 200);
    }
}
