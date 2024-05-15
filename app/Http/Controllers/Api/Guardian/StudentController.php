<?php

namespace App\Http\Controllers\Api\Guardian;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Http\Request;

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
        $students = StudentResource::collection($this->studentService->getByGuardian(auth()->user()->detail->id));

        if (!$students->count()) {
            throw new ModelGetEmptyException("Student's Guardian");
        }

        return response()->json([
            'status' => 200,
            'message' => 'All student`s Guardian',
            'data' => $students->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        return response()->json([
            'status' => 200,
            'message' => 'Student detail',
            'data' => new StudentResource($student->with(['user', 'courseStudents.studentClasses', 'subscriptions'])->first())
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
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
