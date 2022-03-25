<?php

namespace App\Http\Controllers\Api\Student;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseChapterStudentResource;
use App\Services\CourseChapterStudentService;

class CourseChapterController extends Controller
{
    private $courseChapterStudentService;

    public function __construct(CourseChapterStudentService $courseChapterStudentService)
    {
        $this->courseChapterStudentService = $courseChapterStudentService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $courseWorkId
     * @return \Illuminate\Http\Response
     */
    public function index($courseWorkId)
    {
        $courseChapters = CourseChapterStudentResource::collection($this->courseChapterStudentService->getAll($courseWorkId));

        if (count($courseChapters) == 0) {
            throw new ModelGetEmptyException('Course Chapter');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $courseChapters->response()->getData(true)
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $courseWorkId
     * @param  int  $courseChapterId
     * @return \Illuminate\Http\Response
     */
    public function show(int $courseWorkId, int $courseChapterId)
    {
        $chapter = $this->courseChapterStudentService->getById($courseWorkId, $courseChapterId);

        if (gettype($chapter) == 'string') {
            return response()->json([
                'status' => 400,
                'message' => $chapter,
            ], 400);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new CourseChapterStudentResource($chapter)
        ], 200);
    }
}
