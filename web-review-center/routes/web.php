<?php

use  App\Http\Controllers\Student\StudentVideoController;
use  App\Http\Controllers\Admin\VideoController;
use Illuminate\Support\Facades\Route;


    Route::get('/', [VideoController::class, 'index']);
    Route::post('/upload-video', [VideoController::class, 'upload']);


    Route::get('/video', [StudentVideoController::class, 'index']);
    Route::get('/video-chunk', [StudentVideoController::class, 'stream'])->name('video.show');
    Route::get('/video-file-size', [StudentVideoController::class, 'getVideoFileSize']);
    Route::get('/video/{filename}', [StudentVideoController::class, 'show']);

