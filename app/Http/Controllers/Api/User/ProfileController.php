<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\AgencyThemeResource;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    private $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
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
            'data' => new UserResource($user)
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

        $this->userService->updateUserForRole(auth()->user()->id, $validated);

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

        $this->userService->changePassword(auth()->user()->id, $validated['new_password']);

        return response()->json([
            'status' => 200,
            'message' => 'Password Updated Successfully'
        ], 200);
    }

    /**
     * Get User Theme
     * 
     * @param Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTheme(Request $request)
    {
        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => AgencyThemeResource::make($this->userService->getUserTheme())
        ], 200);
    }
}
