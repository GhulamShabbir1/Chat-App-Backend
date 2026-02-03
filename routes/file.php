<?php

use App\Http\Controllers\FileAttachmentController;
use Illuminate\Support\Facades\Route;

// Protected file routes
Route::middleware(['custom.auth'])->group(function () {
    Route::get('/files', [FileAttachmentController::class, 'index']);
    Route::post('/files/upload', [FileAttachmentController::class, 'upload']);
    Route::get('/files/{file}/download', [FileAttachmentController::class, 'download']);
    Route::delete('/files/{file}', [FileAttachmentController::class, 'destroy']);
});
