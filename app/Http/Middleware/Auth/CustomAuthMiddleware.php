<?php

namespace App\Http\Middleware\Auth;

use App\Models\CustomAccessToken;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Find the token
        $accessToken = CustomAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Check if token is expired
        if ($accessToken->isExpired()) {
            return response()->json(['message' => 'Token expired.'], 401);
        }

        // Get the user
        $user = $accessToken->user;

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 401);
        }

        // Set the user on the request
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        // Update last_used_at
        $accessToken->last_used_at = now();
        $accessToken->save();

        return $next($request);
    }
}
