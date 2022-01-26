<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\UpdatePasswordRequest;
use App\Http\Requests\Student\UpdateRequest;
use App\Http\Resources\StudentResource;
use App\Services\StudentService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    private $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    /**
     * Get user profile
     * 
     * @param Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => new StudentResource($user->detail)
        ], 200);
    }

    /**
     * Update user profile
     * 
     * @param UpdateRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request)
    {
        $validated = $request->validated();

        $this->studentService->updateStudent(auth()->user()->detail->id, $validated);

        return response()->json([
            'status' => 200,
            'message' => 'Profile Updated Successfully'
        ], 200);
    }

    /**
     * Update user password
     * 
     * @param UpdatePasswordRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $validated = $request->validated();

        $this->studentService->changePasswordWithValidation(auth()->user()->detail->id, $validated);

        return response()->json([
            'status' => 200,
            'message' => 'Password Updated Successfully'
        ], 200);
    }
}
