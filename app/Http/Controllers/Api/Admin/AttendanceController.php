<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\ClassGroup;
use App\Services\AttendanceService;

class AttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attendances = AttendanceResource::collection($this->attendanceService->pair());

        return response()->json([
            'status' => 200,
            'message' => 'All attendance',
            'data' => $attendances->response()->getData(true)
        ], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttendanceRequest $request)
    {
        return response()->json([
            'status' => 201,
            'message' => 'Attendance recorded',
            'data' => AttendanceResource::make($this->attendanceService->create($request->validated()))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $attendance)
    {
        return response()->json([
            'status' => 200,
            'message' => 'Attendance detail',
            'data' => $this->attendanceService->find($attendance)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendanceRequest $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }

    /**
     * Get All groups of all student attendance.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function showGroup()
    {
        $attendances = $this->attendanceService->allGroups();

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
            'message' => 'All attendance',
            'data' => ($attendances)
        ], 201);
    }
}
