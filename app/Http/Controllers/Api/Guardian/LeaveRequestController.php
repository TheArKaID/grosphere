<?php

namespace App\Http\Controllers\Api\Guardian;

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
        $data = LeaveRequestResource::collection($this->service->getAll());

        return response()->json([
            'status' => 200,
            'data' => $data->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLeaveRequestRequest $request)
    {
        $data = $request->validated();

        $this->service->create($data);

        return response()->json([
            'status' => 201
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(LeaveRequest $leaveRequest)
    {
        $data = new LeaveRequestResource($leaveRequest);

        return response()->json([
            'status' => 200,
            'data' => $data
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeaveRequestRequest $request, LeaveRequest $leaveRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaveRequest $leaveRequest)
    {
        $this->service->delete($leaveRequest);

        return response()->json([
            'status' => 200
        ], 200);
    }
}
