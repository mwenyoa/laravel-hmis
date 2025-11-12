<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class QueuedVerifyEmailNotification extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    public $backoff = [60, 300, 900]; // Retry after 1, 5, 15 minutes

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        $this->onQueue('emails');
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify Your Email Address')
            ->line('Please click the button below to verify your email address.')
            ->action('Verify Email Address', $verificationUrl)
            ->line('If you did not create an account, no further action is required.');
    }

    /**
     * Get the verification URL for the given notifiable.
     */
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

    // Debug: Log what parameters we actually get
    \Log::info('Verification URL parameters:', $params);

    // Construct the frontend verification URL using the correct parameters
    // The 'id' and 'hash' are route parameters, not query parameters
    return $frontendUrl . '/verify-email?' . http_build_query([
        'id' => $notifiable->getKey(), // Get ID directly from user
        'hash' => sha1($notifiable->getEmailForVerification()), // Generate hash directly
        'expires' => $params['expires'],
        'signature' => $params['signature'],
    ]);
}
    /**
     * Handle a job failure.
     */
    public function failed(\Exception $exception): void
    {
        // Log the failure
        \Log::error('Failed to send verification email', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // You could send a notification to admins here
        // or implement a fallback email service
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
