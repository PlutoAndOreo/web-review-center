<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StudentDashboard as StudentAdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\LoginController;
use App\Http\Controllers\Student\CommentController;
use App\Http\Controllers\Student\RegistrationController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\StudentVideoController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\StudentMiddleware;

Route::middleware('guest:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('store');
});

Route::middleware(['auth:admin', AdminMiddleware::class])->group(function () {
    Route::post('/admin/logout', [AuthenticatedSessionController::class, 'logout'])->name('admin.logout');
    Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])->name('logout');
    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');
    
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/',[UserController::class,'index'])->name('list');
        Route::get('/{id}/edit', [UserController::class,'edit']);
        Route::post('/{id}/update', [UserController::class,'update'])->name('update');
    });

    Route::prefix('videos')->name('videos')->group(function () {
        Route::get('/',[VideoController::class,'index'])->name('.list');
        Route::get('/create',[VideoController::class,'create'])->name('.create');
        Route::post('/upload',[VideoController::class,'upload'])->name('.upload');
        Route::get('/{id}/edit',[VideoController::class,'edit'])->name('.edit');
        Route::post('/{id}/update',[VideoController::class,'update'])->name('.update');
        Route::delete('/{id}',[VideoController::class,'destroy'])->name('.destroy');
        Route::get('/{id}/stream',[VideoController::class,'stream'])->name('.stream');

    });

    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/', [StudentAdminDashboardController::class, 'index'])->name('list');
        
    });

    Route::prefix('subjects')->name('subjects.')->group(function () {
        Route::get('/', [SubjectController::class, 'index'])->name('list');
        Route::get('/create', [SubjectController::class, 'create'])->name('create');
        Route::post('/store', [SubjectController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [SubjectController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [SubjectController::class, 'update'])->name('update');
        Route::delete('/{id}', [SubjectController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('list');
        Route::post('/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
    });
});

// Video processing progress
Route::get('/admin/videos/progress/{token}', [VideoController::class, 'progress'])->name('videos.progress');

// Student Auth & Dashboard
Route::prefix('student')->name('student.')->group(function () {
    Route::middleware('guest:student')->group(function () {
        Route::get('register', [RegistrationController::class, 'showRegistrationForm'])->name('register');
        Route::post('register', [RegistrationController::class, 'register'])->name('register.submit');
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login'])->name('login.submit');
    });

    Route::middleware(['auth:student', StudentMiddleware::class])->group(function () {
        Route::get('dashboard', [StudentDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('info', [StudentDashboardController::class, 'info'])->name('info');
        // Videos
        Route::get('videos/{id}', [StudentVideoController::class, 'index'])->name('videos');
        Route::get('video-file-size/{id}', [StudentVideoController::class, 'getVideoFileSize'])->name('video.size');
        Route::get('video-chunk/{id}', [StudentVideoController::class, 'stream'])->name('video.chunk');
        // Logout
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        // Other pages
        Route::post('videos/{id}/history', [RegistrationController::class, 'addHistory'])->name('videos.history');
        Route::get('google-forms', [RegistrationController::class, 'googleForms'])->name('google.forms');
        
        // Comments
        Route::post('videos/{id}/comments', [CommentController::class, 'store'])->name('comments.store');
        Route::get('videos/{id}/comments', [CommentController::class, 'index'])->name('comments.index');
    });
});
