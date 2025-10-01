<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\ForgotPasswordRequest;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Http\Resources\AuthResource;
use App\Http\Resources\UserResource;

class AuthController extends BaseController
{
    /**
     * Register a new user
     * 
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        try {
            // Check if registration is enabled
            if (config('settings.registration_active') == 0) {
                return $this->errorResponse('Registration is currently disabled', null, 403, 'REGISTRATION_DISABLED');
            }

            // Create user
            $user = new User();
            $user->username = strtolower($request->username);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->status = 'active';
            $user->permission = 'all';
            $user->ip = $request->ip();
            $user->token = str_random(75);
            $user->confirmation_code = str_random(100);
            
            if (config('settings.email_verification') == 'on') {
                $user->status = 'pending';
            }

            $user->save();

            // Create token
            $token = $user->createToken('auth_token', ['user:read', 'user:write'])->plainTextToken;

            // Send verification email if enabled
            if (config('settings.email_verification') == 'on') {
                try {
                    $user->notify(new \App\Notifications\VerifyEmail($user));
                } catch (\Exception $e) {
                    \Log::error('Failed to send verification email: ' . $e->getMessage());
                }
            }

            return $this->successResponse(
                new AuthResource($user, $token),
                'Registration successful',
                201
            );

        } catch (\Exception $e) {
            \Log::error('Registration error: ' . $e->getMessage());
            return $this->errorResponse('Registration failed', null, 500, 'REGISTRATION_ERROR');
        }
    }

    /**
     * Login user
     * 
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        // Find user
        $user = User::where('email', $request->email)->first();

        // Check credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid credentials', null, 401, 'INVALID_CREDENTIALS');
        }

        // Check user status
        if ($user->status === 'suspended') {
            return $this->errorResponse('Account suspended', null, 403, 'ACCOUNT_SUSPENDED');
        }

        if ($user->status === 'pending' && config('settings.email_verification') == 'on') {
            return $this->errorResponse('Email not verified', null, 403, 'EMAIL_NOT_VERIFIED');
        }

        // Revoke existing tokens (optional - force single session)
        // $user->tokens()->delete();

        // Create token with abilities
        $abilities = ['user:read', 'user:write', 'messages:read', 'messages:write'];
        
        if ($user->verified_id === 'yes') {
            $abilities[] = 'creator:access';
        }

        $token = $user->createToken('auth_token', $abilities)->plainTextToken;

        return $this->successResponse(
            new AuthResource($user, $token),
            'Login successful'
        );
    }

    /**
     * Logout user (revoke current token)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logout successful');
    }

    /**
     * Refresh token (revoke old, create new)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        $user = $request->user();

        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        // Create new token
        $abilities = $request->user()->currentAccessToken()->abilities ?? ['user:read', 'user:write'];
        $token = $user->createToken('auth_token', $abilities)->plainTextToken;

        return $this->successResponse(
            new AuthResource($user, $token),
            'Token refreshed successfully'
        );
    }

    /**
     * Send password reset link
     * 
     * @param ForgotPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return $this->successResponse(null, 'Password reset link sent to your email');
            }

            return $this->errorResponse('Unable to send reset link', null, 500, 'RESET_LINK_ERROR');

        } catch (\Exception $e) {
            \Log::error('Forgot password error: ' . $e->getMessage());
            return $this->errorResponse('Failed to send reset link', null, 500, 'RESET_LINK_ERROR');
        }
    }

    /**
     * Reset password
     * 
     * @param ResetPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->save();
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                return $this->successResponse(null, 'Password reset successful');
            }

            return $this->errorResponse('Invalid or expired reset token', null, 400, 'INVALID_TOKEN');

        } catch (\Exception $e) {
            \Log::error('Reset password error: ' . $e->getMessage());
            return $this->errorResponse('Failed to reset password', null, 500, 'RESET_PASSWORD_ERROR');
        }
    }

    /**
     * Verify email
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $user = User::where('confirmation_code', $request->token)->first();

        if (!$user) {
            return $this->errorResponse('Invalid verification token', null, 400, 'INVALID_TOKEN');
        }

        if ($user->status === 'active') {
            return $this->errorResponse('Email already verified', null, 400, 'ALREADY_VERIFIED');
        }

        $user->status = 'active';
        $user->confirmation_code = null;
        $user->save();

        return $this->successResponse(
            new UserResource($user),
            'Email verified successfully'
        );
    }

    /**
     * Resend verification email
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendVerification(Request $request)
    {
        $user = $request->user();

        if ($user->status === 'active') {
            return $this->errorResponse('Email already verified', null, 400, 'ALREADY_VERIFIED');
        }

        if (!$user->confirmation_code) {
            $user->confirmation_code = str_random(100);
            $user->save();
        }

        try {
            $user->notify(new \App\Notifications\VerifyEmail($user));
            return $this->successResponse(null, 'Verification email sent');
        } catch (\Exception $e) {
            \Log::error('Resend verification error: ' . $e->getMessage());
            return $this->errorResponse('Failed to send verification email', null, 500, 'VERIFICATION_EMAIL_ERROR');
        }
    }
}

