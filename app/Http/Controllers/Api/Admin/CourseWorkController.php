<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseWorkRequest;
use App\Http\Requests\UpdateCourseWorkRequest;
use App\Http\Resources\CourseWorkResource;
use App\Models\CourseWork;
use App\Services\CourseWorkService;
use Illuminate\Http\Request;

class CourseWorkController extends Controller
{
    protected $courseWorkService;

    public function __construct(CourseWorkService $courseWorkService)
    {
        $this->courseWorkService = $courseWorkService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courseWork = CourseWorkResource::collection($this->courseWorkService->getAll());

        if ($courseWork->count() == 0) {
            // throw new ModelGetEmptyException("CourseWork");
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $courseWork->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseWorkRequest $request)
    {
        $courseWork = $this->courseWorkService->create($request->validated());

        return response()->json([
            'status' => 200,
            'message' => 'CourseWork Created Successfully',
            'data' => $courseWork
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseWork $courseWork)
    {
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new CourseWorkResource($courseWork)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseWorkRequest $request, CourseWork $courseWork)
    {
        $courseWork = $this->courseWorkService->update($courseWork, $request->validated());

        return response()->json([
            'status' => 200,
            'message' => 'CourseWork Updated Successfully',
            'data' => $courseWork
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseWork $courseWork)
    {
        $this->courseWorkService->delete($courseWork);

        return response()->json([
            'status' => 200,
            'message' => 'CourseWork Deleted Successfully'
        ], 200);
    }

    /**
     * Add Teachers to CourseWork
     * 
     * @param CourseWork $courseWork
     * @param Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function addTeachers(CourseWork $courseWork, Request $request)
    {
        $request->validate([
            'teachers' => 'required|array',
            'teachers.*' => 'exists:teachers,id'
        ]);
        $courseWork = $this->courseWorkService->addTeachers($courseWork, $request->teachers);

        return response()->json([
            'status' => 200,
            'message' => 'Teachers Added Successfully'
        ], 200);
    }

    /**
     * Remove Teacher from CourseWork
     * 
     * @param CourseWork $courseWork
     * @param Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeTeacher(CourseWork $courseWork, Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id'
        ]);
        $courseWork = $this->courseWorkService->removeTeacher($courseWork, $request->teacher_id);

        return response()->json([
            'status' => 200,
            'message' => 'Teacher Disabled Successfully'
        ], 200);
    }
}
