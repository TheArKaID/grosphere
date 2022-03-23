<?php

namespace App\Http\Controllers\Api\Student;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
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
        $courseWorks = CourseWorkResource::collection($this->courseWorkService->getAll());

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
     * Display the specified resource.
     *
     * @param  int  $courseWorkId
     * @return \Illuminate\Http\Response
     */
    public function show(int $courseWorkId)
    {
        $courseWork = $this->courseWorkService->getCourseWorkById($courseWorkId);

        if ($courseWork == null) {
            throw new ModelGetEmptyException('Course Work');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new CourseWorkResource($courseWork)
        ], 200);
    }
}
