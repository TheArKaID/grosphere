<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassGroupRequest;
use App\Http\Requests\UpdateClassGroupRequest;
use App\Http\Resources\ClassGroupResource;
use App\Models\ClassGroup;
use App\Services\ClassGroupService;

class ClassGroupController extends Controller
{
    public function __construct(
        private ClassGroupService $service
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classGroups = ClassGroupResource::collection($this->service->getAll()->load(['teachers']));

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $classGroups->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClassGroupRequest $request)
    {
        $classGroup = $this->service->create($request->validated());

        return response()->json([
            'status' => 201,
            'message' => 'Class Group created successfully',
            'data' => new ClassGroupResource($classGroup->load(['teachers', 'students']))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassGroup $classGroup)
    {
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new ClassGroupResource($classGroup->load(['teachers', 'students']))
        ], 200);
    }

    /**
     * @param UpdateClassGroupRequest $request
     * @param string $class_group
     * 
     * Update the specified resource in storage.
     */
    public function update(UpdateClassGroupRequest $request, string $class_group)
    {
        $classGroup = $this->service->update($request->validated(), $class_group);

        return response()->json([
            'status' => 200,
            'message' => 'Class Group updated successfully',
            'data' => new ClassGroupResource($classGroup->load(['teachers', 'students']))
        ], 200);
    }

    /**
     * @param string $class_group
     * 
     * Remove the specified resource from storage.
     */
    public function destroy(string $classGroupId)
    {
        $this->service->delete($classGroupId);

        return response()->json([
            'status' => 200,
            'message' => 'Class Group deleted successfully'
        ], 200);
    }
}
