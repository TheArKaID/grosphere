<?php

namespace App\Services;

use App\Models\LeaveRequest;
use App\Models\LeaveRequestTag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LeaveRequestService
{
    private $model;

    public function __construct(LeaveRequest $model)
    {
        $this->model = $model;
    }

    /**
     * Get all LeaveRequests
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        if ($search = request()->get('search')) {
            $this->model = $this->search($search);
        }

        if (request()->has('page') && request()->get('page') == 'all') {
            return $this->model->with('tag')->get();
        }

        return $this->model->with('tag')->paginate(request('size', 10));
    }

    /**
     * Get one LeaveRequest
     * 
     * @param string $leaveRequestId
     * 
     * @return LeaveRequest
     */
    public function getOne(string $leaveRequestId)
    {
        return $this->model->findOrFail($leaveRequestId);
    }

    /**
     * Search LeaveRequests
     * 
     * @param string $search
     * 
     * @return LeaveRequest
     */
    public function search($search)
    {
        return $this->model->where('reason', 'like', '%' . $search . '%');
    }

    /**
     * Create Leave Request
     * 
     * @param array $data
     * 
     * @return LeaveRequest
     */
    public function create(array $data)
    {
        DB::beginTransaction();

        $data['guardian_id'] = auth()->user()->detail->id;
        $leaveRequest = $this->model->create($data);

        if ($photo = $data['photo']) {
            $fileName = 'leave-requests/' . $leaveRequest->id . '.png';

            // photo is image base64 encoded
            // Decode to image and store to s3
            $photo = base64_decode(substr($photo, strpos($photo, ",")+1));
            Storage::disk('s3')->put($fileName, $photo);
        }

        DB::commit();

        return $leaveRequest;
    }

    /**
     * Update Leave Request
     * 
     * @param array $data
     * @param string $leaveRequestId
     * 
     * @return LeaveRequest
     */
    public function update(array $data, string $leaveRequestId)
    {
        DB::beginTransaction();

        $leaveRequest = $this->model->findOrFail($leaveRequestId);
        $leaveRequest->update($data);

        $leaveRequest->students()->sync($data['students']);
        DB::commit();

        return $leaveRequest;
    }

    /**
     * Update Leave Request
     * 
     * @param string $leaveRequestId
     * @param string $status
     * 
     * @return LeaveRequest
     */
    public function updateStatus(string $leaveRequestId, string $status)
    {
        $leaveRequest = $this->model->findOrFail($leaveRequestId);
        $leaveRequest->update(['status' => $status]);

        return $leaveRequest;
    }

    /**
     * Delete Leave Request
     * 
     * @param LeaveRequest $leaveRequestId
     * 
     * @return void
     */
    public function delete(LeaveRequest $leaveRequest)
    {
        DB::beginTransaction();

        if ($leaveRequest->guardian_id != auth()->user()->detail->id) {
            abort(403, 'Unauthorized');
        }

        $leaveRequest->delete();

        DB::commit();
    }

    function getTag() {
        $model = LeaveRequestTag::query();
        if ($search = request()->get('search')) {
            $model = $model->where('name', 'like', '%' . $search . '%');
        }

        if (request()->has('page') && request()->get('page') == 'all') {
            return $model->get();
        }

        return $model->paginate(request('size', 10));
    }
}
