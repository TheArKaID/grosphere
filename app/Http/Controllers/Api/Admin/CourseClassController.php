<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseClassRequest;
use App\Http\Requests\UpdateCourseClassRequest;
use App\Http\Resources\CourseClassResource;
use App\Services\CourseClassService;

class CourseClassController extends Controller
{
    private $courseClassService;

    public function __construct(CourseClassService $courseClassService)
    {
        $this->courseClassService = $courseClassService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courseClasses = CourseClassResource::collection($this->courseClassService->getAllCourseClasses());

        if ($courseClasses->count() == 0) {
            throw new ModelGetEmptyException('Course Class');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $courseClasses->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCourseClassRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCourseClassRequest $request)
    {
        $validated = $request->validated();

        $courseClass = $this->courseClassService->createCourseClass($validated);

        return response()->json([
            'status' => 201,
            'message' => 'Success',
            'data' => new CourseClassResource($courseClass)
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $courseClassid
     * @return \Illuminate\Http\Response
     */
    public function show(int $courseClassId)
    {
        $courseClass = $this->courseClassService->getCourseClassById($courseClassId);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new CourseClassResource($courseClass)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCourseClassRequest  $request
     * @param  int  $courseClassid
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCourseClassRequest $request, int $courseClassId)
    {
        $validated = $request->validated();

        $courseClass = $this->courseClassService->updateCourseClass($courseClassId, $validated);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new CourseClassResource($courseClass)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $courseClassid
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $courseClassId)
    {
        $this->courseClassService->deleteCourseClass($courseClassId);

        return response()->json([
            'status' => 200,
            'message' => 'Success'
        ], 200);
    }
}
