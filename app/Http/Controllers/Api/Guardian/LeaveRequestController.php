<?php

namespace App\Http\Controllers\Api\Guardian;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLeaveRequestRequest;
use App\Http\Requests\UpdateLeaveRequestRequest;
use App\Http\Resources\LeaveRequestResource;
use App\Http\Resources\LeaveRequestTagResource;
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
            'status' => 200,
            'message' => 'Leave request created successfully.'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = new LeaveRequestResource($this->service->getOne($id));

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
            'status' => 200,
            'message' => 'Leave request deleted successfully.'
        ], 200);
    }

    public function tags() {
        $tag = $this->service->getTag();

        return response()->json([
            'status' => 200,
            'message' => 'Data retrieved successfully.',
            'data' => LeaveRequestTagResource::collection($tag)->response()->getData(true)
        ], 200);
    }
}
