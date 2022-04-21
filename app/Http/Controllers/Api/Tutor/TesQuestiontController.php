<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestQuestionRequest;
use App\Http\Requests\UpdateTestQuestionRequest;
use App\Http\Resources\TestQuestionResource;
use App\Models\TestQuestion;
use App\Services\TestQuestionService;
use Illuminate\Support\Facades\Auth;

class TesQuestiontController extends Controller
{
    private $service;

    public function __construct(TestQuestionService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questions = TestQuestionResource::collection($this->service->getAll());

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
     * @param  \App\Models\TestQuestion  $testQuestion
     * @return \Illuminate\Http\Response
     */
    public function show(TestQuestion $testQuestion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTestQuestionRequest  $request
     * @param  \App\Models\TestQuestion  $testQuestion
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTestQuestionRequest $request, TestQuestion $testQuestion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TestQuestion  $testQuestion
     * @return \Illuminate\Http\Response
     */
    public function destroy(TestQuestion $testQuestion)
    {
        //
    }
}
