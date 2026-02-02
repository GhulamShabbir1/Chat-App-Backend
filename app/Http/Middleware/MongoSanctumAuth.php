<?php

namespace App\Http\Middleware;

use App\Models\MongoPersonalAccessToken;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MongoSanctumAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Find the token
        $accessToken = MongoPersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Check if token is expired
        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            return response()->json(['message' => 'Token expired.'], 401);
        }

        // Get the user
        $user = User::find($accessToken->tokenable_id);

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
