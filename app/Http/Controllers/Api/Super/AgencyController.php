<?php

namespace App\Http\Controllers\Api\Super;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAgencyRequest;
use App\Http\Requests\UpdateAgencyRequest;
use App\Http\Resources\AgencyResource;
use App\Services\AgencyService;

class AgencyController extends Controller
{
    protected $agencyService;

    public function __construct(AgencyService $agencyService)
    {
        $this->agencyService = $agencyService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $agencies = AgencyResource::collection($this->agencyService->getAll());

        if (count($agencies) == 0) {
            throw new ModelGetEmptyException('Agencies');
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $agencies->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAgencyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAgencyRequest $request)
    {
        $validated = $request->validated();

        $agency = $this->agencyService->create($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new AgencyResource($agency)
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
        $agency = $this->agencyService->getOne($id);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new AgencyResource($agency)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAgencyRequest  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAgencyRequest $request, $id)
    {
        $validated = $request->validated();

        $agency = $this->agencyService->update($id, $validated);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new AgencyResource($agency)
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
        $this->agencyService->delete($id);

        return response()->json([
            'status' => 200,
            'message' => 'Success'
        ], 200);
    }
}
