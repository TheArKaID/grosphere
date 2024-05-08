<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuardianRequest;
use App\Http\Requests\UpdateGuardianRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Resources\GuardianResource;
use App\Models\Guardian;
use App\Models\Student;
use App\Services\GuardianService;
use Illuminate\Http\Request;

class GuardianController extends Controller
{
    private $guardianService;

    public function __construct(GuardianService $guardianService)
    {
        $this->guardianService = $guardianService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $guardians = GuardianResource::collection($this->guardianService->getAll());

        if ($guardians->count() == 0) {
            throw new ModelGetEmptyException("Guardian");
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $guardians->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGuardianRequest $request)
    {
        $validated = $request->validated();
        $guardian = new GuardianResource($this->guardianService->create($validated));

        return response()->json([
            'status' => 200,
            'message' => 'Guardian Created Successfully',
            'data' => $guardian
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
        $guardian = $this->guardianService->getById($id);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new GuardianResource($guardian)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateGuardianRequest $request, $id)
    {
        $validated = $request->validated();
        $guardian = new GuardianResource($this->guardianService->update($id, $validated));

        return response()->json([
            'status' => 200,
            'message' => 'Guardian Updated Successfully',
            'data' => $guardian
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
        $this->guardianService->delete($id);

        return response()->json([
            'status' => 200,
            'message' => 'Guardian Deleted Successfully',
        ], 200);
    }

    /**
     * Add a student to a guardian
     * 
     * @param Request $request
     * @param Guardian $guardian
     * 
     * @return \Illuminate\Http\Response
     */
    public function addStudent(Request $request, Guardian $guardian)
    {
        $validated = $request->validate([
            'students' => 'required|array',
            'students.*' => 'required|integer|exists:students,id'
        ]);

        $this->guardianService->addStudent($guardian, $validated['students']);

        return response()->json([
            'status' => 200,
            'message' => 'Student Added Successfully'
        ], 200);
    }

    /**
     * Remove a Student from a guardian
     * 
     * @param Request $request
     * @param Guardian $guardian
     * @param Student $student
     * 
     * @return \Illuminate\Http\Response
     */
    public function removeStudent(Request $request, Guardian $guardian, Student $student)
    {
        $this->guardianService->removeStudent($guardian, $student->id);

        return response()->json([
            'status' => 200,
            'message' => 'Student Removed Successfully'
        ], 200);
    }

    /**
     * Change Guardian Password
     * 
     * @param App\Http\Requests\UpdatePasswordRequest $request
     * @param int $guardian_id
     * 
     * @return \Illuminate\Http\Response
     */
    public function changePassword(UpdatePasswordRequest $request, $guardian_id)
    {
        $validated = $request->validated();

        $this->guardianService->changePassword($guardian_id, $validated['password']);

        return response()->json([
            'status' => 200,
            'message' => 'Guardian Password Changed Successfully'
        ], 200);
    }
}