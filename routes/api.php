<?php

use App\Http\Controllers\Api\Admin\AnnouncementController;
use App\Http\Controllers\Api\Admin\LiveClassController;
use App\Http\Controllers\Api\Admin\ParentController;
use App\Http\Controllers\Api\Admin\StudentController;
use App\Http\Controllers\Api\Admin\TutorController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Api\Tutor\LiveClassController as TutorLiveClassController;
use App\Http\Controllers\Api\Tutor\ProfileController as TutorProfileController;
use App\Http\Controllers\Api\User\LiveClassController as UserLiveClassController;
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
    Route::post('refresh', [AuthController::class, 'refresh']);
});

Route::middleware(['auth:api'])->group(function () {
    Route::name('admin.')->middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::resource('users', UserController::class)->except(['edit', 'create']);

        Route::resource('students', StudentController::class)->except(['edit', 'create']);
        Route::prefix('students/{student_id}')->group(function () {
            Route::put('password', [StudentController::class, 'changePassword'])->name('students.change-password');
        });

        Route::resource('parents', ParentController::class)->except(['edit', 'create']);
        Route::prefix('parents/{parent_id}')->group(function () {
            Route::post('child', [ParentController::class, 'addChild'])->name('parents.add-child');
            Route::put('password', [ParentController::class, 'changePassword'])->name('parents.change-password');
        });

        Route::resource('tutors', TutorController::class)->except(['edit', 'create']);
        Route::prefix('tutors/{tutor_id}')->group(function () {
            Route::put('password', [TutorController::class, 'changePassword'])->name('tutors.change-password');
        });

        // Route::resource('classes', ClassController::class)->except(['edit', 'create']);
        Route::resource('live-classes', LiveClassController::class)->except(['edit', 'create']);
        // Route::resource('course-classes', CourseController::class)->except(['edit', 'create']);

        Route::resource('announcements', AnnouncementController::class)->except(['edit', 'create']);
    });

    Route::name('tutor.')->middleware(['role:tutor'])->prefix('tutor')->group(function () {
        Route::get('/', [TutorProfileController::class, 'index'])->name('index');
        Route::put('/', [TutorProfileController::class, 'update'])->name('update');
        Route::put('password', [TutorProfileController::class, 'changePassword'])->name('change-password');

        Route::resource('live-classes', TutorLiveClassController::class)->except(['edit', 'create']);
        Route::prefix('live-classes/{live_class_id}')->group(function () {
            Route::post('join', [TutorLiveClassController::class, 'join'])->name('live-classes.join');
            Route::post('leave', [TutorLiveClassController::class, 'leave'])->name('live-classes.leave');
        });
    });

    Route::name('student.')->middleware(['role:student'])->prefix('student')->group(function () {
        Route::get('/', [StudentProfileController::class, 'index'])->name('profile');
        Route::put('/', [StudentProfileController::class, 'update'])->name('profile.update');
        Route::put('password', [StudentProfileController::class, 'updatePassword'])->name('profile.update.password');
    });

    Route::name('user.')->middleware(['role:admin|tutor|student|parent'])->prefix('user')->group(function () {
        Route::resource('live-classes', UserLiveClassController::class)->only('index', 'show');
        Route::prefix('live-classes/{live_class_id}')->group(function () {
            Route::post('join', [UserLiveClassController::class, 'join'])->name('live-classes.join');
            Route::post('leave', [UserLiveClassController::class, 'leave'])->name('live-classes.leave');
        });
    });

    Route::post('auth/logout', [AuthController::class, 'logout']);
});

Route::post('tutor/live-classes/validate', [TutorLiveClassController::class, 'validateLiveClass'])->name('tutor.live-classes.validate');
Route::post('user/live-classes/validate', [UserLiveClassController::class, 'validateLiveClass'])->name('user.live-classes.validate');
