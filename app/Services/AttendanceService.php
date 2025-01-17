<?php

namespace App\Services;

use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\ClassGroup;
use App\Models\Student;
use Doctrine\DBAL\Query;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    protected $attendance;

    public function __construct(Attendance $attendance)
    {
        $this->attendance = $attendance;
    }

    /**
     * Get All Student attendance pair, in and out.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function all() : \Illuminate\Database\Eloquent\Collection
    {
        return $this->attendance->with('student')->get();
    }

    /**
     * Pair all attendances in and out for everyday.
     * 
     * @param string $parent_id
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function pair($parent_id = null)
    {
        if ($parent_id) {
            $this->attendance = $this->attendance->where('guardian_id', $parent_id);
        }

        if ($search = request()->get('search', false)) {
            $this->attendance = $this->attendance
            ->where(function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->whereHas('student', function ($query) use ($search) {
                        $query->whereHas('user', function ($query) use ($search) {
                            $query->where('first_name', 'like', '%' . $search . '%')->orWhere('last_name', 'like', '%' . $search . '%');
                        });
                    });
                })
                ->orWhere(function ($query)  use ($search) {
                    $query->whereHas('admin', function ($query) use ($search) {
                        $query->whereHas('user', function ($query) use ($search) {
                            $query->where('first_name', 'like', '%' . $search . '%')->orWhere('last_name', 'like', '%' . $search . '%');
                        });
                    });
                });
            });
        }
        if($date = request()->get('date', false)) {
            $this->attendance = $this->attendance->whereDate('created_at', $date);
        }

        $this->attendance = $this->attendance->with(['student.user', 'guardian.user'])
        ->whereHas('student.user', function ($query) {
            $query->where('agency_id', auth()->user()->agency_id);
        })
        ->whereType('in')->orderBy('id', 'desc');
        return request()->has('page') && request()->get('page') == 'all' ? $this->attendance->get() : $this->attendance->paginate(request('size', 10));
    }

    /**
     * Get all 'in' attendance records.
     * 
     * @param string $filter
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function totalIn($filter = null) : \Illuminate\Database\Eloquent\Collection
    {
        $this->attendance = $this->attendance->select(['id', 'student_id', 'type', 'created_at'])->whereType('in');

        if ($filter) {
            $this->attendance = $this->filterByRange($filter);
        }

        return $this->attendance->get();
    }

    /**
     * Filter weekly or monthly attendance records.
     * 
     * @param string $filter
     * 
     * @return Builder|EloquentBuilder
     */
    function filterByRange(string $filter) : Builder|EloquentBuilder
    {
        switch ($filter) {
            case 'weekly':
                return $this->attendance->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            default:
                return $this->attendance->whereMonth('created_at', now()->month);
                break;
        }
    }
    /**
     * Create a new attendance record.
     * 
     * @param array $data
     * 
     * @return Attendance
     */
    function create(array $data) : Attendance
    {
        $validated = $this->validate($data);

        if ($validated !== true) {
            throw ValidationException::withMessages($validated);
        }
        DB::beginTransaction();

        $proof = $data['proof'];
        $fileName = 'attendances/' . $data['student_id'] . '_' . now()->format('Y-m-d h:i:s') . '_' . $data['type'] . '.' . explode('/', explode(':', substr($proof, 0, strpos($proof, ';')))[1])[1];

        $data['proof'] = '';
        $data['admin_id'] = auth()->user()->detail->id;
        $attendance = $this->attendance->create($data);

        // Proof is image base64 encoded
        // Decode to image and store to s3
        $data['proof'] = base64_decode(substr($proof, strpos($proof, ",")+1));
        Storage::disk('s3')->put($fileName, $data['proof']);
        $data['proof'] = $fileName;
        $attendance->proof = $data['proof'];
        $attendance->save();
        DB::commit();
        return $attendance;
    }

    /**
     * Validate the attendance of student.
     * Student should check in before check out.
     * After the checkout, student could check in again.
     * 
     * @param array $data
     * 
     * @return array|bool
     */
    function validate(array $data) : array|bool
    {
        $guardianService = app(GuardianService::class);
        if (!$guardianService->isGuardian($data['guardian_id'], $data['student_id'])) {
            return ['guardian' => 'Guardian is not the guardian of the student'];
        }

        $attendance = $this->attendance->where('student_id', $data['student_id'])
            ->whereDate('created_at', now()->toDateString())
            ->latest()
            ->first();

        if ($attendance) {
            if ($attendance->type === 'in' && $data['type'] === 'in') {
                return ['check-type' => 'Student already checked in'];
            }

            if ($attendance->type === 'out' && $data['type'] === 'out') {
                return ['check-type' => 'Student already checked out'];
            }
        } elseif ($data['type'] === 'out') {
            return ['check-type' => 'Student has not checked in'];
        }

        return true;
    }

    /**
     * Get the attendance record by in id.
     * 
     * @param string $id
     * @param string $guardian_id
     * 
     * @return array
     */
    function find(string $id, string $guardian_id = null) : array
    {
        $in = $this->attendance->where('type', 'in')->where(function ($query) use ($guardian_id) {
            if ($guardian_id) {
                $query->where('guardian_id', $guardian_id);
            }
        })->findOrFail($id)
        ->setHidden(['out'])->load(['student.user', 'guardian']);

        $out = $this->attendance->where('student_id', $in->student_id)
            ->where('type', 'out')
            ->whereDate('created_at', $in->created_at)
            ->where('created_at', '>', $in->created_at)
            ->first()?->setHidden(['out'])?->load(['student.user', 'guardian']);

        return [
            'in' => AttendanceResource::make($in),
            'out' => $out ? AttendanceResource::make($out) : null
        ];
    }

    /**
     * Get All groups of all student attendance.
     * 
     * @return array|Collection
     */
    function allGroups(string $teacher_id = '') : array|\Illuminate\Database\Eloquent\Collection
    {
        request()->merge(['page' => 'all']);
        $classGroups = app()->make(ClassGroupService::class)->getAll($teacher_id);

        // TODO: Load students.leaveRequests
        $classGroups->load(['students.attendances' => fn($q) => [
            ($date = request()->get('date') ?? now()) ? $q->whereDate('created_at', $date)->whereType('in') : $q
        ]]);

        // Count the total of students in each class group
        $classGroups->map(function ($classGroup) {
            $totalAttendance = 0;
            $classGroup->totalStudent = $classGroup->students->count();
            $classGroup->students->map(function ($student) use (&$totalAttendance) {
                $totalAttendance = $totalAttendance + ($student->attendances->count() > 0 ? 1 : 0);
            });
            $classGroup->totalAttendance = $totalAttendance;
            $totalAttendance = 0;
            unset($classGroup->students);
            return $classGroup;
        });
        
        return $classGroups;
    }

    /**
     * Get detail of the class group for attendance
     * 
     * @param string $classGroup
     * 
     * @return ClassGroup
     */
    function groupDetail(string $classGroup) : ClassGroup
    {
        $classGroup = app()->make(ClassGroupService::class)->getOne($classGroup);

        $classGroup->load(['students.user', 'students.attendances' => fn($q) => [
            ($date = request()->get('date') ?? now()) ? $q->whereDate('created_at', $date)->latest() : $q->latest()
        ]]);

        $students = [];

        $classGroup->students->map(function ($student) use (&$students) {
            $temp = $student;
            $student = [];

            $leaveRequest = $temp->leaveRequests->where('status', 'approved')->where('from_date', '<=', now())->where('to_date', '>=', now())->first();
            if ($leaveRequest) {
                $student['id'] = $temp->id;
                $student['first_name'] = $temp->user->first_name;
                $student['last_name'] = $temp->user->last_name;
                $student['status'] = 'Leave Request';
                $student['leave'] = $leaveRequest;
                $students[] = $student;
                return;
            }

            $id = $temp->attendances->where('type', 'in')->first()?->id;

            $student = $id
                ? collect($this->find($id))->transform(fn($inout) => $inout?->created_at)->toArray()
                : ['in' => null, 'out' => null];

            $student['id'] = $temp->id;
            $student['first_name'] = $temp->user->first_name;
            $student['last_name'] = $temp->user->last_name;

            $student['status'] = $temp->attendances->count() > 0 ? 'Attend' : 'Absent';
            $students[] = $student;
        });

        unset($classGroup->students);

        $classGroup->students = $students;

        return $classGroup;
    }

    /**
     * Get the attendance record by student id.
     * 
     * @param ClassGroup $group
     * @param Student $student
     * 
     * @return Student
     */
    function detailStudentInGroup(ClassGroup $group, Student $student) : Student
    {
        $classGroupService = app()->make(ClassGroupService::class);
        if (!$classGroupService->checkStudent($group->id, $student->id)) {
            throw ValidationException::withMessages(['student' => 'Student not found in the class group.']);
        }

        $student->load(['user', 'attendances' => fn($q) => [
            ($date = request()->get('date') ?? now()) ? $q->whereDate('created_at', $date)->whereType('in') : $q
        ]]);

        $attendances = $student->attendances;

        unset($student->attendances);

        $student->first_name = $student->user->first_name;
        $student->last_name = $student->user->last_name;

        $leaveRequest = $student->leaveRequests->where('status', 'approved')->where('from_date', '<=', now())->where('to_date', '>=', now())->first();
        if ($leaveRequest) {
            $student->status = 'Leave Request';
            $student->leave = $leaveRequest;
            unset($student->leaveRequests);
            return $student;
        }

        $attendances = $attendances->map(function ($attendance) {
            $a = ($x = $this->find($attendance->id)) != [] ? (function ($y) {
                unset($y['in']['student']);
                unset($y['out']['student']);
                return $y;
            })($x) : [];

            return $a;
        });

        $student->attendances = $attendances;
        unset($student->user);
        return $student;
    }
}
