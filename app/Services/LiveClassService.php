<?php

namespace App\Services;

use App\Models\Classes;
use App\Models\CourseStudent;
use App\Models\LiveClass;
use App\Models\LiveClassSetting;
use App\Models\LiveClassStudent;
use App\Models\Student;
use App\Models\Tutor;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LiveClassService
{
    private $liveClass, $tutor, $student, $classService, $liveClassSetting, $liveClassStudent, $groupService;

    public function __construct(
        LiveClass $liveClass,
        Tutor $tutor,
        Student $student,
        ClassService $classService,
        LiveClassSetting $liveClassSetting,
        LiveClassStudent $liveClassStudent,
        GroupService $groupService
    ) {
        $this->liveClass = $liveClass;
        $this->tutor = $tutor;
        $this->student = $student;
        $this->classService = $classService;
        $this->liveClassSetting = $liveClassSetting;
        $this->liveClassStudent = $liveClassStudent;
        $this->groupService = $groupService;
    }

    /**
     * Get all live classes
     * 
     * @return Collection
     */
    public function getAllLiveClasses()
    {
        if (request()->has('search')) {
            $search = request()->get('search');
            $this->liveClass = $this->searchLiveClasses($search);
        }
        if (request()->has('range')) {
            $range = request()->get('range');
            switch ($range) {
                case 'today':
                    $this->liveClass = $this->liveClass->whereDate('start_time', Carbon::today());
                    break;
                case 'coming-soon':
                    $this->liveClass = $this->liveClass->whereDate('start_time', '>', [Carbon::today()]);
                    break;
                default:
                    break;
            }
        }
        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->liveClass->get();
        }
        return $this->liveClass->paginate(request('size', 10));
    }

    /**
     * Cretae Live Class
     * 
     * @param array $data
     * 
     * @return LiveClass
     */
    public function createLiveClass(array $data)
    {
        DB::beginTransaction();

        $data['type'] = Classes::$LIVE;
        $data['agency_id'] = auth()->user()->agency_id;
        $class = $this->classService->createClass($data);
        $data['class_id'] = $class->id;
        $liveClass = $this->liveClass->create($data);
        $this->liveClassSetting->create([
            'live_class_id' => $liveClass->id,
            'mic_on' => $data['mic_on'],
            'cam_on' => $data['cam_on']
        ]);

        DB::commit();

        return $liveClass;
    }

    /**
     * Get Live Class
     * 
     * @param int $id
     * 
     * @return LiveClass
     */
    public function getLiveClassById($id)
    {
        return $this->liveClass->findOrFail($id);
    }

    /**
     * Get Live Class by id without global scope
     * 
     * @param int $id
     * 
     * @return LiveClass
     */
    public function getLiveClassByIdWithoutGlobalScope($id)
    {
        return $this->liveClass->withoutGlobalScope('agency')->findOrFail($id);
    }

    /**
     * Update Live Class
     * 
     * @param int $id
     * @param array $data
     * 
     * @return LiveClass
     */
    public function updateLiveClass($id, array $data)
    {
        DB::beginTransaction();

        $liveClass = $this->getLiveClassById($id);
        $liveClass->update($data);
        $liveClass->setting->update([
            'mic_on' => $data['mic_on'],
            'cam_on' => $data['cam_on']
        ]);
        $this->classService->updateClass($liveClass->class_id, $data);

        DB::commit();
        return $liveClass;
    }

    /**
     * Delete Live Class
     * 
     * @param int $id
     * 
     * @return bool
     */
    public function deleteLiveClass($id)
    {
        DB::beginTransaction();

        $liveClass = $this->getLiveClassById($id);
        $liveClass->delete();
        $this->classService->deleteClass($liveClass->class_id);

        DB::commit();
        return true;
    }

    /**
     * Search in live classes
     * 
     * @param string $search
     * @return LiveClass
     */
    public function searchLiveClasses($search)
    {
        return $this->liveClass->whereHas('class', function ($class) use ($search) {
            $class->where('name', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')->whereHas('tutor', function ($tutor) use ($search) {
                    $tutor->where('name', 'like', '%' . $search . '%');
                });
        });
    }

    /**
     * Get All Current Tutor Live Classes
     * 
     * @return Collection
     */
    public function getAllCurrentTutorLiveClasses()
    {
        $tutorId = auth()->user()->detail->id;

        $this->liveClass = $this->liveClass->whereHas('class', function ($class) use ($tutorId) {
            $class->where('tutor_id', $tutorId);
        });

        if (request()->has('search')) {
            $search = request()->get('search');
            $this->liveClass = $this->searchLiveClasses($search);
        }

        if (request()->has('range')) {
            $range = request()->get('range');
            switch ($range) {
                case 'past':
                    $this->liveClass = $this->liveClass->whereDate('start_time', '<', [Carbon::today()]);
                    break;
                case 'today':
                    $this->liveClass = $this->liveClass->whereDate('start_time', Carbon::today());
                    break;
                case 'coming-soon':
                    $this->liveClass = $this->liveClass->whereDate('start_time', '>', [Carbon::today()]);
                    break;
                default:
                    break;
            }
        }

        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->liveClass->get();
        }

        return $this->liveClass->paginate(request('size', 10));
    }

    /**
     * Get Current Tutor Live Class
     * 
     * @param int $id
     * 
     * @return LiveClass
     */
    public function getCurrentTutorLiveClass($id)
    {
        $tutorId = auth()->user()->detail->id;

        return $this->liveClass->whereHas('class', function ($class) use ($tutorId) {
            $class->where('tutor_id', $tutorId);
        })->findOrFail($id);
    }

    /**
     * Update Current Tutor Live Class
     * 
     * @param int $id
     * @param array $data
     * 
     * @return LiveClass
     */
    public function updateCurrentTutorLiveClass($id, array $data)
    {
        DB::beginTransaction();

        $liveClass = $this->getCurrentTutorLiveClass($id);
        $liveClass->update($data);

        $this->classService->updateClass($liveClass->class_id, $data);

        DB::commit();
        return $liveClass;
    }

    /**
     * Delete Current Tutor Live Class
     * 
     * @param int $id
     * 
     * @return bool
     */
    public function deleteCurrentTutorLiveClass($id)
    {
        DB::beginTransaction();

        $liveClass = $this->getCurrentTutorLiveClass($id);
        $liveClass->delete();
        $this->classService->deleteClass($liveClass->class_id);

        DB::commit();
        return true;
    }

    /**
     * Check if Live Class is started
     * 
     * @param int $liveClassId
     * 
     * @return bool|string
     */
    public function isLiveClassStarted($liveClassId)
    {
        $liveClass = $this->getLiveClassByIdWithoutGlobalScope($liveClassId);
        $liveClassStartTime = Carbon::parse($liveClass->start_time);
        $liveClassEndTime = Carbon::parse($liveClass->start_time)->addMinutes($liveClass->duration);
        $currentTime = Carbon::now();

        return $currentTime->lt($liveClassStartTime) ? 'Live Class Not Started' : 
            ($currentTime->gt($liveClassEndTime) ? 'Live Class Ended' : true);
        return $currentTime->between($liveClassStartTime, $liveClassEndTime);
    }

    /**
     * Is Tutor Live Class not ended
     * 
     * @param int $liveClassId
     * 
     * @return bool
     */
    public function isTutorLiveClassNotEnded($liveClassId)
    {
        $liveClass = $this->getCurrentTutorLiveClass($liveClassId);
        $liveClassEndTime = Carbon::parse($liveClass->start_time)->addMinutes($liveClass->duration);
        $currentTime = Carbon::now();

        return $currentTime->lt($liveClassEndTime) ? true : 'Live Class has ended';
    }

    /**
     * Is Tutor Live Class not Started
     * 
     * @param int $liveClassId
     * 
     * @return bool
     */
    public function isTutorLiveClassNotStarted($liveClassId)
    {
        $liveClass = $this->getCurrentTutorLiveClass($liveClassId);
        $liveClassStartTime = Carbon::parse($liveClass->start_time);
        $currentTime = Carbon::now();

        return $currentTime->lt($liveClassStartTime) ? 'Live Class has not started' : false;
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
     * Enroll Live Class by live class id and student id
     * 
     * @param int $liveClassId
     * @param int $studentId
     * 
     * @return LiveClassStudent|String
     */
    public function enrollByLiveClassIdAndStudentId($liveClassId, $studentId)
    {
        $this->isStudentExist($studentId);
        if ($this->getLiveClassStudentByLiveClassIdAndStudentId($liveClassId, $studentId)) {
            return 'Student already enrolled to this Live CLass. Cannot re-enroll.';
        }
        $courseStudent = $this->createLiveClassStudent($liveClassId, $studentId, LiveClassStudent::$PERSONAL);
        return $courseStudent;
    }
    
    /**
     * Unenroll Live Class by live class id and student id
     * 
     * @param int $liveClassId
     * @param int $studentId
     * 
     * @return bool|string
     */
    public function unenrollByLiveClassIdAndStudentId($liveClassId, $studentId)
    {
        $liveClassStudent = $this->getLiveClassStudentByLiveClassIdAndStudentId($liveClassId, $studentId);
        if (!$liveClassStudent) {
            return 'Student not enrolled to this Live CLass';
        } else if ($liveClassStudent->type == CourseStudent::$GROUP) {
            return 'Student enrolled to this Live CLass from Group. Cannot unenroll personally';
        }

        $liveClassStudent->delete();
        return true;
    }

    /**
     * Enroll Live Class by live class id and group id
     * 
     * @param int $liveClassId
     * @param int $groupId
     * 
     * @return bool
     */
    public function enrollByLiveClassIdAndGroupId($liveClassId, $groupId)
    {
        $liveClass = $this->getLiveClassById($liveClassId);
        $group = $this->groupService->getOne($groupId);
        if ($this->groupService->hasClassAccess($group->id, $liveClass->class_id)) {
            return false;
        }

        DB::beginTransaction();
        $group->classes()->attach($liveClass->class_id);

        $group->students->each(function ($student) use ($liveClass) {
            $this->isStudentExist($student->id);
            $this->createLiveClassStudent($liveClass->id, $student->id, LiveClassStudent::$GROUP);
        });

        DB::commit();
        return true;
    }

    /**
     * Unenroll Live Class by live class id and group id
     * 
     * @param int $liveClassId
     * @param int $groupId
     * 
     * @return bool
     */
    public function unenrollByLiveClassIdAndGroupId($liveClassId, $groupId)
    {
        $liveClass = $this->getLiveClassById($liveClassId);
        $group = $this->groupService->getOne($groupId);

        if (!$this->groupService->hasClassAccess($group->id, $liveClass->class_id)) {
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
     * Get Live Class Student by LiveClassId and StudentId
     * 
     * @param int $liveClassId
     * @param int $studentId
     * 
     * @return LiveClassStudent
     */
    public function getLiveClassStudentByLiveClassIdAndStudentId($liveClassId, $studentId)
    {
        return $this->liveClassStudent->where('live_class_id', $liveClassId)->where('student_id', $studentId)->first();
    }

    /**
     * Switch Live Class Tutor by LiveClassId and TutorId
     * 
     * @param int $liveClassId
     * @param int $tutorId
     * 
     * @return bool|string
     */
    public function switchTutorByLiveClassIdAndTutorId($liveClassId, $tutorId)
    {
        $this->tutor->select('id')->findOrFail($tutorId);
        
        $liveClass = $this->getLiveClassById($liveClassId);

        if ($liveClass->class->tutor_id == $tutorId) {
            return 'Tutor already assigned to this Live Class';
        }
        $liveClass->class->update(['tutor_id' => $tutorId]);
        return true;
    }
}
