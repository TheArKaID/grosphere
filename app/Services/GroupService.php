<?php

namespace App\Services;

use App\Models\Classes;
use App\Models\CourseStudent;
use App\Models\CourseWork;
use App\Models\Group;
use App\Models\GroupStudent;
use App\Models\LiveClass;
use App\Models\LiveClassStudent;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class GroupService
{
    private $group, $groupStudent, $liveClass, $liveClassStudent, $courseWork, $student, $courseStudentService;

    public function __construct(
        Group $group,
        GroupStudent $groupStudent,
        LiveClass $liveClass,
        Student $student,
        LiveClassStudent $liveClassStudent,
        CourseWork $courseWork,
        CourseStudent $courseStudent,
        CourseStudentService $courseStudentService
    ) {
        $this->group = $group;
        $this->groupStudent = $groupStudent;
        $this->liveClass = $liveClass;
        $this->student = $student;
        $this->liveClassStudent = $liveClassStudent;
        $this->courseWork = $courseWork;
        $this->courseStudent = $courseStudent;
        $this->courseStudentService = $courseStudentService;
    }

    /**
     * Get all Groups
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        if (request()->has('search')) {
            $search = request()->get('search');
            $this->group = $this->search($search);
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->group->get();
        }
        return $this->group->paginate(request('size', 10));
    }

    /**
     * Get One Group
     * 
     * @param int $id
     * 
     * @return Group
     */
    public function getOne($id)
    {
        return $this->group->findOrFail($id);
    }

    /**
     * Create Group
     * 
     * @param array $data
     * 
     * @return \App\Models\Group
     */
    public function create($data)
    {
        $group = $this->group->create($data);

        return $group;
    }

    /**
     * Update Group
     * 
     * @param int $id
     * @param array $data
     * 
     * @return Group
     */
    public function update($id, $data)
    {
        $group = $this->group->findOrFail($id);
        $group->update($data);

        return $group;
    }

    /**
     * Delete Group
     * 
     * @param int $id
     * 
     * @return \App\Models\Group
     */
    public function delete($id)
    {
        DB::beginTransaction();

        $group = $this->getOne($id);
        $group->delete();

        DB::commit();
        return true;
    }

    /**
     * Search in Group
     * 
     * @param string $search
     * @return mixed
     */
    public function search($search)
    {
        return $this->group->where('name', 'like', '%' . $search . '%');
    }

    /**
     * Add Student to Group by Creating Group Student
     * 
     * @param int $groupId
     * @param array $studentIds
     * 
     * @return \App\Models\Group
     */
    public function addStudent($groupId, $studentIds)
    {
        $group = $this->getOne($groupId);
        $group->students()->attach($studentIds);

        return $group;
    }

    /**
     * Remove Student from Group by Deleting Group Student
     * 
     * @param int $groupId
     * @param array $studentIds
     * 
     * @return \App\Models\Group
     */
    public function removeStudent($groupId, $studentIds)
    {
        $group = $this->getOne($groupId);
        $group->students()->detach($studentIds);

        return $group;
    }

    /**
     * Check if Group has access to Course
     * 
     * @param int $groupId
     * @param int $classId
     * 
     * @return bool
     */
    public function hasClassAccess($groupId, $classId)
    {
        $group = $this->getOne($groupId);
        $classes = $group->groupAccessClasses()->where('class_id', $classId)->get();

        return $classes->count() > 0;
    }

    /**
     * Get Live Classes by Group
     * 
     * @param int $groupId
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLiveClasses($groupId)
    {
        $group = $this->getOne($groupId);
        $classes = $group->classes()->where('type', Classes::$LIVE)->get();

        return $classes->map(function ($class) {
            return $class->liveClass;
        });
    }

    /**
     * Add Live Class Access to Group
     * 
     * @param int $groupId
     * @param int $liveClassId
     * 
     * @return \App\Models\Group
     */
    public function addLiveClassAccess($groupId, $liveClassId)
    {
        $group = $this->getOne($groupId);
        $liveClass = $this->liveClass->findOrFail($liveClassId);

        if ($this->hasClassAccess($group->id, $liveClass->class_id)) {
            return false;
        }
        DB::beginTransaction();
        $group->classes()->attach($liveClass->class_id);

        $group->students->each(function ($student) use ($liveClass) {
            $this->isStudentExist($student->id);
            $this->createLiveClassStudent($liveClass->id, $student->id, LiveClassStudent::$GROUP);
        });

        DB::commit();

        return $group;
    }

    /**
     * Remove Live Class Access from Group
     * 
     * @param int $groupId
     * @param int $liveClassId
     * 
     * @return \App\Models\Group
     */
    public function removeLiveClassAccess($groupId, $liveClassId)
    {
        $liveClass = $this->liveClass->findOrFail($liveClassId);
        $group = $this->getOne($groupId);

        if (!$this->hasClassAccess($group->id, $liveClass->class_id)) {
            return false;
        }
        DB::beginTransaction();
        $group->students->each(function ($student) use ($liveClass) {
            $student->liveClassStudents->each(function ($lcs) use ($liveClass) {
                if ($lcs->type == LiveClassStudent::$GROUP && $lcs->live_class_id == $liveClass->id) {
                    $lcs->delete();
                }
            });
        });
        $group->classes()->detach($liveClass->class_id);
        DB::commit();

        return true;
    }

    /**
     * Is Student Exist
     * 
     * @param int $studentId
     * 
     * @return bool
     */
    public function isStudentExist($studentId)
    {
        return $this->student->select('id')->findOrFail($studentId);
    }
    
    /**
     * Create LiveClassStudent
     * 
     * @param int $liveClassId
     * @param int $studentId
     * @param int $type
     * 
     * @return LiveClassStudent
     */
    public function createLiveClassStudent($liveClassId, $studentId, $type)
    {
        $liveClassStudent = $this->liveClassStudent->create([
            'live_class_id' => $liveClassId,
            'student_id' => $studentId,
            'type' => $type
        ]);

        return $liveClassStudent;
    }
    
    /**
     * Get Course Works by Group
     * 
     * @param int $groupId
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCourseWorks($groupId)
    {
        $group = $this->getOne($groupId);
        $courseWorks = $group->classes()->where('type', Classes::$COURSE)->get();

        return $courseWorks->map(function ($courseWork) {
            return $courseWork->courseWork;
        });
    }

    /**
     * Add Course Work Access to Group
     * 
     * @param int $groupId
     * @param int $courseWorkId
     * 
     * @return bool
     */
    public function addCourseWorkAccess($groupId, $courseWorkId)
    {
        $courseWork = $this->courseWork->findOrFail($courseWorkId);
        $group = $this->getOne($groupId);
        if ($this->hasClassAccess($group->id, $courseWork->class_id)) {
            return false;
        }

        DB::beginTransaction();
        $group->classes()->attach($courseWork->class_id);

        $group->students->each(function ($student) use ($courseWork) {
            $this->courseStudentService->create([
                'course_work_id' => $courseWork->id,
                'student_id' => $student->id,
                'type' => CourseStudent::$GROUP,
                'status' => 1
            ]);
        });
        DB::commit();

        return true;
    }

    /**
     * Remove Course Work Access from Group
     * 
     * @param int $groupId
     * @param int $courseWorkId
     * 
     * @return bool
     */
    public function removeCourseWorkAccess($groupId, $courseWorkId)
    {
        $courseWork = $this->courseWork->findOrFail($courseWorkId);
        $group = $this->getOne($groupId);

        if (!$this->hasClassAccess($group->id, $courseWork->class_id)) {
            return false;
        }

        DB::beginTransaction();
        $group->students->each(function ($student) use ($courseWork) {
            $student->courseStudents->each(function ($cs) use ($courseWork) {
                if ($cs->type == CourseStudent::$GROUP && $cs->course_work_id == $courseWork->id) {
                    $cs->delete();
                }
            });
        });
        $group->classes()->detach($courseWork->class_id);
        DB::commit();

        return true;
    }
}
