<?php

namespace App\Services;

use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    protected $attendance;

    public function __construct(Attendance $attendance)
    {
        $this->attendance = $attendance;
    }

    function create(array $data) : Attendance
    {

        $validated = $this->validate($data);

        if ($validated !== true) {
            throw ValidationException::withMessages(['check-type' => $validated]);
        }
        DB::beginTransaction();

        $attendance = $this->attendance->create($data);

        $fileName = $attendance->id . '.' . $data['proof']->getClientOriginalExtension();
        $data['proof'] = $data['proof']->storeAs('attendances', $fileName, 's3');
        $attendance->proof = $data['proof'];
        $attendance->save();
        DB::commit();
        return $attendance;
    }

    /**
     * Validate the attendance of student.
     * Student could only check in once a day, and check out once a day.
     * Check out could only be done by the same student that already checked in.
     * 
     * @param array $data
     * 
     * @return array
     */
    function validate(array $data) : bool|string
    {
        $attendance = $this->attendance->where('student_id', $data['student_id'])
            ->whereDate('created_at', now()->toDateString())
            ->latest()
            ->first();

        if ($attendance) {
            if ($attendance->type === 'in' && $data['type'] === 'in') {
                return 'Student already checked in';
            }

            if ($attendance->type === 'out' && $data['type'] === 'out') {
                return 'Student already checked out';
            }

            if ($attendance->type === 'out' && $data['type'] === 'in') {
                return 'Student has already checked out';
            }
        } elseif ($data['type'] === 'out') {
            return 'Student has not checked in';
        }
        return true;
    }
}
