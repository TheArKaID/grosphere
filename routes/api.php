<?php

use App\Http\Controllers\Api\Admin\AgencyController as AdminAgencyController;
use App\Http\Controllers\Api\Admin\AnnouncementController;
use App\Http\Controllers\Api\Admin\CourseCategoryController;
use App\Http\Controllers\Api\Admin\CourseWorkController;
use App\Http\Controllers\Api\Admin\GroupController;
use App\Http\Controllers\Api\Admin\LiveClassController;
use App\Http\Controllers\Api\Admin\ParentController;
use App\Http\Controllers\Api\Admin\StudentController;
use App\Http\Controllers\Api\Admin\TutorController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Front\AgencyController as FrontAgencyController;
use App\Http\Controllers\Api\Front\SearchStudentController;
use App\Http\Controllers\Api\Student\AskAnswerController as StudentAskAnswerController;
use App\Http\Controllers\Api\Student\ChapterAssignmentController as StudentChapterAssignmentController;
use App\Http\Controllers\Api\Student\ChapterMaterialController as StudentChapterMaterialController;
use App\Http\Controllers\Api\Student\CourseChapterController as StudentCourseChapterController;
use App\Http\Controllers\Api\Student\CourseStudentController;
use App\Http\Controllers\Api\Student\CourseWorkController as StudentCourseWorkController;
use App\Http\Controllers\Api\Student\TestQuestionController as StudentTestQuestiontController;
use App\Http\Controllers\Api\Super\AdminController;
use App\Http\Controllers\Api\Super\AgencyController;
use App\Http\Controllers\Api\Tutor\AskAnswerController as TutorAskAnswerController;
use App\Http\Controllers\Api\Tutor\ChapterAssignmentController as TutorChapterAssignmentController;
use App\Http\Controllers\Api\Tutor\ChapterMaterialController as TutorChapterMaterialController;
use App\Http\Controllers\Api\Tutor\ChapterTestController as TutorChapterTestController;
use App\Http\Controllers\Api\Tutor\CourseCategoryController as TutorCourseCategoryController;
use App\Http\Controllers\Api\Tutor\CourseChapterController as TutorCourseChapterController;
use App\Http\Controllers\Api\Tutor\CourseWorkController as TutorCourseWorkController;
use App\Http\Controllers\Api\Tutor\LiveClassController as TutorLiveClassController;
use App\Http\Controllers\Api\Tutor\TestQuestionController as TutorTestQuestionController;
use App\Http\Controllers\Api\User\AgendaController;
use App\Http\Controllers\Api\User\AnnouncementController as UserAnnouncementController;
use App\Http\Controllers\Api\User\LiveClassController as UserLiveClassController;
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

    Route::get('agency/config', [FrontAgencyController::class, 'config'])->name('agency.config');
});
Route::middleware(['auth:api'])->group(function () {
    Route::name('super-admin.')->middleware(['role:super-admin'])->prefix('super-admin')->group(function () {
        Route::resource('agencies', AgencyController::class);
        Route::resource('admins', AdminController::class);
    });
    Route::name('admin.')->middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::resource('users', UserController::class)->except(['edit', 'create']);

        Route::get('agency', [AdminAgencyController::class, 'index']);
        Route::put('agency', [AdminAgencyController::class, 'update']);

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

        Route::ApiResource('groups', GroupController::class);

        // Route::resource('classes', ClassController::class)->except(['edit', 'create']);
        Route::resource('live-classes', LiveClassController::class)->except(['edit', 'create']);
        Route::resource('course-works', CourseWorkController::class)->except(['edit', 'create']);
        Route::post('course-works/{id}/students/enroll', [CourseWorkController::class, 'enrollStudent'])->name('course-works.students.enroll');

        Route::resource('announcements', AnnouncementController::class)->except(['edit', 'create']);
        Route::apiResource('course-categories', CourseCategoryController::class);
    });

    Route::name('tutor.')->middleware(['role:tutor'])->prefix('tutor')->group(function () {
        // Route::get('/', [TutorProfileController::class, 'index'])->name('index');
        // Route::put('/', [TutorProfileController::class, 'update'])->name('update');
        // Route::put('password', [TutorProfileController::class, 'changePassword'])->name('change-password');

        Route::resource('live-classes', TutorLiveClassController::class)->except(['edit', 'create']);
        Route::prefix('live-classes/{live_class_id}')->group(function () {
            Route::post('join', [TutorLiveClassController::class, 'join'])->name('live-classes.join');
            Route::post('leave', [TutorLiveClassController::class, 'leave'])->name('live-classes.leave');
        });

        Route::apiResource('course-categories', TutorCourseCategoryController::class)->only(['index', 'show']);
        Route::apiResource('course-works', TutorCourseWorkController::class);

        Route::name('ask-answers.')->prefix('ask-answers')->group(function () {
            Route::get('', [TutorAskAnswerController::class, 'index'])->name('index');
            Route::get('{course_student_id}', [TutorAskAnswerController::class, 'show'])->name('show');
            Route::post('{course_student_id}', [TutorAskAnswerController::class, 'store'])->name('store');
        });
        Route::name('course-works.chapters.')->prefix('course-works/{course_work}/chapters')->group(function () {
            Route::post('image', [TutorCourseChapterController::class, 'uploadImage'])->name('image.upload');
            Route::delete('image', [TutorCourseChapterController::class, 'deleteImage'])->name('image.delete');
        });
        Route::apiResource('course-works.chapters', TutorCourseChapterController::class);
        Route::apiResource('course-works.chapters.materials', TutorChapterMaterialController::class)->except(['update']);
        Route::apiResource('course-works.chapters.assignments', TutorChapterAssignmentController::class)->only(['index', 'store']);
        Route::name('course-works.chapters.assignments.')->prefix('course-works/{course_work}/chapters/{chapter}/assignments')->group(function () {
            Route::delete('/', [TutorChapterAssignmentController::class, 'destroy'])->name('destroy');
            Route::post('file', [TutorChapterAssignmentController::class, 'uploadFile'])->name('upload-file');
            Route::delete('file', [TutorChapterAssignmentController::class, 'deleteFile'])->name('delete-file');
            Route::get('answers', [TutorChapterAssignmentController::class, 'getStudentAnswers'])->name('answers');
            Route::post('score/{studentAssignmentId}', [TutorChapterAssignmentController::class, 'scoreStudentAssignment'])->name('score');
        });
        Route::apiResource('course-works.chapters.tests', TutorChapterTestController::class)->only(['index', 'store']);
        Route::name('course-works.chapters.tests.')->prefix('course-works/{course_work}/chapters/{chapter}/tests')->group(function () {
            Route::delete('/', [TutorChapterTestController::class, 'destroy'])->name('destroy');
            Route::get('questions', [TutorTestQuestionController::class, 'index'])->name('questions.index');
            Route::post('questions', [TutorTestQuestionController::class, 'store'])->name('questions.store');
            Route::get('questions/{question_id}', [TutorTestQuestionController::class, 'show'])->name('questions.show');
            Route::put('questions/{question_id}', [TutorTestQuestionController::class, 'update'])->name('questions.update');
            Route::delete('questions/{question_id}', [TutorTestQuestionController::class, 'destroy'])->name('questions.destroy');

            Route::name('course-works.chapters.tests.results')->prefix('results')->group(function () {
                Route::get('/', [TutorChapterTestController::class, 'showResults'])->name('index');
                Route::get('{student_test_id}', [TutorChapterTestController::class, 'showResult'])->name('show');
                // Perlukah dijadikan status = Scored?
                Route::post('{student_test_id}', [TutorChapterTestController::class, 'saveScoring'])->name('show');

                Route::post('{student_test_id}/{student_answer_id}', [TutorChapterTestController::class, 'scoreStudentAnswer'])->name('score-student-answer');
            });
        });
    });

    Route::name('student.')->middleware(['role:student'])->prefix('student')->group(function () {
        // Route::get('/', [StudentProfileController::class, 'index'])->name('profile');
        // Route::put('/', [StudentProfileController::class, 'update'])->name('profile.update');
        // Route::put('password', [StudentProfileController::class, 'updatePassword'])->name('profile.update.password');

        Route::name('ask-answers.')->prefix('ask-answers')->group(function () {
            Route::get('', [StudentAskAnswerController::class, 'index'])->name('index');
            Route::get('{course_student_id}', [StudentAskAnswerController::class, 'show'])->name('show');
            Route::post('{course_student_id}', [StudentAskAnswerController::class, 'store'])->name('store');
        });

        Route::apiResource('course-works', StudentCourseWorkController::class)->only(['index', 'show']);
        Route::name('course-works.')->prefix('course-works/{course_work_id}')->group(function () {
            Route::post('enroll', [StudentCourseWorkController::class, 'enroll'])->name('enroll');
        });
        Route::apiResource('course-works.chapters', StudentCourseChapterController::class)->only(['index', 'show']);
        Route::name('course-works.chapters.')->prefix('course-works/{course_work_id}/chapters/{chapter_id}')->group(function () {
            Route::apiResource('materials', StudentChapterMaterialController::class)->only(['index', 'show']);
            Route::apiResource('assignments', StudentChapterAssignmentController::class)->only(['index', 'store']);
            Route::name('assignments.')->prefix('assignments')->group(function () {
                Route::get('answer', [StudentChapterAssignmentController::class, 'answer'])->name('answer');
                Route::post('file', [StudentChapterAssignmentController::class, 'uploadFile'])->name('upload-file');
                Route::delete('file', [StudentChapterAssignmentController::class, 'deleteFile'])->name('delete-file');
            });
            // Route::apiResource('tests', StudentTestQuestiontController::class)->only(['index', 'store']);
            Route::name('tests.')->prefix('tests')->group(function () {
                // Get test summary
                // Different response when test is not enrolled
                Route::get('/', [StudentTestQuestiontController::class, 'index'])->name('index');
                // Enroll to test
                Route::post('enroll', [StudentTestQuestiontController::class, 'enroll'])->name('enroll');
                // Submit Answer
                Route::post('submit', [StudentTestQuestiontController::class, 'submitTest'])->name('submit');
                // Get Question
                Route::get('{test_id}', [StudentTestQuestiontController::class, 'getQuestion'])->name('show');
                Route::get('{test_id}/answer', [StudentTestQuestiontController::class, 'getMyAnswer'])->name('show.answer');
                // Answer Question
                Route::post('{test_id}', [StudentTestQuestiontController::class, 'answerQuestion'])->name('answer');
            });
        });

        // Terkait "My Course"
        Route::apiResource('course-students', CourseStudentController::class)->only(['index', 'show']);
    });

    Route::name('user.')->middleware(['role:admin|tutor|student|parent'])->prefix('user')->group(function () {
        Route::resource('live-classes', UserLiveClassController::class)->only('index', 'show');
        Route::prefix('live-classes/{live_class_id}')->group(function () {
            Route::post('join', [UserLiveClassController::class, 'join'])->name('live-classes.join');
            Route::post('leave', [UserLiveClassController::class, 'leave'])->name('live-classes.leave');
        });
        
        Route::get('announcements', [UserAnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('announcements/{announcement_id}', [UserAnnouncementController::class, 'show'])->name('announcements.show');

        Route::apiResource('agendas', AgendaController::class)->except(['update', 'show']);
        Route::get('calendars', [AgendaController::class, 'calendar'])->name('agendas.calendar');

        Route::get('/', [UserProfileController::class, 'index'])->name('profile');
        Route::put('/', [UserProfileController::class, 'update'])->name('profile.update');
        Route::put('password', [UserProfileController::class, 'updatePassword'])->name('profile.update.password');
    });

    Route::post('auth/logout', [AuthController::class, 'logout']);
});
Route::get('tutor/live-classes/agora/file/{id}', [TutorLiveClassController::class, 'agoraGetFile'])->name('tutor.live-classes.upload-file.get');
Route::post('tutor/live-classes/agora/file/{id}', [TutorLiveClassController::class, 'agoraUploadFile'])->name('tutor.live-classes.upload-file.post');
Route::post('tutor/live-classes/agora/file/{id}/delete', [TutorLiveClassController::class, 'agoraDeleteFile'])->name('tutor.live-classes.upload-file.delete');
Route::post('tutor/live-classes/validate', [TutorLiveClassController::class, 'validateLiveClass'])->name('tutor.live-classes.validate');
Route::post('user/live-classes/validate', [UserLiveClassController::class, 'validateLiveClass'])->name('user.live-classes.validate');
