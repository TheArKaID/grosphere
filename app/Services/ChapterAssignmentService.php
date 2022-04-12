<?php

namespace App\Services;

use App\Models\ChapterAssignment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChapterAssignmentService
{
    private $model, $courseChapterService;

    public function __construct(
        ChapterAssignment $model,
        CourseChapterService $courseChapterService
    ) {
        $this->model = $model;
        $this->courseChapterService = $courseChapterService;
    }

    /**
     * Get Chapter Assignment
     * 
     * @param int $courseChapterId
     * @param int $tutorId
     * @return ChapterAssignment
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
     * Create Chapter Assignment
     * 
     * @param array $data
     * 
     * @return ChapterAssignment
     */
    public function create(array $data)
    {
        $courseChapter = $this->courseChapterService->getById($data['course_work_id'], $data['course_chapter_id'], $data['tutor_id']);

        if ($courseChapter->chapterAssignments) {
            $courseChapter->chapterAssignments->update([
                'task' => $data['task'],
            ]);
            $courseAssignment = $courseChapter->chapterAssignments;
        } else {
            $courseAssignment = $this->model->create([
                'course_chapter_id' => $data['course_chapter_id'],
                'task' => $data['task'],
            ]);
        }

        return $courseAssignment;
    }

    /**
     * Upload Chapter Assignment File
     * 
     * @param mix $file
     * @param int $courseWorkId
     * @param int $courseChapterId
     * @param int $tutorId
     * 
     * 
     */
    public function uploadFile($file, $courseWorkId, $courseChapterId, $tutorId = null)
    {
        $fileName = $file->getClientOriginalName();
        $fileExt = $file->getClientOriginalExtension();

        $slug = Str::slug(pathinfo($fileName, PATHINFO_FILENAME));

        if (Str::wordCount($slug) > 255) {
            $slug = Str::limit($slug, 255, '');
        }
        $res = Storage::cloud()->putFileAs('course_works/' . $courseWorkId . '/chapters/' . $courseChapterId . '/assignments', $file, $slug . '.' . $fileExt);

        return $res ? Storage::cloud()->url($res) : $res;
    }

    /**
     * Delete Chapter Assignment
     * 
     * @param int $courseWorkId
     * @param int $courseChapterId
     * @param int $tutorId
     * @return bool
     */
    public function delete($courseWorkId, $courseChapterId, $tutorId = null)
    {
        $courseChapter = $this->courseChapterService->getById($courseWorkId, $courseChapterId, $tutorId);

        Storage::cloud()->deleteDirectory('course_works/' . $courseWorkId . '/chapters/' . $courseChapterId . '/assignments');
        $courseChapter->chapterAssignments->delete();

        return true;
    }

    /**
     * Delete Assignment File
     * 
     * @param int $courseWorkId
     * @param int $courseChapterId
     * @param string $fileName
     * @param int $tutorId
     * 
     * @return bool
     */
    public function deleteFile($courseWorkId, $courseChapterId, $fileName, $tutorId = null)
    {
        if (Storage::cloud()->exists('course_works/' . $courseWorkId . '/chapters/' . $courseChapterId . '/assignments/' . $fileName)) {
            Storage::cloud()->delete('course_works/' . $courseWorkId . '/chapters/' . $courseChapterId . '/assignments/' . $fileName);
            return true;
        }
        return false;
    }
}
