<?php

namespace App\Services;

use App\Models\Classes;
use App\Models\CourseWork;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseWorkService
{
    private $courseWork, $classService, $courseStudentService;

    public function __construct(
        CourseWork $courseWork,
        ClassService $classService,
        CourseStudentService $courseStudentService
    ) {
        $this->courseWork = $courseWork;
        $this->classService = $classService;
        $this->courseStudentService = $courseStudentService;
    }

    /**
     * Get all course works
     * 
     * @param int $tutorId
     * 
     * @return Collection
     */
    public function getAll($tutorId = null)
    {
        if ($tutorId) {
            $this->courseWork = $this->courseWork->whereHas('class', function ($class) use ($tutorId) {
                $class->where('tutor_id', $tutorId);
            });
        } elseif (Auth::user()->hasRole('student')) {
            $this->courseWork = $this->courseWork->where('level_id', Auth::user()->detail->levels()->id);
        }
        if (request()->has('search')) {
            $search = request()->get('search');
            $this->courseWork = $this->searchCourseWorks($search);
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->courseWork->get();
        }
        return $this->courseWork->paginate(request('size', 10));
    }

    /**
     * Cretae Course Work
     * 
     * @param array $data
     * 
     * @return CourseWork
     */
    public function createCourseWork(array $data)
    {
        DB::beginTransaction();

        $data['type'] = Classes::$COURSE;
        $class = $this->classService->createClass($data);
        $data['class_id'] = $class->id;
        $courseWork = $this->courseWork->create($data);

        DB::commit();

        return $courseWork;
    }

    /**
     * Get Course Work
     * 
     * @param int $id
     * @param int $tutorId
     * 
     * @return CourseWork
     */
    public function getCourseWorkById($id, $tutorId = null)
    {
        if ($tutorId) {
            $this->courseWork = $this->courseWork->whereHas('class', function ($class) use ($tutorId) {
                $class->where('tutor_id', $tutorId);
            });
        }
        return $this->courseWork->findOrFail($id);
    }

    /**
     * Get course work by level id and id
     * 
     * @param int $id
     * @param int $levelId
     * 
     * @return CourseWork
     */
    public function getCourseWorkByIdAndLevelId($id, $levelId)
    {
        return $this->courseWork->where('level_id', $levelId)->findOrFail($id);
    }

    /**
     * Update Course Work
     * 
     * @param int $id
     * @param array $data
     * @param int $tutorId
     * 
     * @return CourseWork
     */
    public function updateCourseWork($id, array $data, $tutorId = null)
    {
        DB::beginTransaction();

        $courseWork = $this->getCourseWorkById($id, $tutorId);
        $courseWork->update($data);

        $this->classService->updateClass($courseWork->class_id, $data);

        DB::commit();
        return $courseWork;
    }

    /**
     * Delete Course Work
     * 
     * @param int $id
     * @param int $tutorId
     * 
     * @return bool
     */
    public function deleteCourseWork($id, $tutorId = null)
    {
        DB::beginTransaction();

        $courseWork = $this->getCourseWorkById($id, $tutorId);
        $courseWork->delete();
        $this->classService->deleteClass($courseWork->class_id);

        DB::commit();
        return true;
    }

    /**
     * Search in course works
     * 
     * @param string $search
     * @return CourseWork
     */
    public function searchCourseWorks($search)
    {
        return $this->courseWork->whereHas('class', function ($class) use ($search) {
            $class->where('name', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')->whereHas('tutor', function ($tutor) use ($search) {
                    $tutor->where('name', 'like', '%' . $search . '%');
                });
        });
    }

    /**
     * Enroll Course Work
     * 
     * @param int $id
     * 
     * @return CourseWork
     */
    public function enroll($id)
    {
        $courseWork = $this->getCourseWorkByIdAndLevelId($id, Auth::user()->detail->levels()->id);
        $courseStudent = $this->courseStudentService->create([
            'course_work_id' => $courseWork->id,
            'student_id' => Auth::user()->detail->id
        ]);
        return $courseStudent;
    }
}
