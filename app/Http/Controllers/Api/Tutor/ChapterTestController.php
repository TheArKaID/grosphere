<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChapterTestRequest;
use App\Http\Resources\ChapterTestResource;
use App\Services\ChapterTestService;
use Illuminate\Support\Facades\Auth;

class ChapterTestController extends Controller
{
    private $service;

    public function __construct(ChapterTestService $service)
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
        $chapterTest = $this->service->getOne($courseChapterId, Auth::user()->detail->id);

        return response()->json([
            'status' => 200,
            'message' => 'Chapter Test retrieved successfully',
            'data' => new ChapterTestResource($chapterTest)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreChapterTestRequest  $request
     * @param int $courseWorkId
     * @param int $courseChapterId
     * @return \Illuminate\Http\Response
     */
    public function store(StoreChapterTestRequest $request, $courseWorkId, $courseChapterId)
    {
        $validated = $request->validated();
        $validated['course_work_id'] = $courseWorkId;
        $validated['course_chapter_id'] = $courseChapterId;
        $validated['tutor_id'] = Auth::user()->detail->id;
        $chapterTest = $this->service->create($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Chapter Test created successfully',
            'data' => new ChapterTestResource($chapterTest)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $courseWorkId
     * @param int $courseChapterId
     * @return \Illuminate\Http\Response
     */
    public function destroy($courseWorkId, $courseChapterId)
    {
        $this->service->delete($courseChapterId, Auth::user()->detail->id);

        return response()->json([
            'status' => 200,
            'message' => 'Chapter Test deleted successfully'
        ], 200);
    }
}
