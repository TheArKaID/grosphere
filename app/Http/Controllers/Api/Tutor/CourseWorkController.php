<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseWorkRequest;
use App\Http\Requests\UpdateCourseWorkRequest;
use App\Http\Resources\CourseWorkResource;
use App\Services\CourseWorkService;

class CourseWorkController extends Controller
{
    private $courseWorkService;

    public function __construct(CourseWorkService $courseWorkService)
    {
        $this->courseWorkService = $courseWorkService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courseWorks = CourseWorkResource::collection($this->courseWorkService->getAll(auth()->user()->detail->id));

        if ($courseWorks->count() == 0) {
            throw new ModelGetEmptyException('Course Work');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $courseWorks->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCourseWorkRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCourseWorkRequest $request)
    {
        $validated = $request->validated();
        $validated['tutor_id'] = auth()->user()->detail->id;

        $courseWork = $this->courseWorkService->createCourseWork($validated);

        return response()->json([
            'status' => 201,
            'message' => 'Success',
            'data' => new CourseWorkResource($courseWork)
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $courseWorkId
     * @return \Illuminate\Http\Response
     */
    public function show(int $courseWorkId)
    {
        $courseWork = $this->courseWorkService->getCourseWorkById($courseWorkId, auth()->user()->detail->id);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new CourseWorkResource($courseWork)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCourseWorkRequest  $request
     * @param  int  $courseWorkId
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCourseWorkRequest $request, int $courseWorkId)
    {
        $validated = $request->validated();

        $courseWork = $this->courseWorkService->updateCourseWork($courseWorkId, $validated, auth()->user()->detail->id);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new CourseWorkResource($courseWork)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $courseWorkId
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $courseWorkId)
    {
        $this->courseWorkService->deleteCourseWork($courseWorkId, auth()->user()->detail->id);

        return response()->json([
            'status' => 200,
            'message' => 'Success'
        ], 200);
    }
}
