<?php

namespace App\Http\Controllers\Teacher\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassMaterialRequest;
use App\Http\Resources\ClassMaterialResource;
use App\Models\ClassMaterial;
use App\Models\ClassSession;
use App\Services\ClassMaterialService;

class ClassMaterialController extends Controller
{
    function __construct(
        protected ClassMaterialService $service
    ) { }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classMaterials = ClassMaterialResource::collection($this->service->getAll());

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $classMaterials->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClassMaterialRequest $request)
    {
        $classMaterial = $this->service->create($request->validated());

        return response()->json([
            'status' => 201,
            'message' => 'Class Material created successfully',
            'data' => new ClassMaterialResource($classMaterial)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassSession $classSession, ClassMaterial $classMaterial)
    {
        $classMaterial = new ClassMaterialResource($this->service->getOne($classMaterial->id));

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $classMaterial->response()->getData(true)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassSession $classSession, ClassMaterial $classMaterial)
    {
        $this->service->delete($classMaterial->id);

        return response()->json([
            'status' => 200,
            'message' => 'Class Material deleted successfully',
        ], 200);
    }
}
