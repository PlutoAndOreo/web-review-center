<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\StudentMiddleware;

// Auth Controllers
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\StudentDashboard as StudentAdminDashboardController;

// Student Controllers
use App\Http\Controllers\Student\LoginController;
use App\Http\Controllers\Student\RegistrationController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\StudentVideoController;
use App\Http\Controllers\Student\CommentController;

    Route::get('/', function () {
        return redirect()->route('student.login'); // route name for student login
    });

    // CSRF Token endpoint (public, no auth required)
    Route::get('/api/csrf-token', [\App\Http\Controllers\CsrfTokenController::class, 'token'])->name('api.csrf-token');

    // Route::middleware('guest:admin')->group(function () {
        Route::post('admin/register', [RegisteredUserController::class, 'store']);
        Route::get('admin/login', [AuthenticatedSessionController::class, 'create'])->name('admin.login');
        Route::post('admin/login', [AuthenticatedSessionController::class, 'store'])->name('admin.store');
    // });

    Route::middleware(['auth:admin'])->group(function () {
        Route::post('admin/logout', [AuthenticatedSessionController::class, 'logout'])->name('admin.logout');
        Route::get('admin/dashboard',[DashboardController::class,'index'])->name('admin.dashboard');
        
        Route::prefix('admin/users')->name('admin.users.')->group(function () {
            Route::get('/',[UserController::class,'index'])->name('list');
            Route::get('/create',[UserController::class,'create'])->name('create');
            Route::post('/store',[UserController::class,'store'])->name('store');
            Route::get('/{id}/edit', [UserController::class,'edit'])->name('edit');
            Route::post('/{id}/update', [UserController::class,'update'])->name('update');
            Route::delete('/{id}', [UserController::class,'destroy'])->name('destroy');
        });

        Route::prefix('admin/videos')->name('admin.videos.')->group(function () {
            Route::get('/',[VideoController::class,'index'])->name('list');
            Route::get('/create',[VideoController::class,'create'])->name('create');
            Route::post('/upload',[VideoController::class,'upload'])->name('upload');
            Route::get('/{id}/edit',[VideoController::class,'edit'])->name('edit');
            Route::post('/{id}/update',[VideoController::class,'update'])->name('update');
            Route::delete('/{id}',[VideoController::class,'destroy'])->name('destroy');
            Route::get('/{id}/stream',[VideoController::class,'stream'])->name('stream');

        });

        Route::prefix('admin/students')->name('admin.students.')->group(function () {
            Route::get('/', [StudentAdminDashboardController::class, 'index'])->name('list');
            Route::get('/create', [StudentAdminDashboardController::class, 'create'])->name('create');
            Route::post('/store', [StudentAdminDashboardController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [StudentAdminDashboardController::class, 'edit'])->name('edit');
            Route::post('/{id}/update', [StudentAdminDashboardController::class, 'update'])->name('update');
            Route::delete('/{id}', [StudentAdminDashboardController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/video-progress', [StudentAdminDashboardController::class, 'showVideoProgress'])->name('video-progress');
            Route::post('/{studentId}/videos/{videoId}/toggle-retake', [StudentAdminDashboardController::class, 'toggleRetake'])->name('toggle-retake');
        });

        Route::prefix('admin/subjects')->name('admin.subjects.')->group(function () {
            Route::get('/', [SubjectController::class, 'index'])->name('list');
            Route::get('/create', [SubjectController::class, 'create'])->name('create');
            Route::post('/store', [SubjectController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [SubjectController::class, 'edit'])->name('edit');
            Route::post('/{id}/update', [SubjectController::class, 'update'])->name('update');
            Route::delete('/{id}', [SubjectController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('admin/notifications')->name('admin.notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('list');
            Route::post('/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
            Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
            Route::post('/{id}/reply', [NotificationController::class, 'reply'])->name('reply');
            Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-delete', [NotificationController::class, 'bulkDelete'])->name('bulk-delete');
        });
    });

Route::get('admin/videos/progress/{token}', [VideoController::class, 'progress'])->name('admin.progress');


// Student Auth & Dashboard
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/register', [RegistrationController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [RegistrationController::class, 'register'])->name('register.submit');
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    });

    Route::prefix('student')->name('student.')->middleware('auth:student')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/info', [StudentDashboardController::class, 'info'])->name('info');
        Route::post('/info', [StudentDashboardController::class, 'update'])->name('updateInfo');

        // Videos
        Route::get('/videos/list', [StudentVideoController::class, 'list'])->name('videos.list');
        Route::get('/videos/{id}', [StudentVideoController::class, 'index'])->whereNumber('id')->name('videos');
        Route::get('/video-file-size/{id}', [StudentVideoController::class, 'getVideoFileSize'])->name('video.size');
        Route::get('/video-chunk/{id}', [StudentVideoController::class, 'stream'])->name('video.chunk');
        Route::get('/video-stream/{id}', [StudentVideoController::class, 'stream'])->name('video.stream');
        Route::get('/videos/{id}/completion-status', [StudentVideoController::class, 'checkCompletionStatus'])->name('videos.completion-status');
        // Logout
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        // Other pages
        Route::post('/videos/{id}/history', [RegistrationController::class, 'addHistory'])->name('videos.history');
        Route::post('/videos/{id}/complete', [RegistrationController::class, 'markComplete'])->name('videos.complete');
        Route::get('/google-forms', [RegistrationController::class, 'googleForms'])->name('google.forms');
        
        // Comments
        Route::post('/videos/{id}/comments', [CommentController::class, 'store'])->name('comments.store');
        Route::get('/videos/{id}/comments', [CommentController::class, 'index'])->name('comments.index');

    });
    
Route::post('/auto-logout', function () {
    \Auth::logout();
    session()->flush();
    return response()->json(['status' => 'logged_out']);
})->name('auto.logout');