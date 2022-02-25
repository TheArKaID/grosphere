<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLiveClassRequest;
use App\Http\Requests\UpdateLiveClassRequest;
use App\Http\Resources\LiveClassResource;
use App\Services\LiveClassService;
use App\Services\TutorService;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

class LiveClassController extends Controller
{
    private $liveClassService, $tutorService;

    public function __construct(LiveClassService $liveClassService, TutorService $tutorService)
    {
        $this->liveClassService = $liveClassService;
        $this->tutorService = $tutorService;
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
        $validated = $request->validated();
        $validated['tutor_id'] = auth()->user()->detail->id;

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $liveClass = new LiveClassResource($this->liveClassService->getCurrentTutorLiveClass($id));

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
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLiveClassRequest $request, int $id)
    {
        $validated = $request->validated();

        $liveClass = new LiveClassResource($this->liveClassService->updateCurrentTutorLiveClass($id, $validated));

        return response()->json([
            'status' => 200,
            'message' => 'Live Class Updated Successfully',
            'data' => $liveClass
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $this->liveClassService->deleteCurrentTutorLiveClass($id);

        return response()->json([
            'status' => 200,
            'message' => 'Live Class Deleted Successfully'
        ], 200);
    }

    /**
     * Tutor Join Live Class
     * 
     * @param  int  $liveClassId
     * 
     * @return \Illuminate\Http\Response
     */
    public function join(int $liveClassId)
    {
        if ($liveUser = $this->tutorService->joinLiveClass($liveClassId)) {
            return response()->json([
                'status' => 200,
                'message' => 'Tutor joined Live Class',
                'data' => [
                    'live_class_name' => $liveUser->liveClass->class->name,
                    'room' => $liveUser->liveClass->host_code,
                    'token' => $liveUser->token,
                    'end_time' => Carbon::parse($liveUser->liveClass->start_time)->addMinutes($liveUser->liveClass->duration)->toDateTimeString(),
                    'thumbnail' => asset('storage/class/thumbnail/' . $liveUser->liveClass->class->thumbnail),
                ]
            ], 200);
        }

        return response()->json([
            'status' => 400,
            'message' => 'Live Class has ended'
        ], 400);
    }

    /**
     * Validate live class
     * 
     * @return \Illuminate\Http\Response
     */
    public function validateLiveClass()
    {
        $liveUser = $this->liveUserService->getLiveUserByToken(request('token'));

        if (!$this->liveClassService->isLiveClassStartedByCode($liveUser->liveClass->host_code)) {
            return response()->json([
                'status' => 400,
                'message' => 'Live Class is not started yet or it is already ended'
            ], 400);
        }

        if(!$this->liveUserService->invalidateLiveUserToken($liveUser->id)) {
            return response()->json([
                'status' => 400,
                'message' => 'Token is invalid'
            ], 400);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Live Class validated',
            'data' => [
                'tutor_name' => $liveUser->liveClass->class->tutor->user->name,
                'user_name' => $liveUser->user->name,
                'role' => $liveUser->user->roles[0]->name
            ],
        ]);
    }
}
