<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteTutorCourseChapterImageRequest;
use App\Http\Requests\StoreCourseChapterRequest;
use App\Http\Requests\StoreTutorCourseChapterImageRequest;
use App\Http\Requests\UpdateCourseChapterRequest;
use App\Http\Resources\CourseChapterResource;
use App\Services\CourseChapterService;
use Illuminate\Support\Facades\Auth;

class CourseChapterController extends Controller
{
    private $courseChapterService;

    public function __construct(
        CourseChapterService $courseChapterService
    ) {
        $this->courseChapterService = $courseChapterService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $courseWorkId
     * 
     * @return \Illuminate\Http\Response
     */
    public function index($courseWorkId)
    {
        $courseChapters = CourseChapterResource::collection($this->courseChapterService->getAll($courseWorkId, Auth::user()->detail->id));

        if (count($courseChapters) == 0) {
            throw new ModelGetEmptyException('Course Chapters');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $courseChapters->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCourseChapterRequest  $request
     * @param int $courseWorkId
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCourseChapterRequest $request, $courseWorkId)
    {
        $validated = $request->validated();

        $validated['course_work_id'] = $courseWorkId;
        $validated['tutor_id'] = Auth::user()->detail->id;

        $courseChapter = $this->courseChapterService->create($validated);

        return response()->json([
            'status' => 201,
            'message' => 'Course Chapter Created',
            'data' => new CourseChapterResource($courseChapter)
        ], 201);
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
        $courseChapter = $this->courseChapterService->getById($courseWorkId, $courseChapterId, Auth::user()->detail->id);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new CourseChapterResource($courseChapter)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCourseChapterRequest  $request
     * @param  int  $courseWorkId
     * @param  int  $courseChapterId
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCourseChapterRequest $request, int $courseWorkId, int $courseChapterId)
    {
        $validated = $request->validated();

        $courseChapter = $this->courseChapterService->update($courseWorkId, $courseChapterId, $validated, Auth::user()->detail->id);

        return response()->json([
            'status' => 200,
            'message' => 'Course Chapter Updated',
            'data' => new CourseChapterResource($courseChapter)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $courseWorkId
     * @param  int  $courseChapterId
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $courseWorkId, int $courseChapterId)
    {
        $result = $this->courseChapterService->delete($courseWorkId, $courseChapterId, Auth::user()->detail->id);

        if (gettype($result) != 'boolean') {
            return response()->json([
                'status' => 400,
                'message' => $result
            ], 400);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Course Chapter Deleted'
        ], 200);
    }

    /**
     * Upload chapter image
     * 
     * @param StoreTutorCourseChapterImageRequest $request
     * @param int $courseWorkId
     * 
     * @return \Illuminate\Http\Response
     */
    public function uploadImage(StoreTutorCourseChapterImageRequest $request, int $courseWorkId)
    {
        $validated = $request->validated();

        $uploaded = $this->courseChapterService->uploadImage($validated['image'], $courseWorkId);

        if ($uploaded) {
            return response()->json([
                'status' => 200,
                'message' => 'Image Uploaded Successfully',
                'url' => $uploaded
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Image Upload Failed'
            ], 400);
        }
    }

    /**
     * Delete chapter image
     * 
     * @param DeleteTutorCourseChapterImageRequest $request
     * @param int $courseWorkId
     * 
     * @return \Illuminate\Http\Response
     */
    public function deleteImage(DeleteTutorCourseChapterImageRequest $request, $courseWorkId)
    {
        $validated = $request->validated();
        $deleted = $this->courseChapterService->deleteImage($validated['imagename'], $courseWorkId);

        if ($deleted) {
            return response()->json([
                'status' => 200,
                'message' => 'Image Deleted Successfully'
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Image Deletion Failed'
            ], 400);
        }
    }
}
