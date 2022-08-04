<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\AgoraTutorUploadFileRequest;
use App\Http\Requests\StoreLiveClassRequest;
use App\Http\Requests\UpdateLiveClassRequest;
use App\Http\Resources\LiveClassResource;
use App\Http\Resources\ValidatedLiveClassResource;
use App\Services\LiveClassService;
use App\Services\LiveUserService;
use App\Services\TutorService;
use Illuminate\Support\Carbon;

class LiveClassController extends Controller
{
    private $liveClassService, $tutorService, $liveUserService;

    public function __construct(LiveClassService $liveClassService, TutorService $tutorService, LiveUserService $liveUserService)
    {
        $this->liveClassService = $liveClassService;
        $this->tutorService = $tutorService;
        $this->liveUserService = $liveUserService;
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
        $liveUser = $this->tutorService->joinLiveClass($liveClassId);

        if (gettype($liveUser) != 'string') {
            $liveClass = $liveUser->liveClass;

            return response()->json([
                'status' => 200,
                'message' => 'Tutor joined Live Class',
                'data' => [
                    'live_class_name' => $liveClass->class->name,
                    'token' => $liveUser->token,
                    'end_time' => Carbon::parse($liveClass->start_time)->addMinutes($liveClass->duration)->toDateTimeString(),
                    'duration' => $liveClass->duration,
                    'thumbnail' => asset('storage/class/thumbnail/' . $liveClass->class->thumbnail),
                ]
            ], 200);
        }

        return response()->json([
            'status' => 400,
            'message' => $liveUser
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

        if (!$this->liveClassService->isLiveClassStarted($liveUser->liveClass->id)) {
            return response()->json([
                'status' => 400,
                'message' => 'Live Class is not started yet or it is already ended'
            ], 400);
        }

        if (!$this->liveUserService->invalidateLiveUserToken($liveUser->id)) {
            return response()->json([
                'status' => 400,
                'message' => 'Token is invalid'
            ], 400);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Live Class validated',
            'data' => new ValidatedLiveClassResource($liveUser),
        ]);
    }

    /**
     * Upload file from Agora Tutor
     * @param  AgoraTutorUploadFileRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function agoraUploadFile(AgoraTutorUploadFileRequest $request, $id)
    {
        $validated = $request->validated();
        $validated['id'] = $id;
        $uploaded = $this->liveUserService->uploadFileFromAgora($validated);

        if (gettype($uploaded) == 'array') {
            return response()->json([
                'status' => true,
                'message' => 'File uploaded successfully',
                'data' => $uploaded
            ], 200);
        }
        
        return response()->json([
            'status' => false,
            'message' => "Failed to upload file"
        ], 400);
    }

    /**
     * Get File from Agora Tutor
     * 
     * @param  int  $id
     * 
     * @return \Illuminate\Http\Response
     */
    public function agoraGetFile($id)
    {
        $file = $this->liveUserService->getFileFromAgora($id);
        
        if (gettype($file) == 'array') {
            return response()->json([
                'status' => true,
                'message' => 'File retrieved successfully',
                'data' => $file
            ], 200);
        }
        
        return response()->json([
            'status' => false,
            'message' => "No file found"
        ], 200);
    }
}
