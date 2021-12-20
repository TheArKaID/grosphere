<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLiveClassRequest;
use App\Http\Requests\UpdateLiveClassRequest;
use App\Http\Resources\LiveClassResource;
use App\Models\LiveClass;
use App\Services\LiveClassService;

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
            'response' => $liveClasses->response()->getData(true)
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
            'response' => $liveClass
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
            'response' => $liveClass
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
            'response' => $liveClass
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
}
