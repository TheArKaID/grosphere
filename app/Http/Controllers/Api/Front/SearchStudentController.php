<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentFrontSearchResource;
use App\Services\StudentService;

class SearchStudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $students = $this->studentService->searchByEmail(request('email', ''));

        if ($students->count() > 0) {
            return response()->json([
                'status' => 200,
                'message' => 'Success',
                'data' => StudentFrontSearchResource::collection($students),
            ], 200);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Student not found'
        ], 200);
    }
}
