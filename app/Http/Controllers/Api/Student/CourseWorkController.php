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
        $courseWorks = CourseWorkResource::collection($this->courseWorkService->getAll(null, true));

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

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new CourseWorkResource($courseWork)
        ], 200);
    }

    /**
     * Enroll a Course Work.
     * 
     * @param  int  $courseWorkId
     * @return \Illuminate\Http\Response
     */
    public function enroll(int $courseWorkId)
    {
        $courseWork = $this->courseWorkService->enroll($courseWorkId);

        if (!$courseWork) {
            return response()->json([
                'status' => 400,
                'message' => 'Failed to enroll course work'
            ], 400);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Enrollment Successfully',
            'data' => new CourseWorkResource($courseWork->courseWork)
        ], 200);
    }
}
