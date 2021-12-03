<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private $userService;

    public function __construct(
        UserService $userService
    ) {
        $this->userService = $userService;
    }

    /**
     * Register User
     *
     * @param RegisterUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterUserRequest $request)
    {
        $validated = $request->validated();

        $user = $this->userService->createUser($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Register berhasil',
            'data' => [
                'user' => $user,
                'token' => $user->createToken('ApiToken')->plainTextToken
            ]
        ], 200);
    }

    /**
     * Login User
     *
     * @param LoginUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginUserRequest $request)
    {
        $validated = $request->validated();

        $user = $this->userService->getByEmail($validated['email']);

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'status' => 401,
                'message' => 'Email atau Password salah'
            ], 401);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Login berhasil',
            'data' => [
                'user' => $user,
                'token' => $user->createToken('ApiToken')->plainTextToken
            ]
        ], 200);
    }

    /**
     * Logout User
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Logout berhasil'
        ], 200);
    }
}
