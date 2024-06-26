<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCurriculumRequest;
use App\Http\Requests\UpdateCurriculumRequest;
use App\Http\Resources\CurriculumResource;
use App\Models\Curriculum;
use App\Services\CurriculumService;

class CurriculumController extends Controller
{

    protected $curriculumService;

    public function __construct(CurriculumService $curriculumService)
    {
        $this->curriculumService = $curriculumService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $curriculums = CurriculumResource::collection($this->curriculumService->getAll());
        
        if ($curriculums->count() == 0) {
            // throw new ModelGetEmptyException("Curriculum");
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $curriculums->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCurriculumRequest $request)
    {
        $curriculum = $this->curriculumService->create($request->validated());

        return response()->json([
            'status' => 200,
            'message' => 'Curriculum Created Successfully',
            'data' => $curriculum
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Curriculum $curriculum)
    {
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $curriculum
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCurriculumRequest $request, Curriculum $curriculum)
    {
        $new = $this->curriculumService->update($curriculum, $request->validated());

        return response()->json([
            'status' => 200,
            'message' => 'Curriculum Updated Successfully',
            'data' => $new
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Curriculum $curriculum)
    {
        $this->curriculumService->delete($curriculum);

        return response()->json([
            'status' => 200,
            'message' => 'Curriculum Deleted Successfully',
        ], 200);
    }
}
