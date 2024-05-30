<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassSessionRequest;
use App\Http\Requests\UpdateClassSessionRequest;
use App\Http\Resources\ClassSessionResource;
use App\Models\ClassSession;
use App\Services\ClassSessionService;
class ClassSessionController extends Controller
{
    protected $classSessionService;

    public function __construct(ClassSessionService $classSessionService)
    {
        $this->classSessionService = $classSessionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classSessions = ClassSessionResource::collection($this->classSessionService->getAll()->load('courseWork'));

        if ($classSessions->count() == 0) {
            throw new ModelGetEmptyException('ClassSession');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $classSessions->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClassSessionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassSession $classSession)
    {
        $classSession = new ClassSessionResource($this->classSessionService->getOne($classSession->id)->load(['studentClasses', 'courseWork', 'classMaterials']));

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $classSession->response()->getData(true)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClassSessionRequest $request, ClassSession $classSession)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassSession $classSession)
    {
        //
    }

    /**
     * End the class session.
     * 
     * @param ClassSession $classSession
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function end(ClassSession $classSession)
    {
        $this->classSessionService->end($classSession->id);

        return response()->json([
            'status' => 200,
            'message' => 'Success'
        ], 200);
    }
}
