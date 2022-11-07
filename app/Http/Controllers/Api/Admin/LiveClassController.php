<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLiveClassRequest;
use App\Http\Requests\UpdateLiveClassRequest;
use App\Http\Resources\LiveClassResource;
use App\Services\LiveClassService;
use Illuminate\Http\Request;

class LiveClassController extends Controller
{
    private $liveClassService;

    public function __construct(LiveClassService $liveClassService)
    {
        $this->liveClassService = $liveClassService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $liveClasses = LiveClassResource::collection($this->liveClassService->getAllLiveClasses());

        if ($liveClasses->count() == 0) {
            throw new ModelGetEmptyException("Live Class");
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $liveClasses
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreLiveClassRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLiveClassRequest $request)
    {
        $validated = $request->validated();

        $liveClass = new LiveClassResource($this->liveClassService->createLiveClass($validated));

        return response()->json([
            'status' => 200,
            'message' => 'Live Class Created Successfully',
            'data' => $liveClass
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $liveClass = new LiveClassResource($this->liveClassService->getLiveClassById($id));

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $liveClass
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLiveClassRequest  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLiveClassRequest $request, $id)
    {
        $validated = $request->validated();

        $liveClass = new LiveClassResource($this->liveClassService->updateLiveClass($id, $validated));

        return response()->json([
            'status' => 200,
            'message' => 'Live Class Updated Successfully',
            'data' => $liveClass
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->liveClassService->deleteLiveClass($id);

        return response()->json([
            'status' => 200,
            'message' => 'Live Class Deleted Successfully'
        ], 200);
    }

    /**
     * Enroll Student to Live Class
     * 
     * @param  Request  $request
     * @param  int  $liveClassId
     * 
     * @return \Illuminate\Http\Response
     */
    public function enrollStudent(Request $request, int $liveClassId)
    {
        $enrolled = $this->liveClassService->enrollByLiveClassIdAndStudentId($liveClassId, $request['student_id']);

        if (gettype($enrolled) == 'string') {
            return response()->json([
                'status' => 400,
                'message' => $enrolled
            ], 400);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Student enrolled to live class'
        ], 200);
    }

    /**
     * Unenroll Student from Live Class
     * 
     * @param  Request  $request
     * @param  int  $liveClassId
     * 
     * @return \Illuminate\Http\Response
     */
    public function unenrollStudent(Request $request, int $liveClassId)
    {
        $unenrolled = $this->liveClassService->unenrollByLiveClassIdAndStudentId($liveClassId, $request['student_id']);

        if (gettype($unenrolled) == 'string') {
            return response()->json([
                'status' => 400,
                'message' => $unenrolled
            ], 400);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Student unenrolled from live class'
        ], 200);
    }

    /**
     * Enroll Group to Live Class
     * 
     * @param  Request  $request
     * @param  int  $liveClassId
     * 
     * @return \Illuminate\Http\Response
     */
    public function enrollGroup(Request $request, int $liveClassId)
    {
        $this->liveClassService->enrollByLiveClassIdAndGroupId($liveClassId, $request['group_id']);

        return response()->json([
            'status' => 200,
            'message' => 'Group enrolled to live class'
        ], 200);
    }

    /**
     * Unenroll Group from Live Class
     * 
     * @param  int  $liveClassId
     * @param  int  $groupId
     * 
     * @return \Illuminate\Http\Response
     */
    public function unenrollGroup(int $liveClassId, int $groupId)
    {
        $this->liveClassService->unenrollByLiveClassIdAndGroupId($liveClassId, $groupId);

        return response()->json([
            'status' => 200,
            'message' => 'Group unenrolled from live class'
        ], 200);
    }
}
