<?php

namespace App\Services;

use App\Models\CourseChapter;
use App\Models\CourseChapterStudent;
use Illuminate\Support\Facades\Auth;

class CourseChapterStudentService
{
    private $courseChapterStudent, $courseChapter;

    public function __construct(
        CourseChapterStudent $courseChapterStudent,
        CourseChapter $courseChapter
    ) {
        $this->courseChapterStudent = $courseChapterStudent;
        $this->courseChapter = $courseChapter;
    }

    /**
     * Get all course Chapters
     * 
     * @param int $courseWorkId
     * 
     * @return Collection
     */
    public function getAll($courseWorkId)
    {
        return $this->courseChapter
            ->select('id', 'course_work_id', 'title', 'order')
            ->with(['courseChapterStudents'])
            ->where('course_work_id', $courseWorkId)
            ->whereHas('courseWork', function ($query) {
                $query->where('level_id', Auth::user()->detail->levels()->id);
            })
            ->orderBy('order')
            ->get();
    }

    /**
     * Create Course Chapter
     * 
     * @param array $data
     * 
     * @return CourseChapterStudent
     */
    public function create(array $data)
    {
        return $this->courseChapterStudent->create($data);
    }

    /**
     * Get Course Chapter
     * 
     * @param int $courseWorkId
     * @param int $id
     * @return CourseChapter|string
     */
    public function getById($courseWorkId, $id)
    {
        $chapter = $this->courseChapter
            ->with(['courseChapterStudents', 'courseWork.courseStudents'])
            ->where('course_work_id', $courseWorkId)
            ->whereHas('courseWork', function ($courseWork) {
                $courseWork->whereHas('courseStudents', function ($courseStudents) {
                    $courseStudents->where('student_id', Auth::user()->detail->id);
                })->where('level_id', Auth::user()->detail->levels()->id);
            })
            ->findOrFail($id);

        // Check if this chapter has been read
        if ($chapter->courseChapterStudents()->count() == 0) {
            // Not Yet
            // Is the order is 0/first ?
            if ($chapter->order == 0) {
                // Yes, create new record
                $this->create([
                    'course_chapter_id' => $chapter->id,
                    'course_student_id' => $chapter->courseWork->courseStudents[0]->id,
                    'status' => 1,
                ]);
            } else {
                // No, Check previous chapter status
                $previousChapter = $this->courseChapter
                    ->whereHas('courseChapterStudents', function ($courseChapterStudents) use ($chapter) {
                        $courseChapterStudents->where('course_student_id', $chapter->courseWork->courseStudents[0]->id);
                    })->where('course_work_id', $courseWorkId)
                    ->where('order', $chapter->order - 1)
                    ->first();

                // If previous chapter is not read, return error
                if (!$previousChapter) {
                    return 'Please read previous chapter first';
                } else {
                    // Otherwise, create new record
                    $this->create([
                        'course_chapter_id' => $chapter->id,
                        'course_student_id' => $chapter->courseWork->courseStudents[0]->id,
                        'status' => 1,
                    ]);
                }
            }
        }

        return $chapter;
    }
}
