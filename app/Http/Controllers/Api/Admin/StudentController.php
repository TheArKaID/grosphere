<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
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
        $students = StudentResource::collection($this->studentService->getAll());

        if ($students->count() == 0) {
            throw new ModelGetEmptyException("Student");
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $students->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStudentRequest $request)
    {
        $validated = $request->validated();

        $student = new StudentResource($this->studentService->createStudent($validated));

        return response()->json([
            'status' => 200,
            'message' => 'Student Created Successfully',
            'data' => $student
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $student = $this->studentService->getById($id);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new StudentResource($student)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateStudentRequest $request
     * @param Student $student
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStudentRequest $request, Student $student)
    {
        $data = $request->validated();
        $student = new StudentResource($this->studentService->updateStudent($student->id, $data));

        return response()->json([
            'status' => 200,
            'message' => 'Student Updated Successfully',
            'data' => $student
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->studentService->deleteStudent($id);

        return response()->json([
            'status' => 200,
            'message' => 'Student Deleted Successfully',
        ], 200);
    }

    /**
     * Change Password 
     * 
     * @param App\Http\Requests\UpdatePasswordRequest $request
     * @param string $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function changePassword(UpdatePasswordRequest $request, $id)
    {
        $validated = $request->validated();

        $this->studentService->changePassword($id, $validated['password']);

        return response()->json([
            'status' => 200,
            'message' => 'Student Password Changed Successfully'
        ], 200);
    }

    /**
     * Sync Guardians
     * 
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function syncGuardians(Request $request, $id)
    {
        $validated = $request->validate([
            'guardian_ids' => 'required|array',
            'guardian_ids.*' => 'integer|exists:guardians,id',
        ], [
            'guardian_ids.required' => 'Guardians is required',
            'guardian_ids.*.integer' => 'Guardians must be an integer',
            'guardian_ids.*.exists' => 'Guardians not found'
        ]);

        $this->studentService->syncGuardians($id, $validated['guardian_ids']);

        return response()->json([
            'status' => 200,
            'message' => 'Guardians Synced Successfully'
        ], 200);
    }
}
