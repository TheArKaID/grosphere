<?php

use App\Http\Controllers\Api\Admin\AnnouncementController;
use App\Http\Controllers\Api\Admin\AttendanceController;
use App\Http\Controllers\Api\Admin\ChapterController;
use App\Http\Controllers\Api\Admin\ClassGroupController;
use App\Http\Controllers\Api\Admin\ClassSessionController;
use App\Http\Controllers\Api\Admin\CourseWorkController;
use App\Http\Controllers\Api\Admin\CurriculumController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\GuardianController;
use App\Http\Controllers\Api\Admin\LeaveRequestController as AdminLeaveRequestController;
use App\Http\Controllers\Api\Admin\PaymentController;
use App\Http\Controllers\Api\Admin\StudentController;
use App\Http\Controllers\Api\Admin\TeacherController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FrontController;
use App\Http\Controllers\Api\Guardian\AttendanceController as GuardianAttendanceController;
use App\Http\Controllers\Api\Guardian\LeaveRequestController;
use App\Http\Controllers\Api\Guardian\StudentController as GuardianStudentController;
use App\Http\Controllers\Api\Student\ClassSessionController as StudentClassSessionController;
use App\Http\Controllers\Api\Student\CourseStudentController;
use App\Http\Controllers\Api\Super\AgencyController;
use App\Http\Controllers\Api\Super\StudentController as SuperStudentController;
use App\Http\Controllers\Api\Teacher\AttendanceController as TeacherAttendanceController;
use App\Http\Controllers\Api\Teacher\ClassMaterialController;
use App\Http\Controllers\Api\Teacher\ClassSessionController as TeacherClassSessionController;
use App\Http\Controllers\Api\Teacher\TeacherFileController;
use App\Http\Controllers\Api\User\AgendaController;
use App\Http\Controllers\Api\User\AnnouncementController as UserAnnouncementController;
use App\Http\Controllers\Api\User\FeedController;
use App\Http\Controllers\Api\User\MessageController;
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

Route::get('email', function () {
    return view('emails.new-message', [
        'sender' => 'ArKa',
        'subdomain' => (fn($subdomain) => $subdomain ? str_replace('://', '://' . $subdomain . '.', env('FE_URL')) : env('FE_URL'))(rand(0, 1) ? 'arka' : null),
        'receiver' => 'Not Arka',
        'message' => 'Simple Message Mail Testing',
        'messageTime' => \Carbon\Carbon::parse(now())->format('Y-m-d H:i:s')
    ]);
});

Route::prefix('auth')->group(function () {
    // Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});
Route::name('front')->prefix('front')->group(function () {
    Route::post('student/search', [FrontController::class, 'searchStudent'])->name('student.search');
    Route::get('theme', [FrontController::class, 'getTheme'])->name('theme.index');
});
Route::middleware(['auth:api'])->group(function () {
    Route::name('superadmin.')->middleware(['role:superadmin'])->prefix('superadmin')->group(function () {
        Route::apiResource('agencies', AgencyController::class);
        Route::prefix('agencies/{agency}')->group(function () {
            Route::post('admin', [AgencyController::class, 'createAdmin'])->name('agencies.admin');
            Route::delete('admin/{admin}', [AgencyController::class, 'deleteAdmin'])->name('agencies.admin.delete');
            
            Route::get('students', [SuperStudentController::class, 'index'])->name('agencies.students.index');
            Route::get('students/{students}', [SuperStudentController::class, 'show'])->name('agencies.students.show');
        });
    });
    Route::name('admin.')->middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::prefix('dashboard')->group(function () {
            Route::get('users', [DashboardController::class, 'users']);
            Route::get('calendars', [DashboardController::class, 'calendars']);
            Route::get('payment-overdues', [DashboardController::class, 'paymentOverdues']);
            Route::get('attendances', [DashboardController::class, 'attendances']);
            Route::get('attendance-summary', [DashboardController::class, 'attendanceSummary']);
        });

        Route::resource('users', UserController::class)->except(['edit', 'create']);

        Route::apiResource('students', StudentController::class);
        Route::prefix('students/{student_id}')->group(function () {
            Route::put('password', [StudentController::class, 'changePassword'])->name('students.change-password');
            Route::post('guardians', [StudentController::class, 'syncGuardians'])->name('students.guardians');
        });

        Route::apiResource('guardians', GuardianController::class);
        Route::prefix('guardians/{guardian}')->group(function () {
            Route::post('student', [GuardianController::class, 'syncStudent'])->name('guardians.sync-student');
            Route::put('password', [GuardianController::class, 'changePassword'])->name('guardians.change-password');
        });

        Route::apiResource('teachers', TeacherController::class);
        Route::prefix('teachers/{teacher_id}')->group(function () {
            Route::put('password', [TeacherController::class, 'changePassword'])->name('teachers.change-password');
        });

        Route::apiResource('announcements', AnnouncementController::class);

        Route::prefix('announcements/{announcement_id}')->group(function () {
            Route::put('status', [AnnouncementController::class, 'toggleStatus'])->name('announcements.toggle-status');
        });

        Route::prefix('payments')->group(function () {
            Route::post('/', [PaymentController::class, 'store'])->name('payments.store');
            Route::get('/', [PaymentController::class, 'index'])->name('payments.index');
            Route::get('{subscription}', [PaymentController::class, 'show'])->name('payments.show');
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

        Route::prefix('attendances')->group(function () {
            Route::post('', [AttendanceController::class, 'store'])->name('attendances.store');
            Route::get('', [AttendanceController::class, 'index'])->name('attendances.index');
            Route::get('groups', [AttendanceController::class, 'showGroup'])->name('attendances.group');
            Route::get('groups/{classGroup}', [AttendanceController::class, 'showGroupDetail'])->name('attendances.group.detail');
            Route::get('groups/{classGroup}/{student}', [AttendanceController::class, 'showStudentDetail'])->name('attendances.group.student');

            Route::get('{attendance}', [AttendanceController::class, 'show'])->name('attendances.show');
            
        });

        Route::apiResource('class-groups', ClassGroupController::class);
        
        Route::apiResource('leave-requests', AdminLeaveRequestController::class)->except(['store', 'destroy']);
    });

    Route::name('teacher.')->middleware(['role:teacher'])->prefix('teacher')->group(function () {
        // Route::get('/', [TeacherProfileController::class, 'index'])->name('index');
        // Route::put('/', [TeacherProfileController::class, 'update'])->name('update');
        // Route::put('password', [TeacherProfileController::class, 'changePassword'])->name('change-password');

        Route::apiResource('storage-files', TeacherFileController::class);
        
        Route::prefix('class-sessions')->group(function () {
            Route::get('/', [TeacherClassSessionController::class, 'index'])->name('class-sessions.index');
            Route::get('{class_session}', [TeacherClassSessionController::class, 'show'])->name('class-sessions.show');
            Route::post('{class_session}/end', [TeacherClassSessionController::class, 'end'])->name('class-sessions.end');

            Route::ApiResource('{classSession}/materials', ClassMaterialController::class)->except('update');
        });

        Route::prefix('class-groups')->group(function () {
            Route::get('', [TeacherAttendanceController::class, 'showGroup'])->name('class-groups');
            Route::get('{classGroup}', [TeacherAttendanceController::class, 'showGroupDetail'])->name('class-groups.detail');
            Route::get('{classGroup}/{student}', [TeacherAttendanceController::class, 'showStudentDetail'])->name('class-groups.detail.student');
        });

    });

    Route::name('guardian.')->middleware(['role:guardian'])->prefix('guardian')->group(function () {
        Route::get('payments', [GuardianStudentController::class, 'subscriptions'])->name('subscriptions.index');
        Route::get('payments/{subscription}', [GuardianStudentController::class, 'subscription'])->name('subscriptions.detail');
        Route::prefix('students')->name('students.')->group(function () {
            Route::get('/', [GuardianStudentController::class, 'index'])->name('index');
            Route::get('{student_id}', [GuardianStudentController::class, 'show'])->name('show');

            // Route::get('{student_id}/attendances', [GuardianController::class, 'attendances'])->name('student.attendances');
            // Route::get('{student_id}/course-works', [GuardianController::class, 'courseWorks'])->name('student.course-works');
            // Route::get('{student_id}/course-works/{course_work_id}', [GuardianController::class, 'courseWork'])->name('student.course-work');
        });

        Route::get('attendances', [GuardianAttendanceController::class, 'index'])->name('attendances.index');
        Route::get('attendances/{attendance}', [GuardianAttendanceController::class, 'show'])->name('attendances.show');

        Route::get('leave-requests/tags', [LeaveRequestController::class, 'tags'])->name('leave-request.tags');
        Route::resource('leave-requests', LeaveRequestController::class);
    });

    Route::name('student.')->middleware(['role:student'])->prefix('student')->group(function () {
        // Route::get('/', [StudentProfileController::class, 'index'])->name('profile');
        // Route::put('/', [StudentProfileController::class, 'update'])->name('profile.update');
        // Route::put('password', [StudentProfileController::class, 'updatePassword'])->name('profile.update.password');

        // Terkait "My Course"
        Route::apiResource('course-students', CourseStudentController::class)->only(['index', 'show']);
        
        Route::prefix('class-sessions')->group(function () {
            Route::get('/', [StudentClassSessionController::class, 'index'])->name('class-sessions.index');
            Route::get('student-classes', [StudentClassSessionController::class, 'studentClasses'])->name('class-sessions.student-classess.index');
            Route::get('{class_session}', [StudentClassSessionController::class, 'showStudentClasses'])->name('class-sessions.student-classess.show');
            Route::post('{class_session}/enroll', [StudentClassSessionController::class, 'enroll'])->name('class-sessions.enroll');
        });
    });

    Route::name('user.')->middleware(['role:admin|teacher|student|guardian|superadmin'])->prefix('user')->group(function () {
        // Route::get('announcements', [UserAnnouncementController::class, 'index'])->name('announcements.index');
        // Route::get('announcements/{announcement_id}', [UserAnnouncementController::class, 'show'])->name('announcements.show');

        Route::apiResource('agendas', AgendaController::class)->except(['update', 'show']);
        Route::get('calendars', [AgendaController::class, 'calendar'])->name('agendas.calendar');

        Route::get('/', [UserProfileController::class, 'index'])->name('profile');
        Route::put('/', [UserProfileController::class, 'update'])->name('profile.update');
        Route::put('password', [UserProfileController::class, 'updatePassword'])->name('profile.update.password');
        Route::get('theme', [UserProfileController::class, 'getTheme'])->name('profile.theme');

        Route::prefix('messages')->group(function () {
            Route::get('recipient', [MessageController::class, 'getRecipients'])->name('messages.recipient');
        });
        Route::apiResource('messages', MessageController::class)->except(['destroy', 'update']);

        Route::prefix('announcements')->group(function () {
            Route::get('', [UserAnnouncementController::class, 'index'])->name('announcements.index');
            Route::get('{announcement_id}', [UserAnnouncementController::class, 'show'])->name('announcements.show');
        });

        Route::apiResource('feeds', FeedController::class);
        Route::prefix('feeds/{id}')->group(function () {
            Route::post('comment', [FeedController::class, 'comment'])->name('feeds.comment');
            Route::post('like', [FeedController::class, 'likeUnlike'])->name('feeds.like');
        });
    });

    Route::post('auth/logout', [AuthController::class, 'logout']);
});