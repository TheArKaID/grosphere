<?php

namespace App\Services;

use App\Models\Classes;
use App\Models\CourseStudent;
use App\Models\CourseWork;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseWorkService
{
    private $courseWork, $classService, $courseStudentService, $courseChapterService;

    public function __construct(
        CourseWork $courseWork,
        ClassService $classService,
        CourseStudentService $courseStudentService,
        CourseChapterService $courseChapterService
    ) {
        $this->courseWork = $courseWork;
        $this->classService = $classService;
        $this->courseStudentService = $courseStudentService;
        $this->courseChapterService = $courseChapterService;
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
     * Get All Course Works and sort by newest AskAnswers relation
     * 
     * @param int $tutorId
     * 
     * @return Collection
     */
    public function getAllWithNewestAskAnswers($tutorId = null)
    {
        $this->courseWork = $this->courseWork->whereHas('class', function ($class) use ($tutorId) {
            $class->where('tutor_id', $tutorId);
        });

        $this->courseWork = $this->courseWork->with(['courseStudents' => function ($courseStudent) {
            $courseStudent->with(['askAnswers' => function ($askAnswers) {
                $askAnswers->orderBy('created_at', 'desc');
            }]);
        }]);

        return $this->courseWork->get();
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

        $this->courseChapterService->create([
            'course_work_id' => $courseWork->id,
            'tutor_id' => Auth::user()->detail->id,
            'title' => 'Chapter 1 - Introduction',
            'description' => 'Chapter Introduction',
            'content' => '{"blocks":[{"key":"b09di","text":"This is the introduction chapter of the course","type":"unstyled","depth":0,"inlineStyleRanges":[],"entityRanges":[],"data":{}}],"entityMap":{}}',
            'status' => 1,
            'order' => 0,
        ]);

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
        $courseWork = $this->getCourseWorkById($id);
        if ($this->courseStudentService->getByCourseWorkIdAndStudentId($courseWork->id, Auth::user()->detail->id, false)) {
            return false;
        }
        $courseStudent = $this->courseStudentService->create([
            'course_work_id' => $courseWork->id,
            'student_id' => Auth::user()->detail->id
        ]);
        return $courseStudent;
    }

    /**
     * Enroll Course Work by course work id and student id
     * 
     * @param int $courseWorkId
     * @param int $studentId
     * 
     * @return CourseWork
     */
    public function enrollByCourseWorkIdAndStudentId($courseWorkId, $studentId)
    {
        $courseWork = $this->getCourseWorkById($courseWorkId);
        if ($this->courseStudentService->getByCourseWorkIdAndStudentId($courseWork->id, $studentId, false)) {
            return false;
        }
        $courseStudent = $this->courseStudentService->create([
            'course_work_id' => $courseWork->id,
            'student_id' => $studentId,
            'type' => CourseStudent::$PERSONAL
        ]);
        return $courseStudent;
    }
}
