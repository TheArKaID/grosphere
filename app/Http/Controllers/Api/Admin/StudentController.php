<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Services\StudentService;

class StudentController extends Controller
{
    private $studentService;

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
        $students = StudentResource::collection($this->studentService->getAll());

        $response = $students->count() == 0 ? [] : $students->response()->getData(true);
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'response' => $response
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateStudentRequest $request)
    {
        $validated = $request->validated();

        $student = new StudentResource($this->studentService->createStudent($validated));

        return response()->json([
            'status' => 200,
            'message' => 'Student created successfully',
            'data' => $student
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $student = new StudentResource($this->studentService->getById($id));

        $response = $student->response()->getData(true);
        $response = count($response) == 0 ? null : $response;

        if ($response) {
            return response()->json([
                'status' => 200,
                'message' => 'Success',
                'response' => $response
            ], 200);
        } else {
            return response()->json([
                'status' => 204,
                'message' => 'Student not found',
                'response' => $response
            ], 200);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStudentRequest $request, $id)
    {
        $validated = $request->validated();

        $student = new StudentResource($this->studentService->updateStudent($id, $validated));

        return response()->json([
            'status' => 200,
            'message' => 'Student Data Updated',
            'response' => $student
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->studentService->deleteStudent($id);

        return response()->json([
            'status' => 200,
            'message' => 'Student Deleted'
        ], 200);
    }
}
