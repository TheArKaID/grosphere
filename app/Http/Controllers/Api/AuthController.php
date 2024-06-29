<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Resources\StudentResource;
use App\Http\Resources\UserResource;
use App\Services\StudentService;
use App\Services\UserService;
use Illuminate\Http\Request;

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
     * @param StoreStudentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(StoreStudentRequest $request)
    {
        $validated = $request->validated();

        $student = $this->studentService->createStudent($validated);

        $token = auth()->login($student->user);

        return response()->json([
            'status' => 200,
            'message' => 'Register Successfully',
            'data' => [
                'student' => new StudentResource($student),
                'token' => $token
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
        $origin = $request->headers->get('origin');

        $validated = $request->validated();
        
        $user = $this->userService->login($validated);
        if (!$user) {
            return response()->json([
                'status' => 401,
                'message' => 'Email/Username or password is wrong'
            ], 401);
        }
        if ($user->agency->website != $origin) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized'
            ], 401);
        }
        $token = auth()->login($user);
        return response()->json([
            'status' => 200,
            'message' => 'User Logged In',
            'data' => [
                'user' => new UserResource(auth()->user()),
                'token' => $token
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
        auth()->invalidate(true);

        return response()->json([
            'status' => 200,
            'message' => 'User Logged Out'
        ], 200);
    }

    /**
     * Refresh Token
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        $token = auth()->refresh(true);

        return response()->json([
            'status' => 200,
            'message' => 'Token Refreshed',
            'data' => [
                'token' => $token
            ]
        ], 200);
    }
}
