<?php

use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

// Protected team routes (flat structure)
Route::middleware(['custom.auth'])->group(function () {
    Route::get('/teams', [TeamController::class, 'index']);
    Route::post('/teams', [TeamController::class, 'store']);
    Route::get('/teams/{team}', [TeamController::class, 'show'])->middleware('team.access');
    Route::put('/teams/{team}', [TeamController::class, 'update'])->middleware('team.access');
    Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->middleware('team.access');
    Route::post('/teams/{team}/members', [TeamController::class, 'addMember'])->middleware('team.access');
    Route::delete('/teams/{team}/members', [TeamController::class, 'removeMember'])->middleware('team.access');
});
