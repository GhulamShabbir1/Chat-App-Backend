<?php

namespace App\Http\Middleware\File;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ValidateFileUpload
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = $request->file('file');
        $size = $file->getSize();

        // Validate file size (max 10MB)
        if ($size > 10485760) {
            return response()->json(['error' => 'File size exceeds 10MB limit'], 422);
        }

        return $next($request);
    }
}
