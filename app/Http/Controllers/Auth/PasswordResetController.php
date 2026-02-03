<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $email = $request->email;
        $token = Str::random(60);

        // Store token in MongoDB
        PasswordResetToken::updateOrCreate(
            ['email' => $email],
            [
                'token' => $token,
                'created_at' => now(),
            ]
        );

        // Send email
        try {
            Mail::to($email)->send(new PasswordResetMail($token, $email));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to send email',
                'details' => $e->getMessage()
            ], 500);
        }

        return response()->json(['message' => 'Password reset link sent to your email']);
    }

    public function resetPassword(Request $request)
    {
        $record = PasswordResetToken::where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return response()->json(['error' => 'Invalid token'], 400);
        }

        // Check if token is expired (e.g., 60 minutes)
        if ($record->created_at->addMinutes(60)->isPast()) {
            $record->delete();
            return response()->json(['error' => 'Token expired'], 400);
        }

        // Update password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete token
        $record->delete();

        return response()->json(['message' => 'Password reset successfully']);
    }
}
