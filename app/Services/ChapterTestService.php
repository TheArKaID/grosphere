<?php

namespace App\Services;

use App\Models\ChapterTest;

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

        return true;
    }
}
