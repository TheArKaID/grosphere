<?php

namespace App\Services;

use App\Models\CourseChapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseChapterService
{
    private $courseChapter;

    public function __construct(
        CourseChapter $courseChapter
    ) {
        $this->courseChapter = $courseChapter;
    }

    /**
     * Get all course Chapters
     * 
     * @param int $courseWorkId
     * @param int $tutorId
     * 
     * @return Collection
     */
    public function getAll($courseWorkId, $tutorId = null)
    {
        if ($tutorId) {
            $this->courseChapter = $this->courseChapter->whereHas('courseWork', function ($courseWork) use ($tutorId) {
                $courseWork->whereHas('class', function ($class) use ($tutorId) {
                    $class->where('tutor_id', $tutorId);
                });
            });
        }
        $this->courseChapter = $this->courseChapter->where('course_work_id', $courseWorkId);
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->courseChapter->get();
        }
        return $this->courseChapter->paginate(request('size', 10));
    }

    /**
     * Get Course Chapter
     * 
     * @param int $courseWorkId
     * @param int $tutorId
     * @return CourseChapter
     */
    public function getLast($courseWorkId, $tutorId)
    {
        if ($tutorId) {
            $this->courseChapter = $this->courseChapter->whereHas('courseWork', function ($courseWork) use ($tutorId) {
                $courseWork->whereHas('class', function ($class) use ($tutorId) {
                    $class->where('tutor_id', $tutorId);
                });
            });
        }
        return $this->courseChapter->where('course_work_id', $courseWorkId)->latest()->first();
    }

    /**
     * Create Course Chapter
     * 
     * @param array $data
     * 
     * @return CourseChapter
     */
    public function create(array $data)
    {
        $currentOrder = $this->getLast($data['course_work_id'], $data['tutor_id']);
        $data['order'] = $currentOrder ? $currentOrder->order + 1 : 0;
        $data['status'] = 1;
        $courseChapter = $this->courseChapter->create($data);

        return $courseChapter;
    }

    /**
     * Get Course Chapter
     * 
     * @param int $courseWorkId
     * @param int $id
     * @param int $tutorId
     * @return CourseChapter
     */
    public function getById($courseWorkId, $id, $tutorId = null)
    {
        if ($tutorId) {
            $this->courseChapter = $this->courseChapter->whereHas('courseWork', function ($courseWork) use ($tutorId) {
                $courseWork->whereHas('class', function ($class) use ($tutorId) {
                    $class->where('tutor_id', $tutorId);
                });
            });
        }
        return $this->courseChapter->where('course_work_id', $courseWorkId)->findOrFail($id);
    }

    /**
     * Update Course Chapter
     * 
     * @param int $courseWorkId
     * @param int $id
     * @param array $data
     * @param int $tutorId
     * @return CourseChapter
     */
    public function update($courseWorkId, $id, array $data, $tutorId = null)
    {
        $courseChapter = $this->getById($courseWorkId, $id, $tutorId);
        $courseChapter->update($data);

        return $courseChapter;
    }

    /**
     * Delete Course Chapter
     * 
     * @param int $courseWorkId
     * @param int $id
     * @param int $tutorId
     * @return bool
     */
    public function delete($courseWorkId, $id, $tutorId = null)
    {
        $courseChapter = $this->getById($courseWorkId, $id, $tutorId);
        if ($courseChapter->order == 0) {
            return 'Failed. First Chapter cannot be deleted. Edit it, or Delete the Course Work instead.';
        }
        $courseChapter->delete();

        return true;
    }

    /**
     * Upload Course Chapter Image
     * 
     * @param file $image
     * @param int $courseWorkId
     * 
     * @return string
     */
    public function uploadImage($image, $courseWorkId)
    {
        $imageName = $image->getClientOriginalName();
        $imageExt = $image->getClientOriginalExtension();

        $slug = time() . '-' .Str::slug(pathinfo($imageName, PATHINFO_FILENAME));

        if (Str::wordCount($slug) > 255) {
            $slug = Str::limit($slug, 225, '');
        }
        $res = Storage::cloud()->putFileAs('course_works/' . $courseWorkId . '/chapters/contents', $image, $slug . '.' . $imageExt);

        return $res ? Storage::cloud()->url($res) : $res;
    }

    /**
     * Delete Course Chapter Image
     * 
     * @param string $imageName
     * @param int $courseWorkId
     * 
     * @return bool
     */
    public function deleteImage($imageName, $courseWorkId)
    {
        $res = Storage::cloud()->delete('course_works/' . $courseWorkId . '/chapters/contents/' . $imageName);

        return $res ? true : false;
    }
}
