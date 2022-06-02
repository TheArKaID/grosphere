<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChapterTestRequest;
use App\Http\Requests\StoreTutorScoreStudentAnswerRequest;
use App\Http\Resources\ChapterTestResource;
use App\Http\Resources\StudentTestResource;
use App\Services\ChapterTestService;
use App\Services\TakeChapterTestService;
use Illuminate\Support\Facades\Auth;

class ChapterTestController extends Controller
{
    private $service, $takeChapterTestService;

    public function __construct(ChapterTestService $service, TakeChapterTestService $takeChapterTestService)
    {
        $this->service = $service;
        $this->takeChapterTestService = $takeChapterTestService;
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

    /**
     * Show Results
     * 
     * @param int $courseWorkId
     * @param int $courseChapterId
     * 
     * @return \Illuminate\Http\Response
     */
    public function showResults($courseWorkId, $courseChapterId)
    {
        $results = $this->service->getResults($courseChapterId, Auth::user()->detail->id);

        return response()->json([
            'status' => 200,
            'message' => 'Chapter Test results retrieved successfully',
            'data' => $results
        ], 200);
    }

    /**
     * Show Results
     * 
     * @param int $courseWorkId
     * @param int $courseChapterId
     * @param int $studentTestId
     * 
     * @return \Illuminate\Http\Response
     */
    public function showResult($courseWorkId, $courseChapterId, $studentTestId)
    {
        $results = $this->takeChapterTestService->getStudentTestByID($courseChapterId, $studentTestId, Auth::user()->detail->id);

        return response()->json([
            'status' => 200,
            'message' => 'Chapter Test result retrieved successfully',
            'data' => new StudentTestResource($results)
        ], 200);
    }

    /**
     * Score Student Answer
     * 
     * @param  \App\Http\Requests\StoreTutorScoreStudentAnswerRequest  $request
     * @param int $courseWorkId
     * @param int $courseChapterId
     * @param int $studentTestId
     * @param int $studentAnswerId
     * 
     * @return \Illuminate\Http\Response
     */
    public function scoreStudentAnswer(StoreTutorScoreStudentAnswerRequest $request, $courseWorkId, $courseChapterId, $studentTestId, $studentAnswerId)
    {
        $validated = $request->validated();

        $this->takeChapterTestService->scoreStudentAnswer($courseChapterId, $studentTestId, $studentAnswerId, $validated['is_correct']);

        return response()->json([
            'status' => 200,
            'message' => 'Scored student answer successfully',
        ], 200);
    }
}
