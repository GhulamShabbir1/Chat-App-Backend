<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\FileAttachmentController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

// Debug routes
Route::get('/test', function () {
    return response()->json(['message' => 'Server is working']);
});

Route::middleware(['auth:sanctum'])->get('/test-auth', function (Illuminate\Http\Request $request) {
    try {
        $user = $request->user();
        return response()->json([
            'message' => 'Auth is working',
            'user' => $user
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

// Protected routes
Route::middleware(['mongo.auth'])->group(function () {

    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    // Workspace routes
    Route::get('/workspaces', [WorkspaceController::class, 'index']);
    Route::post('/workspaces', [WorkspaceController::class, 'store']);
    Route::get('/workspaces/{workspace}', [WorkspaceController::class, 'show'])->middleware('workspace.access');
    Route::put('/workspaces/{workspace}', [WorkspaceController::class, 'update'])->middleware('workspace.access');
    Route::delete('/workspaces/{workspace}', [WorkspaceController::class, 'destroy'])->middleware('workspace.access');
    Route::post('/workspaces/{workspace}/members', [WorkspaceController::class, 'addMember'])->middleware('workspace.access');
    Route::delete('/workspaces/{workspace}/members', [WorkspaceController::class, 'removeMember'])->middleware('workspace.access');

    // Team routes
    Route::get('/workspaces/{workspace}/teams', [TeamController::class, 'index'])->middleware('workspace.access');
    Route::post('/workspaces/{workspace}/teams', [TeamController::class, 'store'])->middleware('workspace.access');
    Route::get('/workspaces/{workspace}/teams/{team}', [TeamController::class, 'show'])->middleware(['workspace.access', 'team.access']);
    Route::put('/workspaces/{workspace}/teams/{team}', [TeamController::class, 'update'])->middleware(['workspace.access', 'team.access']);
    Route::delete('/workspaces/{workspace}/teams/{team}', [TeamController::class, 'destroy'])->middleware(['workspace.access', 'team.access']);
    Route::post('/workspaces/{workspace}/teams/{team}/members', [TeamController::class, 'addMember'])->middleware(['workspace.access', 'team.access']);
    Route::delete('/workspaces/{workspace}/teams/{team}/members', [TeamController::class, 'removeMember'])->middleware(['workspace.access', 'team.access']);

    // Channel routes
    Route::get('/workspaces/{workspace}/teams/{team}/channels', [ChannelController::class, 'index'])->middleware(['workspace.access', 'team.access']);
    Route::post('/workspaces/{workspace}/teams/{team}/channels', [ChannelController::class, 'store'])->middleware(['workspace.access', 'team.access']);
    Route::get('/workspaces/{workspace}/teams/{team}/channels/{channel}', [ChannelController::class, 'show'])->middleware(['workspace.access', 'team.access', 'channel.access']);
    Route::put('/workspaces/{workspace}/teams/{team}/channels/{channel}', [ChannelController::class, 'update'])->middleware(['workspace.access', 'team.access', 'channel.access']);
    Route::delete('/workspaces/{workspace}/teams/{team}/channels/{channel}', [ChannelController::class, 'destroy'])->middleware(['workspace.access', 'team.access', 'channel.access']);
    Route::post('/workspaces/{workspace}/teams/{team}/channels/{channel}/members', [ChannelController::class, 'addMember'])->middleware(['workspace.access', 'team.access', 'channel.access']);
    Route::delete('/workspaces/{workspace}/teams/{team}/channels/{channel}/members', [ChannelController::class, 'removeMember'])->middleware(['workspace.access', 'team.access', 'channel.access']);

    // Message routes - Channel messages
    Route::get('/workspaces/{workspace}/teams/{team}/channels/{channel}/messages', [MessageController::class, 'indexChannelMessages'])->middleware(['workspace.access', 'team.access', 'channel.access']);
    Route::post('/workspaces/{workspace}/teams/{team}/channels/{channel}/messages', [MessageController::class, 'storeChannelMessage'])->middleware(['workspace.access', 'team.access', 'channel.access']);

    // Message routes - Direct messages
    Route::get('/messages/direct', [MessageController::class, 'indexDirectMessages']);
    Route::post('/messages/direct', [MessageController::class, 'storeDirectMessage']);

    // Message routes - Update and Delete
    Route::put('/messages/{message}', [MessageController::class, 'update']);
    Route::delete('/messages/{message}', [MessageController::class, 'destroy']);

    // File attachment routes
    Route::get('/files', [FileAttachmentController::class, 'index']);
    Route::post('/files/upload', [FileAttachmentController::class, 'upload']);
    Route::get('/files/{file}/download', [FileAttachmentController::class, 'download']);
    Route::delete('/files/{file}', [FileAttachmentController::class, 'destroy']);
});

