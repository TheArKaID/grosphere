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
use Illuminate\Validation\Rules\Password;

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
     * @param int $id
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
     * @param \Illuminate\Http\Request $request
     * @param Student $student
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStudentRequest $request, Student $student)
    {
        $validated = $request->validate([
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'birth_date' => 'nullable|date_format:d-m-Y',
            'birth_place' => 'nullable|string|max:255',
            'gender' => 'nullable|numeric|between:0,1',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|min:8|max:50',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $student->user_id,
            'username' => 'required_without:email|string|max:255|unique:users,username,' . $student->user_id,
            'id_number' => 'nullable|string|max:25',
            'photo' => 'nullable|string',
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()->mixedCase()]
        ], [
            'password.confirmed' => 'Password confirmation does not match',
            'password.min' => 'Password must be at least 8 characters',
            'password.letters' => 'Password must contain at least one letter',
            'password.numbers' => 'Password must contain at least one number',
            'password.mixed' => 'Password must contain at least one uppercase and one lowercase letter',
        ]);

        $student = new StudentResource($this->studentService->updateStudent($student->id, $validated));

        return response()->json([
            'status' => 200,
            'message' => 'Student Updated Successfully',
            'data' => $student
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
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
     * @param int $id
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
     * @param int $id
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
