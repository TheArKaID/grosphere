<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLevelRequest;
use App\Http\Requests\UpdateLevelRequest;
use App\Http\Resources\LevelResource;
use App\Services\LevelService;

class LevelController extends Controller
{
    private $levelService;

    public function __construct(LevelService $levelService)
    {
        $this->levelService = $levelService;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $levels = LevelResource::collection($this->levelService->getAll());

        if(count($levels)==0){
            throw new ModelGetEmptyException('Level');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $levels->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreLevelRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLevelRequest $request)
    {
        $validated = $request->validated();

        $level = new LevelResource($this->levelService->create($validated));

        return response()->json([
            'status' => 200,
            'message' => 'Level Created Successfully',
            'data' => $level
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $levelId
     * @return \Illuminate\Http\Response
     */
    public function show(int $levelId)
    {
        $level = new LevelResource($this->levelService->getById($levelId));

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $level
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLevelRequest  $request
     * @param  int  $levelId
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLevelRequest $request, int $levelId)
    {
        $validated = $request->validated();

        $level = new LevelResource($this->levelService->update($levelId, $validated));

        return response()->json([
            'status' => 200,
            'message' => 'Level Updated Successfully',
            'data' => $level
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $levelId
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $levelId)
    {
        $this->levelService->delete($levelId);

        return response()->json([
            'status' => 200,
            'message' => 'Level Deleted Successfully'
        ], 200);
    }
}
