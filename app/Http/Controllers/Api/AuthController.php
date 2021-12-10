<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\CreateStudentRequest;
use App\Services\StudentService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private $userService;
    private $studentService;

    public function __construct(
        UserService $userService,
        StudentService $studentService
    ) {
        $this->studentService = $studentService;
        $this->userService = $userService;
    }

    /**
     * Register User
     *
     * @param CreateStudentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(CreateStudentRequest $request)
    {
        $validated = $request->validated();

        $student = $this->studentService->createStudent($validated);

        return response()->json([
            'status' => 200,
            'message' => 'Register berhasil',
            'data' => [
                'student' => $student,
                'token' => $student->user->createToken('ApiToken')->plainTextToken
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

        $user = $this->userService->login($validated);

        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => 'Email atau Password salah'
            ], 401);
        }
        
        return response()->json([
            'status' => 200,
            'message' => 'Login berhasil',
            'response' => [
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
