<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Http\Resources\TeacherCollection;
use App\Http\Resources\TeacherResource;
use App\Models\Teacher;
use App\Services\TeacherService;

class TeacherController extends Controller
{
    private $teacherService;

    public function __construct(TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teachers = new TeacherCollection($this->teacherService->getAll());

        if ($teachers->count() == 0) {
            // throw new ModelGetEmptyException("Teacher");
        }

        return response()->json([
            'status' => 200,
            'message' => "Success",
            'data' => $teachers->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreTeacherRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTeacherRequest $request)
    {
        $validated = $request->validated();
        $teacher = $this->teacherService->create($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Teacher Created Successfully',
            'data' => new TeacherResource($teacher)
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param string $teacher
     * @return \Illuminate\Http\Response
     */
    public function show(string $id)
    {
        $teacher = $this->teacherService->getById($id);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new TeacherResource($teacher)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateTeacherRequest $request
     * @param Teacher $teacher
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        $data = $request->validated();

        $teacher = $this->teacherService->update($teacher->id, $data);

        return response()->json([
            'status' => 200,
            'message' => 'Teacher Updated Successfully',
            'data' => new TeacherResource($teacher)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        $this->teacherService->delete($id);

        return response()->json([
            'status' => 200,
            'message' => 'Teacher Deleted Successfully',
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
    public function changePassword(UpdatePasswordRequest $request, string $id)
    {
        $validated = $request->validated();

        $this->teacherService->changePassword($id, $validated['password']);

        return response()->json([
            'status' => 200,
            'message' => 'Teacher Password Changed Successfully'
        ], 200);
    }
}
