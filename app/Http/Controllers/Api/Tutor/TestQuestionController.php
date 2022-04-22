<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestQuestionRequest;
use App\Http\Requests\UpdateTestQuestionRequest;
use App\Http\Resources\TestQuestionResource;
use App\Services\TestQuestionService;
use Illuminate\Support\Facades\Auth;

class TestQuestionController extends Controller
{
    private $service;

    public function __construct(TestQuestionService $service)
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
        $questions = TestQuestionResource::collection($this->service->getAllQuestions($courseChapterId, Auth::user()->detail->id));

        if ($questions->count() == 0) {
            throw new ModelGetEmptyException('Test Questions');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Test Questions retrieved successfully',
            'data' => $questions->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTestQuestionRequest  $request
     * @param int $courseWorkId
     * @param int $courseChapterId
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTestQuestionRequest $request, $courseWorkId, $courseChapterId)
    {
        $validated = $request->validated();
        $validated['course_work_id'] = $courseWorkId;
        $validated['course_chapter_id'] = $courseChapterId;
        $validated['tutor_id'] = Auth::user()->detail->id;
        $question = $this->service->addQuestion($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Question added to Chapter Test successfully',
            'data' => new TestQuestionResource($question)
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $courseWorkId
     * @param int $courseChapterId
     * @param  int  $testQuestionId
     * 
     * @return \Illuminate\Http\Response
     */
    public function show($courseWorkId, $courseChapterId, $testQuestionId)
    {
        $question = $this->service->getOne($courseChapterId, $testQuestionId, Auth::user()->detail->id);

        return response()->json([
            'status' => 200,
            'message' => 'Test Question retrieved successfully',
            'data' => new TestQuestionResource($question)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTestQuestionRequest  $request
     * @param int $courseWorkId
     * @param int $courseChapterId
     * @param  int  $testQuestionId
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTestQuestionRequest $request, $courseWorkId, $courseChapterId, $testQuestionId)
    {
        $validated = $request->validated();
        $validated['course_work_id'] = $courseWorkId;
        $validated['course_chapter_id'] = $courseChapterId;
        $validated['tutor_id'] = Auth::user()->detail->id;
        $question = $this->service->updateQuestion($testQuestionId, $validated);

        return response()->json([
            'status' => 200,
            'message' => 'Test Question updated successfully',
            'data' => new TestQuestionResource($question)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $courseWorkId
     * @param int $courseChapterId
     * @param  int  $testQuestionId
     * 
     * @return \Illuminate\Http\Response
     */
    public function destroy($courseWorkId, $courseChapterId, $testQuestionId)
    {
        $this->service->deleteQuestion($courseChapterId, $testQuestionId, Auth::user()->detail->id);

        return response()->json([
            'status' => 200,
            'message' => 'Question deleted from Chapter Test successfully'
        ], 200);
    }
}
