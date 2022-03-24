<?php

namespace App\Services;

use App\Models\CourseStudent;

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
     * @param int $id
     * @return CourseStudent
     */
    public function getById($id)
    {
        return $this->courseStudent->findOrFail($id);
    }

    /**
     * Update Course Student
     * 
     * @param int $id
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
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $courseStudent = $this->getById($id);
        $courseStudent->delete();

        return true;
    }
}
