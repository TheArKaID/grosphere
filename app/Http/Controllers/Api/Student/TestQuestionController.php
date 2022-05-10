<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\TakeChapterTestResource;
use Illuminate\Http\Request;
use App\Services\TakeChapterTestService;
use Illuminate\Support\Facades\Auth;

class TestQuestionController extends Controller
{
    private $takeChapterTestService;

    public function __construct(TakeChapterTestService $takeChapterTestService)
    {
        $this->takeChapterTestService = $takeChapterTestService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $courseWorkId
     * @param int $ccourseChapterId
     * 
     * @return \Illuminate\Http\Response
     */
    public function index($courseWorkId, $courseChapterId)
    {
        $testSum = $this->takeChapterTestService->getTestSummary($courseChapterId, Auth::user()->detail->id);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $testSum
        ]);
    }

    /**
     * Enroll to a test.
     * 
     * @param  Request  $request
     * @param int $courseWorkId
     * @param int $ccourseChapterId
     *  
     * @return \Illuminate\Http\Response
     */
    public function enroll(Request $request, $courseWorkId, $courseChapterId)
    {
        $result = $this->takeChapterTestService->enrollToTest($courseChapterId, Auth::user()->detail->id);

        if (gettype($result) == 'string') {
            return response()->json([
                'status' => 400,
                'message' => $result,
            ]);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Test enrolled successfully.',
            'data' => new TakeChapterTestResource($result)
        ]);
    }

    /**
     * Get question.
     * 
     * @param  Request  $request
     * @param int $courseWorkId
     * @param int $ccourseChapterId
     * @param int $questionId
     * 
     * @return \Illuminate\Http\Response
     */
    public function getQuestion(Request $request, $courseWorkId, $courseChapterId, $questionId)
    {
        $question = $this->takeChapterTestService->getQuestion($courseChapterId, Auth::user()->detail->id, $questionId);

        if (gettype($question) == 'string') {
            return response()->json([
                'status' => 400,
                'message' => $question,
            ]);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $question
        ]);
    }

    /**
     * Answer a question.
     * 
     * @param  Request  $request
     * @param int $courseWorkId
     * @param int $ccourseChapterId
     * @param int $questionId
     * 
     * @return \Illuminate\Http\Response
     */
    public function answerQuestion(Request $request, $courseWorkId, $courseChapterId, $questionId)
    {
        $result = $this->takeChapterTestService->answerQuestion($courseChapterId, Auth::user()->detail->id, $questionId, $request->answer);

        if (gettype($result) == 'string') {
            return response()->json([
                'status' => 400,
                'message' => $result,
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            // 'data' => $result
        ]);
    }
}
