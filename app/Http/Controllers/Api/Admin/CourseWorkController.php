<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseWorkRequest;
use App\Http\Requests\UpdateCourseWorkRequest;
use App\Models\CourseWork;
use App\Services\CourseWorkService;

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
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $this->courseWorkService->getAll()
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
            'data' => $courseWork
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
}
