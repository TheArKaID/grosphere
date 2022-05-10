<?php

namespace App\Services;

use App\Models\ChapterTest;
use App\Models\CourseChapterStudent;
use App\Models\StudentTest;
use App\Models\TestQuestion;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TakeChapterTestService
{
    private $testQuestion, $studentTest, $chapterTestService, $courseStudentService, $courseChapterStudentService;

    public function __construct(
        TestQuestion $testQuestion,
        StudentTest $studentTest,
        ChapterTestService $chapterTestService,
        CourseStudentService $courseStudentService,
        CourseChapterStudentService $courseChapterStudentService
    ) {
        $this->testQuestion = $testQuestion;
        $this->studentTest = $studentTest;
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
            "available_at" => Carbon::parse($chapterTest->available_at)->toDateTimeString(),
            "available_until" => Carbon::parse($chapterTest->available_until)->toDateTimeString(),
            "total_question" => $chapterTest->testQuestions()->count(),
        ];

        $courseChapterStudent = $this->getCourseChapterStudent($courseChapterId, $studentId);
        if ($this->isTakenChapterTestActive($courseChapterStudent, $studentId)) {
            $testSum['questions'] = $chapterTest->testQuestions()->select('id', 'type')->get();
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

        $courseStudent = $this->courseStudentService->getByCourseIdAndStudentId($chapterTest->courseChapter->course_work_id, $studentId);

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
            return $this->testQuestion->findOrFail($questionId);
        }
        return 'Cannot access Question. Make sure you\'ve enrolled to the test.';
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

        if (Carbon::parse($chapterTest->available_at)->isFuture()) {
            return "Chapter Test is not started yet";
        }

        if (Carbon::parse($chapterTest->available_until)->isPast()) {
            return "Chapter Test is ended";
        }

        return false;
    }
}
