<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tutor\UpdatePasswordRequest;
use App\Http\Requests\Tutor\UpdateRequest;
use App\Http\Resources\TutorResource;
use App\Services\TutorService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    private $tutorService;
    public function __construct(TutorService $tutorService)
    {
        $this->tutorService = $tutorService;
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
            'data' => new TutorResource($user->detail)
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

        $this->tutorService->update(auth()->user()->detail->id, $validated);

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

        $this->tutorService->changePasswordByTutor(auth()->user()->detail->id, $validated);

        return response()->json([
            'status' => 200,
            'message' => 'Password Updated Successfully'
        ], 200);
    }
}
