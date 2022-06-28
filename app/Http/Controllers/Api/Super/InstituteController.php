<?php

namespace App\Http\Controllers\Api\Super;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInstituteRequest;
use App\Http\Requests\UpdateInstituteRequest;
use App\Http\Resources\InstituteResource;
use App\Services\InstituteService;

class InstituteController extends Controller
{
    protected $instituteService;

    public function __construct(InstituteService $instituteService)
    {
        $this->instituteService = $instituteService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $institutes = InstituteResource::collection($this->instituteService->getAll());

        if (count($institutes) == 0) {
            throw new ModelGetEmptyException('Institutes');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $institutes->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreInstituteRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreInstituteRequest $request)
    {
        $validated = $request->validated();

        $institute = $this->instituteService->create($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new InstituteResource($institute)
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $institute = $this->instituteService->getOne($id);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new InstituteResource($institute)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateInstituteRequest  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateInstituteRequest $request, $id)
    {
        $validated = $request->validated();

        $institute = $this->instituteService->update($id, $validated);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new InstituteResource($institute)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->instituteService->delete($id);

        return response()->json([
            'status' => 200,
            'message' => 'Success'
        ], 200);
    }
}
