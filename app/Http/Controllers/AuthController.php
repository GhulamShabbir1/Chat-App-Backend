<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Resources\UserResource;
use App\Jobs\SendWelcomeEmail;
use App\Mail\PasswordResetMail;
use App\Models\CustomAccessToken;
use App\Models\PasswordResetToken;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function sendOtp(SendOtpRequest $request)
    {
        $email = $request->email;

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            return response()->json(['error' => 'User already exists'], 409);
        }

        // Generate 6-digit OTP
        $otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in database with expiration (5 minutes)
        OtpToken::updateOrCreate(
            ['email' => $email],
            [
                'otp' => $otp,
                'expires_at' => now()->addMinutes(5),
            ]
        );

        // Send OTP email
        try {
            Mail::to($email)->send(new OtpMail($otp, $email));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to send OTP email',
                'details' => $e->getMessage()
            ], 500);
        }

        return response()->json(['message' => 'OTP sent to your email']);
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $email = $request->email;
        $otp = $request->otp;

        // Find OTP record
        $otpRecord = OtpToken::where('email', $email)->first();

        if (!$otpRecord) {
            return response()->json(['error' => 'OTP not found'], 400);
        }

        // Check if OTP matches
        if ($otpRecord->otp !== $otp) {
            return response()->json(['error' => 'Invalid OTP'], 400);
        }

        // Check if OTP is expired
        if ($otpRecord->expires_at->isPast()) {
            $otpRecord->delete();
            return response()->json(['error' => 'OTP expired'], 400);
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $email,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        // Delete OTP record
        $otpRecord->delete();

        // Dispatch welcome email job
        // SendWelcomeEmail::dispatch($user);

        // Send welcome notification
        // $user->notify(new WelcomeNotification());

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

