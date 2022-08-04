<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Services\AgencyService;
use Illuminate\Http\Request;

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
     * @param  Request  $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function config(Request $request)
    {
        $agencyKey = $request->header('X-Agency-Key');
        
        $agency = $this->agencyService->getConfig($agencyKey);
        
        return response()->json([
            'status' => 200,
            'message' => 'Student not found',
            'data' => $agency
        ], 200);
    }
}
