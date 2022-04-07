<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChapterAssignmentFileRequest;
use App\Http\Requests\ChapterAssignmentRequest;
use App\Http\Requests\DeleteChapterAssignmentFileRequest;
use App\Http\Resources\ChapterAssignmentResource;
use App\Models\ChapterAssignment;
use App\Services\ChapterAssignmentService;
use Illuminate\Support\Facades\Auth;

class ChapterAssignmentController extends Controller
{
    private $service;

    public function __construct(ChapterAssignmentService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $courseWorkId
     * @param int $courseChapterId
     * 
     * @return \Illuminate\Http\Response
     */
    public function index($courseWorkId, $courseChapterId)
    {
        $chapterAssignment = $this->service->getOne($courseChapterId, Auth::user()->detail->id);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new ChapterAssignmentResource($chapterAssignment)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ChapterAssignmentRequest  $request
     * @param int $courseWorkId
     * @param int $courseChapterId
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(ChapterAssignmentRequest $request, $courseWorkId, $courseChapterId)
    {
        $validated = $request->validated();

        $validated['course_work_id'] = $courseWorkId;
        $validated['course_chapter_id'] = $courseChapterId;
        $validated['tutor_id'] = Auth::user()->detail->id;

        $chapterAssignment = $this->service->create($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Chapter Assignment Saved Successfully',
            'data' => new ChapterAssignmentResource($chapterAssignment)
        ], 200);
    }

    /**
     * Upload file to Chapter Assignment
     * 
     * @param StoreChapterAssignmentFileRequest $request
     * @param int $courseWorkId
     * @param int $courseChapterId
     * @param int $chapterAssignmentId
     *  
     * @return \Illuminate\Http\Response
     */
    public function uploadFile(StoreChapterAssignmentFileRequest $request, $courseWorkId, $courseChapterId)
    {
        $validated = $request->validated();
        $uploaded = $this->service->uploadFile($validated['file'], $courseWorkId, $courseChapterId, Auth::user()->detail->id);

        if ($uploaded) {
            return response()->json([
                'status' => 200,
                'message' => 'File Uploaded Successfully',
                'url' => $uploaded
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'File Upload Failed'
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $courseWorkId
     * @param int $courseChapterId
     * 
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $courseWorkId, int $courseChapterId)
    {
        $this->service->delete($courseWorkId, $courseChapterId);

        return response()->json([
            'status' => 200,
            'message' => 'Chapter Assignment Deleted Successfully'
        ], 200);
    }

    /**
     * Delete file from Chapter Assignment
     * 
     * @param DeleteChapterAssignmentFileRequest $request
     * @param int $courseWorkId
     * @param int $courseChapterId
     * 
     * @return \Illuminate\Http\Response
     */
    public function deleteFile(DeleteChapterAssignmentFileRequest $request, int $courseWorkId, int $courseChapterId)
    {
        $validated = $request->validated();
        $deleted = $this->service->deleteFile($courseWorkId, $courseChapterId, $validated['filename'], Auth::user()->detail->id);

        if ($deleted) {
            return response()->json([
                'status' => 200,
                'message' => 'File Deleted Successfully'
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'File Not Found, Deletion Failed'
            ], 400);
        }
    }
}
