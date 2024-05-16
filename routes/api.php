<?php

use App\Http\Controllers\Api\Admin\AnnouncementController;
use App\Http\Controllers\Api\Admin\AttendanceController;
use App\Http\Controllers\Api\Admin\ChapterController;
use App\Http\Controllers\Api\Admin\ClassSessionController;
use App\Http\Controllers\Api\Admin\CourseWorkController;
use App\Http\Controllers\Api\Admin\CurriculumController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\GuardianController;
use App\Http\Controllers\Api\Admin\PaymentController;
use App\Http\Controllers\Api\Admin\StudentController;
use App\Http\Controllers\Api\Admin\TeacherController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Front\SearchStudentController;
use App\Http\Controllers\Api\Guardian\StudentController as GuardianStudentController;
use App\Http\Controllers\Api\Student\CourseStudentController;
use App\Http\Controllers\Api\Teacher\TeacherFileController;
use App\Http\Controllers\Api\User\AgendaController;
use App\Http\Controllers\Api\User\AnnouncementController as UserAnnouncementController;
use App\Http\Controllers\Api\User\ProfileController as UserProfileController;
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
    // Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});
Route::name('front')->prefix('front')->group(function () {
    Route::post('student/search', [SearchStudentController::class, 'index'])->name('student.search');
});
Route::middleware(['auth:api'])->group(function () {
    Route::name('admin.')->middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::prefix('dashboard')->group(function () {
            Route::get('users', [DashboardController::class, 'users']);
            Route::get('calendars', [DashboardController::class, 'calendars']);
            Route::get('payment-overdues', [DashboardController::class, 'paymentOverdues']);
            Route::get('attendances', [DashboardController::class, 'attendances']);
        });

        Route::resource('users', UserController::class)->except(['edit', 'create']);

        Route::apiResource('students', StudentController::class);
        Route::prefix('students/{student_id}')->group(function () {
            Route::put('password', [StudentController::class, 'changePassword'])->name('students.change-password');
        });

        Route::apiResource('guardians', GuardianController::class);
        Route::prefix('guardians/{guardian}')->group(function () {
            Route::post('student', [GuardianController::class, 'addStudent'])->name('guardians.add-student');
            Route::delete('student/{student}', [GuardianController::class, 'removeStudent'])->name('guardians.remove-student');
            Route::put('password', [GuardianController::class, 'changePassword'])->name('guardians.change-password');
        });

        Route::apiResource('teachers', TeacherController::class);
        Route::prefix('teachers/{teacher_id}')->group(function () {
            Route::put('password', [TeacherController::class, 'changePassword'])->name('teachers.change-password');
        });

        // Route::resource('announcements', AnnouncementController::class)->except(['edit', 'create']);

        Route::prefix('payments')->group(function () {
            Route::get('/', [PaymentController::class, 'index'])->name('payments.index');
            Route::post('/', [PaymentController::class, 'store'])->name('payments.store');
        });

        Route::apiResource('curricula', CurriculumController::class);
        Route::prefix('curricula')->group(function () {
            // Api Resource Chapter
            Route::apiResource('{curriculum}/chapters', ChapterController::class);
        });

        Route::apiResource('course-works', CourseWorkController::class);
        Route::prefix('course-works/{course_work}')->group(function () {
            Route::post('teacher', [CourseWorkController::class, 'addTeachers'])->name('course-works.add-teacher');
            Route::delete('teacher/{teacher_id}', [CourseWorkController::class, 'removeTeacher'])->name('course-works.remove-teacher');
        });
        
        Route::apiResource('class-sessions', ClassSessionController::class);

        Route::post('attendances', [AttendanceController::class, 'store'])->name('attendances.store');
        Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
        Route::get('attendances/{attendance}', [AttendanceController::class, 'show'])->name('attendances.index');
    });

    Route::name('teacher.')->middleware(['role:teacher'])->prefix('teacher')->group(function () {
        // Route::get('/', [TeacherProfileController::class, 'index'])->name('index');
        // Route::put('/', [TeacherProfileController::class, 'update'])->name('update');
        // Route::put('password', [TeacherProfileController::class, 'changePassword'])->name('change-password');

        Route::apiResource('storage-files', TeacherFileController::class);
    });

    Route::name('guardian.')->middleware(['role:guardian'])->prefix('guardian')->group(function () {
        Route::prefix('students')->name('students.')->group(function () {
            Route::get('/', [GuardianStudentController::class, 'index'])->name('index');
            Route::get('{student_id}', [GuardianStudentController::class, 'show'])->name('show');

            // Route::get('{student_id}/attendances', [GuardianController::class, 'attendances'])->name('student.attendances');
            // Route::get('{student_id}/course-works', [GuardianController::class, 'courseWorks'])->name('student.course-works');
            // Route::get('{student_id}/course-works/{course_work_id}', [GuardianController::class, 'courseWork'])->name('student.course-work');
        });
    });

    Route::name('student.')->middleware(['role:student'])->prefix('student')->group(function () {
        // Route::get('/', [StudentProfileController::class, 'index'])->name('profile');
        // Route::put('/', [StudentProfileController::class, 'update'])->name('profile.update');
        // Route::put('password', [StudentProfileController::class, 'updatePassword'])->name('profile.update.password');

        // Terkait "My Course"
        Route::apiResource('course-students', CourseStudentController::class)->only(['index', 'show']);
    });

    Route::name('user.')->middleware(['role:admin|teacher|student|guardian'])->prefix('user')->group(function () {
        // Route::get('announcements', [UserAnnouncementController::class, 'index'])->name('announcements.index');
        // Route::get('announcements/{announcement_id}', [UserAnnouncementController::class, 'show'])->name('announcements.show');

        Route::apiResource('agendas', AgendaController::class)->except(['update', 'show']);
        Route::get('calendars', [AgendaController::class, 'calendar'])->name('agendas.calendar');

        Route::get('/', [UserProfileController::class, 'index'])->name('profile');
        Route::put('/', [UserProfileController::class, 'update'])->name('profile.update');
        Route::put('password', [UserProfileController::class, 'updatePassword'])->name('profile.update.password');
    });

    Route::post('auth/logout', [AuthController::class, 'logout']);
});