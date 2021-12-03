<?php

use App\Http\Controllers\Api\AuthController;
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
    Route::get('user', function (Request $request) {
        return response()->json([
            'status' => 200,
            'message' => 'success',
            'data' => $request->user(),
        ], 200);
    });

    Route::post('auth/logout', [AuthController::class, 'logout']);
});