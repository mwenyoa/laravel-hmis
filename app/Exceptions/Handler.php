<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Request;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->expectsJson()) {
                return $this->handleApiException($e);
            }
        });
    }

    /**
     * Handle API exceptions
     */
    private function handleApiException(Throwable $e)
    {
        // Log the error for debugging
        \Log::error('API Exception: ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'exception' => $e
        ]);

        $statusCode = $this->getStatusCode($e);
        $message = $this->getUserFriendlyMessage($e, $statusCode);

        $response = [
            'status' => 'Fail',
            'message' => $message,
        ];

        // Add validation errors if available
        if ($e instanceof \Illuminate\Validation\ValidationException) {
            $response['data'] = $e->errors();
        }

        // Add debug info in development
        if (config('app.debug')) {
            $response['debug'] = [
                'actual_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'exception' => get_class($e),
            ];
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Get appropriate HTTP status code
     */
    private function getStatusCode(Throwable $e): int
    {
        if (method_exists($e, 'getStatusCode')) {
            return $e->getStatusCode();
        }

        if ($e instanceof \Illuminate\Validation\ValidationException) {
            return 422;
        }

        if ($e instanceof \Illuminate\Auth\AuthenticationException) {
            return 401;
        }

        if ($e instanceof \Illuminate\Database\QueryException) {
            return $this->isDatabaseConnectionError($e) ? 503 : 500;
        }

        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ||
            $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return 404;
        }

        if ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
            return 405;
        }

        return 500;
    }

    /**
     * Get user-friendly error message
     */
    private function getUserFriendlyMessage(Throwable $e, int $statusCode): string
    {
        // Database connection errors
        if ($e instanceof \Illuminate\Database\QueryException && $this->isDatabaseConnectionError($e)) {
            return 'Database service unavailable. Please try again later.';
        }

        // Specific exception messages
        if ($e instanceof \Illuminate\Validation\ValidationException) {
            return 'Validation failed.';
        }

        if ($e instanceof \Illuminate\Auth\AuthenticationException) {
            return 'Unauthenticated. Please log in.';
        }

        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return 'The requested resource was not found.';
        }

        // Generic messages based on status code
        $messages = [
            400 => 'Bad request.',
            401 => 'Unauthorized.',
            403 => 'Forbidden.',
            404 => 'Resource not found.',
            405 => 'Method not allowed.',
            422 => 'Validation failed.',
            429 => 'Too many requests. Please try again later.',
            500 => 'Internal server error. Please try again later.',
            503 => 'Service unavailable. Please try again later.',
        ];

        return $messages[$statusCode] ?? 'An unexpected error occurred. Please try again later.';
    }

    /**
     * Check if it's a database connection error
     */
    private function isDatabaseConnectionError(\Illuminate\Database\QueryException $e): bool
    {
        $errorMessage = strtolower($e->getMessage());
        
        $connectionErrors = [
            'connection refused',
            'target machine actively refused',
            'no connection could be made',
            'could not find driver',
            'sqlstate[hy000]',
            '2002',
            '2003',
            '2006',
            '2013'
        ];

        foreach ($connectionErrors as $error) {
            if (str_contains($errorMessage, strtolower($error))) {
                return true;
            }
        }

        return false;
    }
}