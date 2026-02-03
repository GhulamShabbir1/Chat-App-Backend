<?php

use Illuminate\Support\Facades\Route;

// Debug routes
Route::get('/test', function () {
    return response()->json(['message' => 'Server is working']);
});

Route::middleware(['custom.auth'])->get('/test-auth', function (Illuminate\Http\Request $request) {
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

// Include separate route files
require __DIR__.'/auth.php';
require __DIR__.'/workspace.php';
require __DIR__.'/team.php';
require __DIR__.'/channel.php';
require __DIR__.'/message.php';
require __DIR__.'/file.php';
