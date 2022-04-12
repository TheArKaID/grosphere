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
}
