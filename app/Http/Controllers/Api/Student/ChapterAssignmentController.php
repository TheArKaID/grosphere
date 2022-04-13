<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteStudentChapterAssignmentFileRequest;
use App\Http\Requests\StoreStudentChapterAssignmentFileRequest;
use App\Http\Requests\StoreStudentChapterAssignmentRequest;
use App\Http\Resources\ChapterAssignmentResource;
use App\Http\Resources\ChapterStudentAssignmentResource;
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
        $chapterAssignment = $this->service->getOne($courseChapterId);
        
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new ChapterAssignmentResource($chapterAssignment)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreChapterAssignmentRequest  $request
     * @param int $courseWorkId
     * @param int $courseChapterId
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStudentChapterAssignmentRequest $request, $courseWorkId, $courseChapterId)
    {
        $validated = $request->validated();

        $studentAssignment = $this->service->storeStudentAnswer($courseWorkId, $courseChapterId, Auth::user()->detail->id, $validated['answer']);
        
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new ChapterStudentAssignmentResource($studentAssignment)
        ], 200);
    }

    /**
     * Get Student Chapter Assignment Answer
     * 
     * @param int $courseWorkId
     * @param int $courseChapterId
     * 
     * @return \Illuminate\Http\Response
     */
    public function answer($courseWorkId, $courseChapterId)
    {
        $studentAssignment = $this->service->getStudentAnswer($courseWorkId, $courseChapterId, Auth::user()->detail->id);
        
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new ChapterStudentAssignmentResource($studentAssignment)
        ], 200);
    }
    /**
     * Upload Chapter Assignment File
     * 
     * @param StoreStudentChapterAssignmentFileRequest $request
     * @param int $courseWorkId
     * @param int $courseChapterId
     * 
     * @return \Illuminate\Http\Response
     */
    public function uploadFile(StoreStudentChapterAssignmentFileRequest $request, $courseWorkId, $courseChapterId)
    {
        $validated = $request->validated();
        $uploaded = $this->service->uploadStudentFile($validated['file'], $courseWorkId, $courseChapterId, Auth::user()->detail->id);

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
     * Delete Chapter Assignment File
     * 
     * @param DeleteStudentChapterAssignmentFileRequest $request
     * @param int $courseWorkId
     * @param int $courseChapterId
     * 
     * @return \Illuminate\Http\Response
     */
    public function deleteFile(DeleteStudentChapterAssignmentFileRequest $request, $courseWorkId, $courseChapterId)
    {
        $validated = $request->validated();
        $deleted = $this->service->deleteStudentFile($courseWorkId, $courseChapterId, Auth::user()->detail->id, $validated['filename']);

        if ($deleted) {
            return response()->json([
                'status' => 200,
                'message' => 'File Deleted Successfully'
            ], 200);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'File Deletion Failed'
            ], 400);
        }
    }
}
