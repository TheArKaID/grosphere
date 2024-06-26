<?php

namespace App\Http\Controllers\Api\Student;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseStudentResource;
use App\Services\CourseStudentService;

class CourseStudentController extends Controller
{
    private $courseStudentService;

    public function __construct(CourseStudentService $courseStudentService)
    {
        $this->courseStudentService = $courseStudentService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courseStudents = CourseStudentResource::collection($this->courseStudentService->getAll());

        if (count($courseStudents) == 0) {
            // throw new ModelGetEmptyException('CourseStudent');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $courseStudents->response()->getData(true)
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $courseStudentId
     * @return \Illuminate\Http\Response
     */
    public function show(string $courseStudentId)
    {
        $courseStudent = $this->courseStudentService->getById($courseStudentId);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new CourseStudentResource($courseStudent)
        ], 200);
    }
}
