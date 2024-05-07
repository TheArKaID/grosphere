<?php

namespace App\Services;

use App\Models\Attendance;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    protected $attendance;

    public function __construct(Attendance $attendance)
    {
        $this->attendance = $attendance;
    }

    function create(array $data) : Attendance
    {
        DB::beginTransaction();

        $attendance = $this->attendance->create($data);

        $fileName = $attendance->id . '.' . $data['proof']->getClientOriginalExtension();
        $data['proof'] = $data['proof']->storeAs('attendances', $fileName, 's3');
        $attendance->proof = $data['proof'];
        $attendance->save();
        DB::commit();
        return $attendance;
    }
}
