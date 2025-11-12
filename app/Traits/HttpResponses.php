<?php

namespace App\Traits;

trait HttpResponses
{
    /**
     * Success response
     */
    protected function success($data, $message = null, $warning = null, $code = 200)
    {
        // Ensure code is always an integer
        $statusCode = (int) $code;
        
        return response()->json([
            'status' => 'Success',
            'message' => $message,
            'warning' => $warning,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Error response
     */
    protected function error($message, $code = 500, $data = null)
    {
        // Ensure code is always an integer and fallback to 500 if invalid
        $statusCode = $this->normalizeStatusCode($code);
        
        return response()->json([
            'status' => 'Fail',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Validation error response - return errors as array of objects
     */
    protected function validationError($errors, $message = 'Validation failed.')
    {
        // Convert Laravel validation errors to array of objects
        $formattedErrors = [];
        
        foreach ($errors as $field => $errorMessages) {
            foreach ($errorMessages as $errorMessage) {
                $formattedErrors[] = [
                    'field' => $field,
                    'message' => $errorMessage
                ];
            }
        }

        return response()->json([
            'status' => 'Fail',
            'message' => $message,
            'errors' => $formattedErrors, // Array of objects
            'data' => $formattedErrors,   // Keep both for compatibility
        ], 422); // Fixed status code for validation errors
    }

    /**
     * Not found response
     */
    protected function notFound($message = 'Resource not found.')
    {
        return response()->json([
            'status' => 'Fail',
            'message' => $message,
        ], 404);
    }

    /**
     * Unauthorized response
     */
    protected function unauthorized($message = 'Unauthorized.')
    {
        return response()->json([
            'status' => 'Fail',
            'message' => $message,
        ], 401);
    }

    /**
     * Normalize status code to ensure it's a valid HTTP status code integer
     */
    private function normalizeStatusCode($code)
    {
        $code = (int) $code;
        
        // If code is 0 or not a valid HTTP status code, default to 500
        if ($code === 0 || $code < 100 || $code > 599) {
            return 500;
        }
        
        return $code;
    }
}