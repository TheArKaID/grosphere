<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\ModelGetEmptyException;
use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

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
        $data = $this->dashboardService->nationalHoliday(request('date', null));

        if (gettype($data) === 'string') {
            return response()->json([
                'status' => 500,
                'message' => $data
            ], 500);
        }

        return response()->json([
            'status' => 200,
            'message' => 'All Calendar Data',
            'data' => $data
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

    /**
     * Return Attendances Data
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function attendances(Request $request)
    {
        return response()->json([
            'status' => 200,
            'message' => 'All Attendances Data',
            'data' => $this->dashboardService->attendances($request->get('filter', null))
        ], 200);
    }
}
