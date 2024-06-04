<?php

namespace App\Http\Controllers\Api\Student;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassSessionRequest;
use App\Http\Requests\UpdateClassSessionRequest;
use App\Http\Resources\ClassSessionResource;
use App\Models\ClassSession;
use App\Models\StudentClass;
use App\Services\ClassSessionService;
use App\Services\StudentService;
use Illuminate\Http\Request;

class ClassSessionController extends Controller
{
    public function __construct(
        protected ClassSessionService $classSessionService,
        protected StudentService $studentService
    ) {
        $this->classSessionService = $classSessionService;
        $this->studentService = $studentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        request()->merge(['active_only' => true]);
        $classSessions = ClassSessionResource::collection($this->classSessionService->getAll());

        if ($classSessions->count() == 0) {
            throw new ModelGetEmptyException('Class Sessions');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $classSessions->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClassSessionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassSession $classSession)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClassSessionRequest $request, ClassSession $classSession)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassSession $classSession)
    {
        //
    }

    public function studentClasses()
    {
        $classSessions = ClassSessionResource::collection($this->studentService->getAllStudentClassSessions());

        if ($classSessions->count() == 0) {
            throw new ModelGetEmptyException('Student Classes');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $classSessions->response()->getData(true)
        ], 200);
    }

    public function showStudentClasses(StudentClass $studentClass) {
        
    }

    public function enroll(Request $request, int $classSessionId)
    {
        $this->studentService->enrollStudentToClass($classSessionId);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => []
        ], 200);
    }
}
