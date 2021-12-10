<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateStudentRequest;
use App\Services\StudentService;
use Illuminate\Http\Request;

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
        $students = $this->studentService->getAll();
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

        $student = $this->studentService->createStudent($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Student ditambahkan',
            'data' => [
                'user' => $student,
                'token' => $student->createToken('ApiToken')->plainTextToken
            ]
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
        $student = $this->studentService->getById($id);

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
                'response' => null
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
