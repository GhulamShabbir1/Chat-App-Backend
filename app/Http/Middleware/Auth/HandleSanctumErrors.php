<?php

namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleSanctumErrors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $response = $next($request);
            return $response;
        } catch (\Throwable $e) {
            // Log the error
            \Log::error('Middleware error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Return 401 for auth errors
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
