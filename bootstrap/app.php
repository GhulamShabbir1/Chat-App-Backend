<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'workspace.access' => \App\Http\Middleware\CheckWorkspaceAccess::class,
            'team.access' => \App\Http\Middleware\CheckTeamAccess::class,
            'channel.access' => \App\Http\Middleware\CheckChannelAccess::class,
            'custom.auth' => \App\Http\Middleware\CustomAuthMiddleware::class,
            'validate.workspace.store' => \App\Http\Middleware\ValidateWorkspaceStore::class,
            'validate.team.store' => \App\Http\Middleware\ValidateTeamStore::class,
            'validate.channel.store' => \App\Http\Middleware\ValidateChannelStore::class,
            'validate.file.upload' => \App\Http\Middleware\ValidateFileUpload::class,
            'validate.forgot.password' => \App\Http\Middleware\ValidateForgotPassword::class,
            'validate.reset.password' => \App\Http\Middleware\ValidateResetPassword::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                // Handle ValidationException
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validation failed',
                        'errors' => $e->errors(),
                    ], 422);
                }

                // Handle ModelNotFoundException
                if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Resource not found',
                    ], 404);
                }

                // Handle AuthenticationException
                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Unauthenticated',
                    ], 401);
                }

                // Handle AuthorizationException
                if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Forbidden',
                    ], 403);
                }

                // Handle HttpException
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                    return response()->json([
                        'status' => false,
                        'message' => $e->getMessage() ?: 'HTTP error',
                    ], $e->getStatusCode());
                }

                // Handle general Throwable
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }
        });
    })->create();
