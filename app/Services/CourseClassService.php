<?php

namespace App\Services;

use App\Models\Classes;
use App\Models\CourseClass;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CourseClassService
{
    private $courseClass, $classService;

    public function __construct(
        CourseClass $courseClass,
        ClassService $classService
    ) {
        $this->courseClass = $courseClass;
        $this->classService = $classService;
    }

    /**
     * Get all course classes
     * 
     * @param int $tutorId
     * 
     * @return Collection
     */
    public function getAllCourseClasses($tutorId = null)
    {
        if ($tutorId) {
            $this->courseClass = $this->courseClass->whereHas('class', function ($class) use ($tutorId) {
                $class->where('tutor_id', $tutorId);
            });
        }
        if (request()->has('search')) {
            $search = request()->get('search');
            $this->courseClass = $this->searchCourseClasses($search);
        }
        if (request()->has('subject')) {
            $subject = request()->get('subject');
            $this->courseClass = $this->courseClass->whereHas('courseSubject', function ($query) use ($subject) {
                $query->where('name', $subject);
            });
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->courseClass->get();
        }
        return $this->courseClass->paginate(request('size', 10));
    }

    /**
     * Cretae Course Class
     * 
     * @param array $data
     * 
     * @return CourseClass
     */
    public function createCourseClass(array $data)
    {
        DB::beginTransaction();

        $data['type'] = Classes::$COURSE;
        $class = $this->classService->createClass($data);
        $data['class_id'] = $class->id;
        $courseClass = $this->courseClass->create($data);

        DB::commit();

        return $courseClass;
    }

    /**
     * Get Course Class
     * 
     * @param int $id
     * @param int $tutorId
     * 
     * @return CourseClass
     */
    public function getCourseClassById($id, $tutorId = null)
    {
        if ($tutorId) {
            $this->courseClass = $this->courseClass->whereHas('class', function ($class) use ($tutorId) {
                $class->where('tutor_id', $tutorId);
            });
        }
        return $this->courseClass->findOrFail($id);
    }

    /**
     * Update Course Class
     * 
     * @param int $id
     * @param array $data
     * @param int $tutorId
     * 
     * @return CourseClass
     */
    public function updateCourseClass($id, array $data, $tutorId = null)
    {
        DB::beginTransaction();

        $courseClass = $this->getCourseClassById($id, $tutorId);
        $courseClass->update($data);

        $this->classService->updateClass($courseClass->class_id, $data);

        DB::commit();
        return $courseClass;
    }

    /**
     * Delete Course Class
     * 
     * @param int $id
     * @param int $tutorId
     * 
     * @return bool
     */
    public function deleteCourseClass($id, $tutorId = null)
    {
        DB::beginTransaction();

        $courseClass = $this->getCourseClassById($id, $tutorId);
        $courseClass->delete();
        $this->classService->deleteClass($courseClass->class_id);

        DB::commit();
        return true;
    }

    /**
     * Search in course classes
     * 
     * @param string $search
     * @return CourseClass
     */
    public function searchCourseClasses($search)
    {
        return $this->courseClass->whereHas('class', function ($class) use ($search) {
            $class->where('name', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')->whereHas('tutor', function ($tutor) use ($search) {
                    $tutor->where('name', 'like', '%' . $search . '%');
                });
        });
    }
}
