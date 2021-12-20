<?php

use App\Http\Controllers\Api\Admin\ParentController;
use App\Http\Controllers\Api\Admin\StudentController;
use App\Http\Controllers\Api\Admin\TutorController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::resource('users', UserController::class)->except(['edit', 'create']);

        Route::resource('students', StudentController::class)->except(['edit', 'create']);
        Route::prefix('students/{student_id}')->group(function () {
            Route::put('password', [StudentController::class, 'changePassword']);
        });

        Route::resource('parents', ParentController::class)->except(['edit', 'create']);
        Route::prefix('parents/{parent_id}')->group(function () {
            Route::post('child', [ParentController::class, 'addChild']);
            Route::put('password', [ParentController::class, 'changePassword']);
        });
        
        Route::resource('tutors', TutorController::class)->except(['edit', 'create']);
        Route::prefix('tutors/{tutor_id}')->group(function () {
            Route::put('password', [TutorController::class, 'changePassword']);
        });
    });
    Route::get('/user', function () {
        return User::with(['detail'])->find(auth()->user()->id);
    });

    Route::get('profile', function (Request $request) {
        $user = $request->user();
        $roles = $user->roles->map(function ($role) {
            return [
                'name' => $role->name,
                'readable_name' => $role->readable_name,
            ];
        });
        unset($user->roles);
        $user->roles = $roles;

        return response()->json([
            'status' => 200,
            'message' => 'success',
            'response' => $user
        ], 200);
    });

    Route::post('auth/logout', [AuthController::class, 'logout']);
});
