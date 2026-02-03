<?php

use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

// Protected workspace routes
Route::middleware(['custom.auth'])->group(function () {
    Route::get('/workspaces', [WorkspaceController::class, 'index']);
    Route::post('/workspaces', [WorkspaceController::class, 'store']);
    Route::get('/workspaces/{workspace}', [WorkspaceController::class, 'show'])->middleware('workspace.access');
    Route::put('/workspaces/{workspace}', [WorkspaceController::class, 'update'])->middleware('workspace.access');
    Route::delete('/workspaces/{workspace}', [WorkspaceController::class, 'destroy'])->middleware('workspace.access');
    Route::post('/workspaces/{workspace}/members', [WorkspaceController::class, 'addMember'])->middleware('workspace.access');
    Route::delete('/workspaces/{workspace}/members', [WorkspaceController::class, 'removeMember'])->middleware('workspace.access');
});
