<?php

namespace App\Providers;

use App\Models\Specialty;
use App\Notifications\VerificationEmail;
use App\Policies\SpecialtyPolicy;
use Exception;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\Messages\MailMessage;

class AuthServiceProvider extends ServiceProvider
{
     protected $policies = [
        Specialty::class => SpecialtyPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
        // password reset
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        VerificationEmail::toMailUsing(function (object $notifiable, string $url) {
            $parsedUrl = parse_url($url);

            // Check if path exists and has enough segments
            if (! isset($parsedUrl['path'])) {
                throw new Exception('Invalid URL: missing path');
            }

            $pathSegments = explode('/', trim($parsedUrl['path'], '/'));
            if (count($pathSegments) < 4) {
                throw new Exception('Invalid URL structure. Expected at least 4 segments.');
            }

            // Arrays are 0-indexed, so:
            // [0] = 'email'
            // [1] = 'verify'
            // [2] = id (third segment)
            // [3] = hash (fourth segment)
            $id = $pathSegments[2];
            $hash = $pathSegments[3];

            // Parse query parameters
            $queryParams = [];
            if (isset($parsedUrl['query'])) {
                parse_str($parsedUrl['query'], $queryParams);
            }

            if (! isset($queryParams['signature'])) {
                throw new Exception('Missing signature parameter in URL.');
            }

            if (! isset($queryParams['expires'])) {
                throw new Exception('Missing expires parameter in URL.');
            }

            // Use the notifiable's ID instead of the one from URL for consistency
            $frontendUrl = config('app.frontend_url').'/signup/email-verification/verify?'.http_build_query([
                'id' => $notifiable->getKey(), // Use the notifiable's ID
                'hash' => $hash,
                'expires' => $queryParams['expires'],
                'signature' => $queryParams['signature'],
            ]);

            return (new MailMessage)
                ->subject('Verify Email Address')
                ->line('Click the button below to verify your email address.')
                ->action('Verify Email Address', $frontendUrl);
        });

        //
    }
}
