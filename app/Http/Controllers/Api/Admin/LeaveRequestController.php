<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeaveRequestRequest;
use App\Http\Requests\UpdateLeaveRequestRequest;
use App\Http\Resources\LeaveRequestResource;
use App\Models\LeaveRequest;
use App\Services\LeaveRequestService;

class LeaveRequestController extends Controller
{
    function __construct(
        protected LeaveRequestService $service
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $leaveRequests = LeaveRequestResource::collection($this->service->getAll());

        return response()->json([
            'status' => 200,
            'message' => 'Leave Request Fetched Successfully',
            'data' => $leaveRequests->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLeaveRequestRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(LeaveRequest $leaveRequest)
    {
        $leaveRequest = LeaveRequestResource::make($this->service->getOne($leaveRequest->id));

        return response()->json([
            'status' => 200,
            'message' => 'Leave Request Fetched Successfully',
            'data' => $leaveRequest
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeaveRequestRequest $request, LeaveRequest $leaveRequest)
    {
        $data = $request->validated();

        $this->service->updateStatus($leaveRequest->id, $data['status']);

        return response()->json([
            'status' => 200,
            'message' => 'Leave Request Processed Successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaveRequest $leaveRequest)
    {
        //
    }
}
