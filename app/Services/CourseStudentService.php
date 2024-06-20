<?php

namespace App\Services;

use App\Models\CourseStudent;
use Illuminate\Support\Facades\Auth;

class CourseStudentService
{
    private $courseStudent;

    public function __construct(
        CourseStudent $courseStudent
    ) {
        $this->courseStudent = $courseStudent;
    }

    /**
     * Get all course students
     * 
     * @return Collection
     */
    public function getAll()
    {
        $this->courseStudent = $this->courseStudent->where('student_id', Auth::user()->detail->id);

        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->courseStudent->get();
        }
        return $this->courseStudent->paginate(request('size', 10));
    }

    /**
     * Create Course Student
     * 
     * @param array $data
     * 
     * @return CourseStudent
     */
    public function create(array $data)
    {
        $courseStudent = $this->courseStudent->create($data);

        return $courseStudent;
    }

    /**
     * Get Course Student
     * 
     * @param string $id
     * @return CourseStudent
     */
    public function getById($id)
    {
        return $this->courseStudent->findOrFail($id);
    }

    /**
     * Get by id and tutor id
     * 
     * @param string $id
     * @param string $tutorId
     * 
     * @return CourseStudent
     */
    public function getByIdAndTutorId($id, $tutorId)
    {
        return $this->courseStudent->where('id', $id)->whereHas('courseWork', function ($courseWork) use ($tutorId) {
            $courseWork->whereHas('class', function ($class) use ($tutorId) {
                $class->where('tutor_id', $tutorId);
            });
        })->firstOrFail();
    }

    /**
     * Get by id and Student id
     * 
     * @param string $id
     * @param string $studentId
     * 
     * @return CourseStudent
     */
    public function getByIdAndStudentId($id, $studentId)
    {
        return $this->courseStudent->where('id', $id)->where('student_id', $studentId)->firstOrFail();
    }

    /**
     * Get Course Student by course work id and student id
     * 
     * @param string $courseWorkId
     * @param string $studentId
     * @param bool $throwException
     * 
     * @return CourseStudent
     */
    public function getByCourseWorkIdAndStudentId($courseWorkId, $studentId, $throwException = true)
    {
        $this->courseStudent = $this->courseStudent->where('course_work_id', $courseWorkId)->where('student_id', $studentId);

        if ($throwException) {
            return $this->courseStudent->firstOrFail();
        } else {
            return $this->courseStudent->first();
        }
    }

    /**
     * Get All Course Student by course work id and student id
     * 
     * @param string $courseWorkId
     * @param string $studentId
     * 
     * @return CourseStudent
     */
    public function getAllByCourseWorkIdAndStudentId($courseWorkId, $studentId)
    {
        return $this->courseStudent->where('course_work_id', $courseWorkId)->where('student_id', $studentId)->get();
    }

    /**
     * Update Course Student
     * 
     * @param string $id
     * @param array $data
     * @return CourseStudent
     */
    public function update($id, array $data)
    {
        $courseStudent = $this->getById($id);
        $courseStudent->update($data);

        return $courseStudent;
    }

    /**
     * Delete Course Student
     * 
     * @param string $id
     * @return bool
     */
    public function delete($id)
    {
        $courseStudent = $this->getById($id);
        $courseStudent->delete();

        return true;
    }
}
