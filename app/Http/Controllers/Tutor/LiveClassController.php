<?php

namespace App\Http\Controllers\Tutor;

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
        $liveClasses = LiveClassResource::collection($this->liveClassService->getAllCurrentTutorLiveClasses());

        if ($liveClasses->count() == 0) {
            throw new ModelGetEmptyException("Live Class");
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $liveClasses->response()->getData(true)
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LiveClass  $liveClass
     * @return \Illuminate\Http\Response
     */
    public function show(LiveClass $liveClass)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLiveClassRequest  $request
     * @param  \App\Models\LiveClass  $liveClass
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLiveClassRequest $request, LiveClass $liveClass)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LiveClass  $liveClass
     * @return \Illuminate\Http\Response
     */
    public function destroy(LiveClass $liveClass)
    {
        //
    }
}
