<?php

namespace App\Services;

use App\Models\ChapterTest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChapterTestService
{
    private $model, $courseChapterService;

    public function __construct(
        ChapterTest $model,
        CourseChapterService $courseChapterService
    ) {
        $this->model = $model;
        $this->courseChapterService = $courseChapterService;
    }

    /**
     * Get Chapter Test
     * 
     * @param int $courseChapterId
     * @param int $tutorId
     * @return ChapterTest
     */
    public function getOne($courseChapterId, $tutorId = null)
    {
        if ($tutorId) {
            $this->model = $this->model->whereHas('courseChapter', function ($courseChapter) use ($tutorId) {
                $courseChapter->whereHas('courseWork', function ($courseWork) use ($tutorId) {
                    $courseWork->whereHas('class', function ($class) use ($tutorId) {
                        $class->where('tutor_id', $tutorId);
                    });
                });
            });
        }
        return $this->model->where('course_chapter_id', $courseChapterId)->firstOrFail();
    }

    /**
     * Create Chapter Test
     * 
     * @param array $data
     * 
     * @return ChapterTest
     */
    public function create(array $data)
    {
        $chapter = $this->courseChapterService->getById($data['course_work_id'], $data['course_chapter_id'], $data['tutor_id']);

        if ($data['type'] == ChapterTest::$ON_FILE) {
            $data['status'] = 1;

            if (isset($data['file'])) {
                $fileName = $data['file']->getClientOriginalName();
                $fileExt = $data['file']->getClientOriginalExtension();
    
                $slug = Str::slug(pathinfo($fileName, PATHINFO_FILENAME));
    
                if (Str::wordCount($slug) > 255) {
                    $slug = Str::limit($slug, 255, '');
                }
                $test = $this->model->create($data);
                Storage::cloud()->putFileAs('course_works/' . $data['course_work_id'] . '/chapters/' . $data['course_chapter_id'] . '/tests', $data['file'], $slug . '.' . $fileExt);
            }
        }
        if ($chapter->chapterTest) {
            $chapter->chapterTest->update($data);
            $test = $chapter->chapterTest;
        } else {
            $data['status'] = 1;
            $test = $this->model->create($data);
        }

        return $test;
    }

    /**
     * Delete Chapter Test
     * 
     * @param int $courseChapterId
     * @param int $tutorId
     * @return bool
     */
    public function delete($courseChapterId, $tutorId = null)
    {
        $model = $this->getOne($courseChapterId, $tutorId);
        $model->delete();
        Storage::cloud()->deleteDirectory('course_works/' . $model->courseChapter->courseWork->id . '/chapters/' . $model->courseChapter->id . '/tests');
        return true;
    }

    /**
     * Get Chapter Test Results
     * 
     * @param int $courseChapterId
     * @param int $tutorId
     * 
     * @return Collection
     */
    public function getResults($courseChapterId, $tutorId = null)
    {
        $chapterTest = $this->getOne($courseChapterId, $tutorId);

        $courseChapterStudents = (object) $chapterTest->courseChapter->courseChapterStudents;

        $results = [];
        foreach ($courseChapterStudents as $ccs) {
            if ($chapterTest->type == ChapterTest::$ON_FILE) {
                $test = $ccs->latestStudentTest;
                $resulkt = [
                    'student_name' => $ccs->courseStudent->student->user->name,
                    'student_test_id' => $test->id,
                    'status' => $test->getStatus(),
                    'score' => $test->getScore(),
                    'file' => $test->getAnswerFilePath()
                ];
            } else {
                $resulkt = $ccs->studentTests->map(function ($studentTest) {
                    return [
                        'student_name' => $studentTest->courseChapterStudent->courseStudent->student->user->name,
                        'student_test_id' => $studentTest->id,
                        'status' => $studentTest->getStatus(),
                        'score' => $studentTest->score,
                        'student_answers' => $studentTest->studentTestAnswers,
                        'questions' => $studentTest->courseChapterStudent->courseChapter->chapterTest->testQuestions()->with('testAnswers')->get()
                    ];
                });
            }
            array_push($results, $resulkt);
        }

        return $results;
    }
}
