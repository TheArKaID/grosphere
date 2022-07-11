<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAdminAgencyRequest;
use App\Http\Resources\AgencyResource;
use App\Models\Agency;
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
        $agency = $this->agencyService->getCurrentAgency();

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => AgencyResource::make($agency)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAdminAgencyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAdminAgencyRequest $request)
    {
        $validated = $request->validated();
        $agency = $this->agencyService->updateCurrentAgency($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => AgencyResource::make($agency)
        ], 200);
    }
}
