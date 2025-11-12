<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class VerificationEmail extends VerifyEmail
{
    protected function verificationUrl($notifiable)
    {
        // Get the frontend URL from your config
        $frontendUrl = config('app.frontend_url');

        // This is the default signed URL generation in Laravel
        $verificationLink = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        // Parse the signed URL to get the query parameters
        $query = parse_url($verificationLink, PHP_URL_QUERY);
        parse_str($query, $params);

        // Construct the frontend verification URL using the correct parameters
        // Get id and hash directly from the user, not from query params
        return $frontendUrl . '/verify-email?' . http_build_query([
            'id' => $notifiable->getKey(), // Get ID directly from user model
            'hash' => sha1($notifiable->getEmailForVerification()), // Generate hash directly
            'expires' => $params['expires'],
            'signature' => $params['signature'],
        ]);
    }
}