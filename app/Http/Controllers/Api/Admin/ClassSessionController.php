<?php

namespace App\Http\Controllers\Api\Admin;

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
        $classSessions = ClassSessionResource::collection($this->classSessionService->getAll());

        if ($classSessions->count() == 0) {
           throw new ModelGetEmptyException('ClassSessions');
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
        $data = $request->validated();
        $classSession = $this->classSessionService->create($data);

        return response()->json([
            'status' => 200,
            'message' => 'ClassSession Created Successfully',
            'data' => $classSession
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassSession $classSession)
    {
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => ClassSessionResource::make($classSession->load('teacher', 'courseWork', 'classMaterials', 'studentClasses'))
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClassSessionRequest $request, ClassSession $classSession)
    {
        $data = $request->validated();
        $classSession = $this->classSessionService->update($classSession, $data);

        return response()->json([
            'status' => 200,
            'message' => 'ClassSession Updated Successfully',
            'data' => $classSession
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassSession $classSession)
    {
        $this->classSessionService->delete($classSession);

        return response()->json([
            'status' => 200,
            'message' => 'ClassSession Deleted Successfully'
        ], 200);
    }
}
