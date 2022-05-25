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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $askAnswer = $this->service->getAllFormatted();

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $askAnswer
        ], 200);
    }

    /**
     * @param $courseStudentId
     * 
     * @return \Illuminate\Http\Response
     */
    public function show($courseStudentId)
    {
        $askAnswer = $this->service->getOneFormattedForStudent($courseStudentId, Auth::user()->detail->id);

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
     * @param  int  $courseStudentId
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStudentAskAnswerRequest $request, int $courseStudentId)
    {
        $validated = $request->validated();
        $validated['student_id'] = Auth::user()->detail->id;
        $validated['course_student_id'] = $courseStudentId;
        $askAnswer = $this->service->store($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Ask has been created',
            'data' => $askAnswer
        ], 200);
    }
}
