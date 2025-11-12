<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class EmailVerificationController extends Controller
{
    /**
     * Handle the email verification request from the signed URL.
     * The `signed` middleware on the route automatically validates the URL.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @param  string  $hash
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request, $id, $hash)
    {
        // Find the user by ID or fail
        $user = User::findOrFail($id);

        // Check if the hash matches the user's email hash
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json([
                'message' => 'Invalid verification link',
                'verified' => false,
                'expired' => true,
                'action_required' => 'resend'
            ], 400);
        }

        // Check if the email is already verified
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified',
                'verified' => true,
                'expired' => false
            ], 200);
        }

        // Mark the email as verified and dispatch the Verified event
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json([
            'message' => 'Email successfully verified',
            'verified' => true,
            'expired' => false
        ]);
    }

    /**
     * Handle expired verification links and allow users to request new ones
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleExpiredLink(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User with this email address not found',
                'expired' => true,
                'action_required' => 'register'
            ], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email is already verified',
                'verified' => true,
                'expired' => false
            ], 400);
        }

        // Check if user has recently requested verification emails (max 3 in 1 hour)
        if ($this->verificationEmailsRecentlySent($user) >= 3) {
            return response()->json([
                'message' => 'Too many verification attempts. Please try again in 1 hour.',
                'expired' => true,
                'action_required' => 'wait',
                'retry_after' => 3600 // 1 hour in seconds
            ], 429);
        }

        // Send new verification email
        $user->sendEmailVerificationNotification();
        
        // Increment the verification email counter
        $this->incrementVerificationEmailCount($user);

        return response()->json([
            'message' => 'New verification link sent to your email',
            'verified' => false,
            'expired' => false,
            'email' => $user->email,
            'resend_count' => $this->verificationEmailsRecentlySent($user)
        ], 200);
    }

    /**
     * Get the email verification status for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        
        return response()->json([
            'message' => $user->hasVerifiedEmail() ? 'Email already verified' : 'Email verification required',
            'verified' => $user->hasVerifiedEmail(),
            'email' => $user->email,
            'resend_url' => '/api/email/verification-notification',
            'expired_link_url' => '/api/email/verification/expired'
        ]);
    }

    /**
     * Resend the email verification notification for authenticated users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified',
                'verified' => true
            ], 400);
        }

        // Check rate limiting for authenticated users (max 5 in 1 hour)
        if ($this->verificationEmailsRecentlySent($user) >= 5) {
            return response()->json([
                'message' => 'Too many verification attempts. Please try again in 30 minutes.',
                'retry_after' => 1800 // 30 minutes
            ], 429);
        }

        $user->sendEmailVerificationNotification();
        
        // Increment the verification email counter
        $this->incrementVerificationEmailCount($user);

        return response()->json([
            'message' => 'Verification link sent to your email',
            'verified' => false,
            'email' => $user->email,
            'resend_count' => $this->verificationEmailsRecentlySent($user)
        ]);
    }

    /**
     * Track verification email attempts and check rate limiting
     *
     * @param  \App\Models\User  $user
     * @return int
     */
    private function verificationEmailsRecentlySent(User $user): int
    {
        $key = 'verification_emails_sent_' . $user->id;
        return Cache::get($key, 0);
    }

    /**
     * Increment verification email counter
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    private function incrementVerificationEmailCount(User $user): void
    {
        $key = 'verification_emails_sent_' . $user->id;
        $count = Cache::get($key, 0);
        Cache::put($key, $count + 1, now()->addHours(1)); // Count resets after 1 hour
    }

    /**
     * Override the default sendEmailVerificationNotification to track attempts
     * This method can be called if you want to manually send with tracking
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    private function sendVerificationEmailWithTracking(User $user): void
    {
        $this->incrementVerificationEmailCount($user);
        $user->sendEmailVerificationNotification();
    }
}