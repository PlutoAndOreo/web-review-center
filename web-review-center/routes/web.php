<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VideoController;
use Illuminate\Support\Facades\Route;


// Auth (migrated from auth.php)
Route::middleware('guest:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('store');
});

Route::middleware('auth:admin')->group(function () {
    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/',[UserController::class,'index'])->name('list');
        Route::get('/{id}/update', [UserController::class,'update'])->name('update');
    });

    Route::prefix('videos')->name('videos')->group(function () {
        Route::get('/',[VideoController::class,'index'])->name('.list');
        Route::get('/create',[VideoController::class,'create'])->name('.create');
        Route::post('/upload',[VideoController::class,'upload'])->name('.upload');
        Route::get('/{id}/edit',[VideoController::class,'edit'])->name('.edit');
        Route::post('/{id}/update',[VideoController::class,'update'])->name('.update');
        Route::delete('/{id}',[VideoController::class,'destroy'])->name('.destroy');
    });
});

// Video processing progress
Route::get('/admin/videos/progress/{token}', [VideoController::class, 'progress'])->name('videos.progress');
