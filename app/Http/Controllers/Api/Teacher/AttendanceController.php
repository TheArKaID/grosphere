<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassGroup;
use App\Models\Student;
use App\Services\AttendanceService;

class AttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Get All groups of all student attendance.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function showGroup()
    {
        $attendances = $this->attendanceService->allGroups(auth()->user()->detail->id);

        return response()->json([
            'status' => 200,
            'message' => 'All attendance',
            'data' => ($attendances)
        ], 201);
    }

    /**
     * Get detail of the class group for attendance
     * 
     * @param ClassGroup $classGroup
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function showGroupDetail(ClassGroup $classGroup)
    {
        $attendances = $this->attendanceService->groupDetail($classGroup->id);

        return response()->json([
            'status' => 200,
            'message' => 'All Student in a Group Attendance',
            'data' => $attendances
        ], 201);
    }

    /**
     * Get detail of student on the group for attendance
     * 
     * @param ClassGroup $classGroup
     * @param Student $student
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function showStudentDetail(ClassGroup $classGroup, Student $student)
    {
        $attendances = $this->attendanceService->detailStudentInGroup($classGroup, $student);

        return response()->json([
            'status' => 200,
            'message' => 'A Student in a Group Attendance',
            'data' => $attendances
        ], 201);
    }
}
