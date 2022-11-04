<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseWorkRequest;
use App\Http\Requests\UpdateCourseWorkRequest;
use App\Http\Resources\CourseWorkResource;
use App\Services\CourseWorkService;
use Illuminate\Http\Request;

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
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCourseWorkRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCourseWorkRequest $request)
    {
        $validated = $request->validated();

        $courseWork = $this->courseWorkService->createCourseWork($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new CourseWorkResource($courseWork)
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $courseWorkid
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
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCourseWorkRequest  $request
     * @param  int  $courseWorkid
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCourseWorkRequest $request, int $courseWorkId)
    {
        $validated = $request->validated();

        $courseWork = $this->courseWorkService->updateCourseWork($courseWorkId, $validated);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new CourseWorkResource($courseWork)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $courseWorkid
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $courseWorkId)
    {
        $this->courseWorkService->deleteCourseWork($courseWorkId);

        return response()->json([
            'status' => 200,
            'message' => 'Success'
        ], 200);
    }

    /**
     * Enroll Student to Course Work
     * 
     * @param  Request  $request
     * @param  int  $courseWorkId
     * 
     * @return \Illuminate\Http\Response
     */
    public function enrollStudent(Request $request, int $courseWorkId)
    {
        $enrolled = $this->courseWorkService->enrollByCourseWorkIdAndStudentId($courseWorkId, $request['student_id']);

        if (gettype($enrolled) == 'string') {
            return response()->json([
                'status' => 400,
                'message' => $enrolled
            ], 400);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Student enrolled to course work'
        ], 200);
    }

    /**
     * Unenroll Student from Course Work
     * 
     * @param  Request  $request
     * @param  int  $courseWorkId
     * 
     * @return \Illuminate\Http\Response
     */
    public function unenrollStudent(Request $request, int $courseWorkId)
    {
        $unenrolled = $this->courseWorkService->unenrollByCourseWorkIdAndStudentId($courseWorkId, $request['student_id']);

        if (gettype($unenrolled) == 'string') {
            return response()->json([
                'status' => 400,
                'message' => $unenrolled
            ], 400);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Student unenrolled from course work'
        ], 200);
    }

    /**
     * Enroll Group to Course Work
     * 
     * @param  Request  $request
     * @param  int  $courseWorkId
     * 
     * @return \Illuminate\Http\Response
     */
    public function enrollGroup(Request $request, int $courseWorkId)
    {
        $this->courseWorkService->enrollByCourseWorkIdAndGroupId($courseWorkId, $request['group_id']);

        return response()->json([
            'status' => 200,
            'message' => 'Group enrolled to course work'
        ], 200);
    }

    /**
     * Unenroll Group from Course Work
     * 
     * @param  int  $courseWorkId
     * @param  int  $groupId
     * 
     * @return \Illuminate\Http\Response
     */
    public function unenrollGroup(int $courseWorkId, int $groupId)
    {
        $this->courseWorkService->unenrollByCourseWorkIdAndGroupId($courseWorkId, $groupId);

        return response()->json([
            'status' => 200,
            'message' => 'Group unenrolled from course work'
        ], 200);
    }
}
