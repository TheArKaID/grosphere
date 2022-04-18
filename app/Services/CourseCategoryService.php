<?php

namespace App\Services;

use App\Models\CourseCategory;
use Error;

class CourseCategoryService
{
    private $courseCategory;

    public function __construct(
        CourseCategory $courseCategory
    ) {
        $this->courseCategory = $courseCategory;
    }

    /**
     * Get all course Chapters
     * 
     * @return Collection
     */
    public function getAll()
    {
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->courseCategory->get();
        }
        return $this->courseCategory->paginate(request('size', 10));
    }

    /**
     * Get Course Category
     * 
     * @param int $id
     * 
     * @return CourseCategory
     */
    public function getById($id)
    {
        return $this->courseCategory->findOrFail($id);
    }

    /**
     * Create Course Category
     * 
     * @param array $data
     * 
     * @return CourseCategory
     */
    public function create($data)
    {
        return $this->courseCategory->create($data);
    }

    /**
     * Update Course Category
     * 
     * @param int $id
     * @param array $data
     * 
     * @return CourseCategory
     */
    public function update($id, $data)
    {
        $courseCategory = $this->getById($id);
        $courseCategory->update($data);

        return $courseCategory;
    }

    /**
     * Delete Course Category
     * 
     * @param int $id
     * 
     * @return CourseCategory
     */
    public function delete($id)
    {
        $courseCategory = $this->getById($id);
        $courseCategory->delete();

        return $courseCategory;
    }
}
