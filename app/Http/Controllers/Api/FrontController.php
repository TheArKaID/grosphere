<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentFrontSearchResource;
use App\Services\FrontService;
use App\Services\StudentService;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function __construct(
        protected FrontService $service
    ) {
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchStudent(StudentService $studentService)
    {
        $students = $studentService->searchByEmail(request('email', ''));

        if ($students->count() > 0) {
            return response()->json([
                'status' => 200,
                'message' => 'Success',
                'data' => ($students),
            ], 200);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Student not found'
        ], 200);
    }

    /**
     * Get Theme of Agency
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTheme(Request $request)
    {
        $theme = $this->service->getTheme($request->header('origin', 'postman.grosphere.sg'));

        return response()->json([
            'status' => 200,
            'message' => 'Theme',
            'data' => $theme
        ], 200);
    }
}
