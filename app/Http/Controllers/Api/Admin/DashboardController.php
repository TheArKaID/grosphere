<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display a listing of the resource.
     */
    public function users()
    {
        return response()->json([
            'status' => 200,
            'message' => 'All Users Data',
            'data' => $this->dashboardService->users()
        ], 200);
    }

    /**
     * Get calendars data
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function calendars()
    {
        return response()->json([
            'status' => 200,
            'message' => 'All Calendar Data',
            'data' => $this->dashboardService->calendars(request('date', null))
        ], 200);
    }

    /**
     * Return Payment Overdues Data
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function paymentOverdues()
    {
        return response()->json([
            'status' => 200,
            'message' => 'All Payment Overdues Data',
            'data' => $this->dashboardService->paymentOverdues()
        ], 200);
    }
}
