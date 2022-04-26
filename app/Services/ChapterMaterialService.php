<?php

namespace App\Services;

use App\Models\ChapterMaterial;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChapterMaterialService
{
    private $model, $courseChapterService;

    public function __construct(
        ChapterMaterial $model,
        CourseChapterService $courseChapterService
    ) {
        $this->model = $model;
        $this->courseChapterService = $courseChapterService;
    }

    /**
     * Get all course Material
     * 
     * @param int $courseChapterId
     * @param int $tutorId
     * 
     * @return Collection
     */
    public function getAll($courseChapterId, $tutorId = null)
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
        $this->model = $this->model->where('course_chapter_id', $courseChapterId);
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->model->get();
        }
        return $this->model->paginate(request('size', 10));
    }

    /**
     * Get Chapter Material
     * 
     * @param int $courseChapterId
     * @param int $id
     * @param int $tutorId
     * @return ChapterMaterial
     */
    public function getById($courseChapterId, $id, $tutorId = null)
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
        return $this->model->where('course_chapter_id', $courseChapterId)->findOrFail($id);
    }

    /**
     * Create Chapter Material
     * 
     * @param array $data
     * 
     * @return ChapterMaterial
     */
    public function create(array $data)
    {
        $courseChapter = $this->courseChapterService->getById($data['course_work_id'], $data['course_chapter_id'], $data['tutor_id']);

        $fileName = $data['file']->getClientOriginalName();
        $fileExt = $data['file']->getClientOriginalExtension();

        $slug = Str::slug(pathinfo($fileName, PATHINFO_FILENAME));

        $savedFilename = time() . '-' . $slug . '.' . $fileExt;
        Storage::cloud()->putFileAs('course_works/' . $data['course_work_id'] . '/chapters/' . $data['course_chapter_id'] . '/materials', $data['file'], $savedFilename);

        return $this->model->create([
            'course_chapter_id' => $courseChapter->id,
            'shown_filename' => $fileName,
            'saved_filename' => $savedFilename,
            'ext' => $fileExt,
        ]);
    }

    /**
     * Delete Chapter Material
     * 
     * @param int $courseChapterId
     * @param int $id
     * @param int $tutorId
     * @return bool
     */
    public function delete($courseChapterId, $id, $tutorId = null)
    {
        $model = $this->getById($courseChapterId, $id, $tutorId);
        Storage::cloud()->delete('course_works/' . $model->courseChapter->courseWork->id . '/chapters/' . $model->courseChapter->id . '/materials/' . $model->saved_filename);
        $model->delete();

        return true;
    }
}
