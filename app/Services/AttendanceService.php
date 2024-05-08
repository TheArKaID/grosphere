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
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function pair()
    {
        return $this->attendance->whereType('in')->paginate(10);
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

    /**
     * Get the attendance record by in id.
     * 
     * @param integer $id
     * 
     * @return array
     */
    function find(int $id) : array
    {
        $in = $this->attendance->where('type', 'in')->findOrFail($id)
        ->setHidden(['out']);

        $out = $this->attendance->where('student_id', $in->student_id)
            ->where('type', 'out')
            ->whereDate('created_at', $in->created_at)
            ->first()?->setHidden(['out']);

        return [
            'in' => $in,
            'out' => $out
        ];
    }
}
