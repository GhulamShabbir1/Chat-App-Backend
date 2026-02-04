<?php

use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

// Protected workspace routes (flat structure)
Route::middleware(['custom.auth'])->group(function () {
    Route::get('/workspaces', [WorkspaceController::class, 'index']);
    Route::post('/workspaces', [WorkspaceController::class, 'store']);
    Route::get('/workspaces/{workspace}', [WorkspaceController::class, 'show']);
    Route::put('/workspaces/{workspace}', [WorkspaceController::class, 'update']);
    Route::delete('/workspaces/{workspace}', [WorkspaceController::class, 'destroy']);
    Route::post('/workspace-members', [WorkspaceController::class, 'addMember']);
    Route::delete('/workspace-members', [WorkspaceController::class, 'removeMember']);
});
