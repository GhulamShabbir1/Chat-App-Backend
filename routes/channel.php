<?php

use App\Http\Controllers\ChannelController;
use Illuminate\Support\Facades\Route;

// Protected channel routes (flat structure)
Route::middleware(['custom.auth'])->group(function () {
    Route::get('/channels', [ChannelController::class, 'index']);
    Route::post('/channels', [ChannelController::class, 'store'])->middleware('validate.channel.store');
    Route::get('/channels/{channel}', [ChannelController::class, 'show']);
    Route::put('/channels/{channel}', [ChannelController::class, 'update']);
    Route::delete('/channels/{channel}', [ChannelController::class, 'destroy']);
    Route::post('/channel-members/{id}', [ChannelController::class, 'addMember']);
    Route::delete('/channel-members/{id}', [ChannelController::class, 'removeMember']);
});
