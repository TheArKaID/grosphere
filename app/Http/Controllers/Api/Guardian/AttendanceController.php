<?php

namespace App\Http\Controllers\Api\Guardian;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
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
        $attendances = AttendanceResource::collection($this->attendanceService->pair(auth()->user()->detail->id));

        if ($attendances->count() == 0) {
            // throw new ModelGetEmptyException("Attendance");
        }

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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $attendance)
    {
        return response()->json([
            'status' => 200,
            'message' => 'Attendance detail',
            'data' => $this->attendanceService->find($attendance, auth()->user()->detail->id)
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
}
