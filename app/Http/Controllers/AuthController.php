<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Mail\PasswordResetMail;
use App\Models\CustomAccessToken;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\WelcomeEmail($user));

        $token = $user->createCustomToken();

        return response()->json([
            'message' => 'User registered successfully',
            'user' => new UserResource($user),
            'user_id' => (string) $user->_id,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $user = User::verifyCredentials($request->email, $request->password);

        if (!$user) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $token = $user->createCustomToken();

        return response()->json([
            'message' => 'Login successful',
            'user' => new UserResource($user),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        if ($token) {
            CustomAccessToken::where('token', $token)->delete();
        }

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'user' => new UserResource($request->user()),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $user->updateProfile($request->validated());

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => new UserResource($user),
        ]);
    }

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

