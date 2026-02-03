<?php

use App\Http\Controllers\ChannelController;
use Illuminate\Support\Facades\Route;

// Protected channel routes (flat structure)
Route::middleware(['custom.auth'])->group(function () {
    Route::get('/channels', [ChannelController::class, 'index']);
    Route::post('/channels', [ChannelController::class, 'store']);
    Route::get('/channels/{channel}', [ChannelController::class, 'show'])->middleware('channel.access');
    Route::put('/channels/{channel}', [ChannelController::class, 'update'])->middleware('channel.access');
    Route::delete('/channels/{channel}', [ChannelController::class, 'destroy'])->middleware('channel.access');
    Route::post('/channels/{channel}/members', [ChannelController::class, 'addMember'])->middleware('channel.access');
    Route::delete('/channels/{channel}/members', [ChannelController::class, 'removeMember'])->middleware('channel.access');
});
