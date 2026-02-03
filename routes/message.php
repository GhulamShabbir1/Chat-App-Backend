<?php

use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

// Protected message routes
Route::middleware(['custom.auth'])->group(function () {
    // Channel messages
    Route::get('/workspaces/{workspace}/teams/{team}/channels/{channel}/messages', [MessageController::class, 'indexChannelMessages'])->middleware(['workspace.access', 'team.access', 'channel.access']);
    Route::post('/workspaces/{workspace}/teams/{team}/channels/{channel}/messages', [MessageController::class, 'storeChannelMessage'])->middleware(['workspace.access', 'team.access', 'channel.access']);

    // Direct messages
    Route::get('/messages/direct', [MessageController::class, 'indexDirectMessages']);
    Route::post('/messages/direct', [MessageController::class, 'storeDirectMessage']);

    // Update and Delete messages
    Route::put('/messages/{message}', [MessageController::class, 'update']);
    Route::delete('/messages/{message}', [MessageController::class, 'destroy']);
});
