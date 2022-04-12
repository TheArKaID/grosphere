<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseCategoryResource;
use App\Services\CourseCategoryService;

class CourseCategoryController extends Controller
{
    private $service;

    public function __construct(CourseCategoryService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courseCategorys = CourseCategoryResource::collection($this->service->getAll());

        if ($courseCategorys->count() == 0) {
            throw new ModelGetEmptyException('Course Category');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $courseCategorys->response()->getData(true)
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $courseCategoryId
     * @return \Illuminate\Http\Response
     */
    public function show(int $courseCategoryId)
    {
        $courseCategory = $this->service->getById($courseCategoryId);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new CourseCategoryResource($courseCategory)
        ], 200);
    }
}
