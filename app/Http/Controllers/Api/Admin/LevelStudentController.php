<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLevelStudentRequest;
use App\Http\Requests\UpdateLevelStudentRequest;
use App\Http\Resources\LevelStudentResource;
use App\Models\LevelStudent;
use App\Services\LevelStudentService;

class LevelStudentController extends Controller
{
    private $levelStudentService;

    public function __construct(LevelStudentService $levelStudentService)
    {
        $this->levelStudentService = $levelStudentService;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $levelStudents = LevelStudentResource::collection($this->levelStudentService->getAll());

        if (count($levelStudents) == 0) {
            throw new ModelGetEmptyException('Level Student');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $levelStudents->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreLevelStudentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLevelStudentRequest $request)
    {
        $validated = $request->validated();

        $levelStudent = $this->levelStudentService->create($validated);

        return response()->json([
            'status' => 201,
            'message' => 'Level Student Created Successfully',
            'data' => new LevelStudentResource($levelStudent)
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LevelStudent  $levelStudent
     * @return \Illuminate\Http\Response
     */
    public function show(LevelStudent $levelStudent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLevelStudentRequest  $request
     * @param  \App\Models\LevelStudent  $levelStudent
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLevelStudentRequest $request, LevelStudent $levelStudent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LevelStudent  $levelStudent
     * @return \Illuminate\Http\Response
     */
    public function destroy(LevelStudent $levelStudent)
    {
        //
    }
}
