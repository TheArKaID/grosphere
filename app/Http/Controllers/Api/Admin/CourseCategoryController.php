<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseCategoryRequest;
use App\Http\Requests\UpdateCourseCategoryRequest;
use App\Http\Resources\CourseCategoryResource;
use App\Services\CourseCategoryService;

class CourseCategoryController extends Controller
{
    private $service;

    public function __construct(CourseCategoryService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courseCategories = CourseCategoryResource::collection($this->service->getAll());

        if ($courseCategories->count() == 0) {
            throw new ModelGetEmptyException('Course Category');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Course Categorys retrieved successfully',
            'data' => $courseCategories->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCourseCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCourseCategoryRequest $request)
    {
        $validated = $request->validated();

        $courseCategory = $this->service->create($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Course Category Created Successfully',
            'data' => new CourseCategoryResource($courseCategory)
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $courseCategoryId
     * @return \Illuminate\Http\Response
     */
    public function show(int $courseCategoryId)
    {
        $courseCategory = $this->service->getById($courseCategoryId);

        return response()->json([
            'status' => 200,
            'message' => 'Course Category Retrieved Successfully',
            'data' => new CourseCategoryResource($courseCategory)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCourseCategoryRequest  $request
     * @param  int  $courseCategoryId
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCourseCategoryRequest $request, int $courseCategoryId)
    {
        $validated = $request->validated();

        $courseCategory = $this->service->update($courseCategoryId, $validated);

        return response()->json([
            'status' => 200,
            'message' => 'Course Category Updated Successfully',
            'data' => new CourseCategoryResource($courseCategory)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $courseCategoryId
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $courseCategoryId)
    {
        $this->service->delete($courseCategoryId);

        return response()->json([
            'status' => 200,
            'message' => 'Course Category Deleted Successfully'
        ], 200);
    }
}
