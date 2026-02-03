<?php

use App\Http\Controllers\ChannelController;
use Illuminate\Support\Facades\Route;

// Protected channel routes
Route::middleware(['custom.auth'])->group(function () {
    Route::get('/workspaces/{workspace}/teams/{team}/channels', [ChannelController::class, 'index'])->middleware(['workspace.access', 'team.access']);
    Route::post('/workspaces/{workspace}/teams/{team}/channels', [ChannelController::class, 'store'])->middleware(['workspace.access', 'team.access']);
    Route::get('/workspaces/{workspace}/teams/{team}/channels/{channel}', [ChannelController::class, 'show'])->middleware(['workspace.access', 'team.access', 'channel.access']);
    Route::put('/workspaces/{workspace}/teams/{team}/channels/{channel}', [ChannelController::class, 'update'])->middleware(['workspace.access', 'team.access', 'channel.access']);
    Route::delete('/workspaces/{workspace}/teams/{team}/channels/{channel}', [ChannelController::class, 'destroy'])->middleware(['workspace.access', 'team.access', 'channel.access']);
    Route::post('/workspaces/{workspace}/teams/{team}/channels/{channel}/members', [ChannelController::class, 'addMember'])->middleware(['workspace.access', 'team.access', 'channel.access']);
    Route::delete('/workspaces/{workspace}/teams/{team}/channels/{channel}/members', [ChannelController::class, 'removeMember'])->middleware(['workspace.access', 'team.access', 'channel.access']);
});
