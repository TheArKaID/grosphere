<?php

namespace App\Http\Controllers\Api\Super;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\Services\StudentService;

class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = StudentResource::collection($this->studentService->getAll());

        if ($students->count() == 0) {
            // throw new ModelGetEmptyException("Student");
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $students->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $agencyId, string $studentId)
    {
        $student = $this->studentService->getById($studentId)->load(['user', 'courseStudents.studentClasses', 'subscriptions']);
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new StudentResource($student)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        //
    }
}
