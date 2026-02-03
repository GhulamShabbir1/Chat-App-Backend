<?php

use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

// Protected message routes (flattened)
Route::middleware(['custom.auth'])->group(function () {
    // Channel messages (flattened)
    Route::get('/channel-messages', [MessageController::class, 'indexChannelMessages']);
    Route::post('/channel-messages', [MessageController::class, 'storeChannelMessage']);

    // Direct messages
    Route::get('/messages/direct', [MessageController::class, 'indexDirectMessages']);
    Route::post('/messages/direct', [MessageController::class, 'storeDirectMessage']);

    // Update and Delete messages
    Route::put('/messages/{message}', [MessageController::class, 'update']);
    Route::delete('/messages/{message}', [MessageController::class, 'destroy']);
});
