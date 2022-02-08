<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tutor\UpdatePasswordRequest;
use App\Http\Requests\Tutor\UpdateRequest;
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
        
    }
}
