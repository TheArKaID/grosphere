<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTutorAskAnswerRequest;
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
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $askAnswers = $this->service->getAllFormattedForTutor(Auth::user()->detail->id);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $askAnswers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTutorAskAnswerRequest  $request
     * @param int  $courseStudentid
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTutorAskAnswerRequest $request, $courseStudentid)
    {
        $validated = $request->validated();
        $validated['course_student_id'] = $courseStudentid;
        $validated['tutor_id'] = Auth::user()->detail->id;
        
        $askAnswer = $this->service->storeForTutor($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $askAnswer
        ]);
    }

    /**
     * @param int $course_student_id
     * 
     * @return \Illuminate\Http\Response
     */
    public function show($course_student_id)
    {
        $askAnswers = $this->service->getOneFormattedForTutor($course_student_id, Auth::user()->detail->id);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $askAnswers
        ]);
    }
}
