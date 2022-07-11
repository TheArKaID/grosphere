<?php

namespace App\Services;

use App\Models\ChapterTest;
use App\Models\CourseChapterStudent;
use App\Models\StudentTest;
use App\Models\StudentTestAnswer;
use App\Models\TestQuestion;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TakeChapterTestService
{
    private $testQuestion, $studentTest, $studentTestAnswer, $chapterTestService, $courseStudentService, $courseChapterStudentService;

    public function __construct(
        TestQuestion $testQuestion,
        StudentTest $studentTest,
        StudentTestAnswer $studentTestAnswer,
        ChapterTestService $chapterTestService,
        CourseStudentService $courseStudentService,
        CourseChapterStudentService $courseChapterStudentService
    ) {
        $this->testQuestion = $testQuestion;
        $this->studentTest = $studentTest;
        $this->studentTestAnswer = $studentTestAnswer;
        $this->chapterTestService = $chapterTestService;
        $this->courseStudentService = $courseStudentService;
        $this->courseChapterStudentService = $courseChapterStudentService;
    }

    /**
     * Get Test Summary
     * 
     * @param int $courseChapterId
     * @param int $studentId
     * 
     * @return StudentTest
     */
    public function getTestSummary($courseChapterId, $studentId)
    {
        $chapterTest = $this->chapterTestService->getOne($courseChapterId);
        $testSum = [
            "title" => $chapterTest->title,
            "duration" => $chapterTest->duration,
            "attempt" => $chapterTest->attempt,
            "available_at" => $chapterTest->available_at ? Carbon::parse($chapterTest->available_at)->toDateTimeString() : null,
            "available_until" => $chapterTest->available_until ? Carbon::parse($chapterTest->available_until)->toDateTimeString() : null,
            "total_question" => $chapterTest->testQuestions()->count(),
        ];

        $courseChapterStudent = $this->getCourseChapterStudent($courseChapterId, $studentId);
        if ($this->isTakenChapterTestActive($courseChapterStudent, $studentId)) {
            if ($chapterTest->type == $chapterTest::$ON_FILE) {
                $testSum["file"] = $chapterTest->getFile();
            } else {
                $testSum['questions'] = $chapterTest->testQuestions()->select('id', 'type')->get();
            }
        }

        return $testSum;
    }

    /**
     * Get Course Chapter Student
     * 
     * @param int $courseChapterId
     * @param int $studentId
     * 
     * @return CourseChapterStudent|null
     */
    public function getCourseChapterStudent($courseChapterId, $studentId)
    {
        $chapterTest = $this->chapterTestService->getOne($courseChapterId);

        $courseStudent = $this->courseStudentService->getByCourseWorkIdAndStudentId($chapterTest->courseChapter->course_work_id, $studentId);

        $courseChapterStudent = $this->courseChapterStudentService->getOne($courseChapterId, $courseStudent->id);

        return $courseChapterStudent;
    }

    /**
     * Enroll to a test.
     * 
     * @param int $courseChapterId
     * @param int $studentId
     * 
     * @return StudentTest|string
     */
    public function enrollToTest($courseChapterId, $studentId)
    {
        if ($status = $this->isChapterTestTimeOver($courseChapterId)) {
            return $status;
        }

        $courseChapterStudent = $this->getCourseChapterStudent($courseChapterId, $studentId);

        if ($this->isTakenChapterTestActive($courseChapterStudent, $studentId)) {
            return $courseChapterStudent->latestStudentTest;
        }

        if ($courseChapterStudent->studentTests()->count() >= $courseChapterStudent->courseChapter->chapterTest->attempt) {
            return "You have reached the maximum number of attempts";
        }

        return $this->studentTest->create([
            'course_chapter_student_id' => $courseChapterStudent->id,
            'status' => 1,
            'score' => 0,
        ]);
    }

    /**
     * Get Question by id
     * 
     * @param int $courseChapterId
     * @param int $studentId
     * @param int $questionId
     * 
     * @return TestQuestion|boolean
     */
    public function getQuestion($courseChapterId, $studentId, $questionId)
    {
        $courseChapterStudent = $this->getCourseChapterStudent($courseChapterId, $studentId);

        // if (!$courseChapterStudent->latestStudentTest) {
        //     return false;
        // }

        if ($this->isTakenChapterTestActive($courseChapterStudent, $studentId)) {
            $question = $this->testQuestion->findOrFail($questionId);
            $res = [
                'id' => $question->id,
                'chapter_test_id' => $question->chapter_test_id,
                'type' => $question->type,
                'question' => $question->question,
                'answers' => $question->type == TestQuestion::$MULTIPLE_CHOICE ? $question->testAnswers->map(function ($answer) {
                    return [
                        'answer' => $answer->answer,
                        'number' => $answer->number,
                    ];
                }) : null
            ];

            $studentAnswer = $this->getMyAnswer($courseChapterStudent->latestStudentTest->id, $questionId);

            $res['your_answer'] = $studentAnswer ?
                $studentAnswer->answer
                : null;
            if (auth()->user()->hasRole('tutor')) {
                $res['answer_number'] = $question->answer_number;
            }

            return $res;
        }
        return 'Cannot access Question. Make sure you\'ve enrolled to the test.';
    }

    /**
     * Get my answer to a question
     * 
     * @param int $studentTestId
     * @param int $questionId
     * 
     * @return StudentTestAnswer|boolean
     */
    public function getMyAnswer($studentTestId, $questionId)
    {
        return $this->studentTestAnswer->where('student_test_id', $studentTestId)->where('test_question_id', $questionId)->first();
    }

    /**
     * Answer a question by id
     * 
     * @param int $courseChapterId
     * @param int $studentId
     * @param int $questionId
     * @param string $answer
     * 
     * @return TestQuestion|boolean
     */
    public function answerQuestion($courseChapterId, $studentId, $questionId, $answer)
    {
        $courseChapterStudent = $this->getCourseChapterStudent($courseChapterId, $studentId);

        if ($this->isTakenChapterTestActive($courseChapterStudent, $studentId)) {
            $question = $this->testQuestion->findOrFail($questionId);
            $is_correct = null;
            if ($question->type == $this->testQuestion::$MULTIPLE_CHOICE) {
                $is_correct = $question->answer_number == $answer;
            }
            $ans = $this->studentTestAnswer->updateOrCreate([
                'test_question_id' => $questionId,
                'student_test_id' => $courseChapterStudent->latestStudentTest->id
            ], [
                'test_question_id' => $questionId,
                'student_test_id' => $courseChapterStudent->latestStudentTest->id,
                'answer' => $answer,
                'is_correct' => $is_correct
            ]);
            return $ans;
        }
        return 'Cannot access Question. Make sure you\'ve enrolled to the test.';
    }

    /**
     * Submit test
     * 
     * @param int $courseChapterId
     * @param int $studentId
     * @param $file
     * 
     * @return StudentTest|string
     */
    public function submitTest($courseChapterId, $studentId, $file)
    {
        $chapterTest = $this->chapterTestService->getOne($courseChapterId);
        // dd($courseChapterId);
        if ($chapterTest->type == $chapterTest::$ON_FILE) {
            return $this->uploadFile($courseChapterId, $studentId, $file);
        } else {
            $courseChapterStudent = $this->getCourseChapterStudent($courseChapterId, $studentId);

            if ($this->isTakenChapterTestActive($courseChapterStudent, $studentId)) {
                $test = $courseChapterStudent->latestStudentTest;
                $test->status = $test::$SUBMITTED;
                $test->score = $this->scoreTheTest($test);
                $test->save();
                return $test;
            }
        }

        return 'Cannot access Test. Make sure you\'ve enrolled to the test.';
    }

    /**
     * Is Taken Chapter Test Active
     * 
     * @param CourseChapterStudent $courseChapterStudent
     * @param int $studentId
     * 
     * @return boolean
     */
    public function isTakenChapterTestActive($courseChapterStudent, $studentId)
    {
        // $courseChapterStudent = $this->getCourseChapterStudent($courseChapterId, $studentId);

        if (!$courseChapterStudent->latestStudentTest) {
            return false;
        }

        if (Carbon::parse($courseChapterStudent->latestStudentTest->created_at)->addMinutes($courseChapterStudent->courseChapter->chapterTest->duration)->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check Chapter Test availability - Time.
     * 
     * @param int $courseChapterId
     * 
     * @return boolean
     */
    public function isChapterTestTimeOver($courseChapterId)
    {
        $chapterTest = $this->chapterTestService->getOne($courseChapterId);

        if ($chapterTest->available_at && Carbon::parse($chapterTest->available_at)->isFuture()) {
            return "Chapter Test is not started yet";
        }

        if ($chapterTest->available_until && Carbon::parse($chapterTest->available_until)->isPast()) {
            return "Chapter Test is ended";
        }

        return false;
    }

    /**
     * Score the test
     * 
     * @param StudentTest $test
     * 
     * @return float|string
     */
    public function scoreTheTest($test)
    {
        $score = 0;
        $answers = $test->studentTestAnswers;
        foreach ($answers as $answer) {
            if ($answer->is_correct) {
                // if ($answer->testQuestion->type == $this->testQuestion::$ESSAY) {
                //     return 'Failed. The Essay question is not scored.';
                // }
                $score += 1;
            }
        }
        return ($score / $test->courseChapterStudent->courseChapter->chapterTest->testQuestions()->count()) * 100;
    }

    /**
     * Upload file for ON_FILE type test
     * 
     * @param int $courseChapterId
     * @param int $studentId
     * @param $file
     * 
     * @return StudentTest|string
     */
    public function uploadFile($courseChapterId, $studentId, $file)
    {
        $courseChapterStudent = $this->getCourseChapterStudent($courseChapterId, $studentId);

        // if ($this->isTakenChapterTestActive($courseChapterStudent, $studentId)) {
        DB::beginTransaction();
        $test = $courseChapterStudent->latestStudentTest;
        $test->status = $test::$SUBMITTED;
        $test->score = $test->type == ChapterTest::$ON_APP ? $this->scoreTheTest($test) : 0;
        $test->save();
        $fileExt = $file->getClientOriginalExtension();

        Storage::cloud()->putFileAs('course_works/' . $courseChapterStudent->courseChapter->course_work_id . '/chapters/' . $courseChapterId . '/tests_answers', $file, $studentId . '.' . $fileExt);

        DB::commit();
        return $test;
        // }
        // return 'Cannot access Test. Make sure you\'ve enrolled to the test.';
    }

    /**
     * Get Student Test By ID
     * 
     * @param int $courseChapterId
     * @param int $studentTestId
     * @param int $tutorId
     * 
     * @return StudentTest|string
     */
    public function getStudentTestByID($courseChapterId, $studentTestId, $tutorId)
    {
        $this->chapterTestService->getOne($courseChapterId, $tutorId);

        return $this->studentTest->findOrFail($studentTestId);
    }

    /**
     * Score Student Answer by Student Test Answer ID
     * 
     * @param int $courseChapterId
     * @param int $studentTestId
     * @param int $studentTestAnswerId
     * @param int $isCorrect
     * 
     * @return StudentTestAnswer|string
     */
    public function scoreStudentAnswer($courseChapterId, $studentTestId, $studentTestAnswerId, $isCorrect)
    {
        $studentTestAnswer = $this->studentTestAnswer->whereHas('studentTest', function ($query) use ($courseChapterId) {
            $query->whereHas('courseChapterStudent', function ($query) use ($courseChapterId) {
                $query->where('course_chapter_id', '=', $courseChapterId);
            });
        })->findOrFail($studentTestAnswerId);

        $studentTestAnswer->is_correct = $isCorrect;
        $studentTestAnswer->save();
        $test = $this->studentTest->findOrFail($studentTestId);
        $test->score = $this->scoreTheTest($test);
        $test->save();
        return $studentTestAnswer;
    }
    
    /**
     * Score Student Test by Student Test ID
     * 
     * @param int $courseChapterId
     * @param int $studentTestId
     * @param int $tutorId
     * @param int $score
     * 
     * @return StudentTest|string
     */
    public function scoreStudentTest($courseChapterId, $studentTestId, $tutorId, $score)
    {
        $studentTest = $this->getStudentTestByID($courseChapterId, $studentTestId, $tutorId);

        $studentTest->score = $score;
        $studentTest->save();
        return $studentTest;
    }
}
