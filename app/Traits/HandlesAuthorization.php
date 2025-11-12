<?php

namespace App\Traits;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

trait HandlesAuthorization
{
    use HttpResponses;

    protected function ensureAuthenticated(): void
    {
        if (! Auth::check()) {
            $this->abortWithError(401, 'You must be logged in to access this resource.');
        }
    }

    protected function ensureOwnership($resource): void
    {
        $userId = Auth::id();

        // Check multiple ownership scenarios
        $isOwner = $this->checkOwnershipScenarios($resource, $userId);

        if (! $isOwner) {
            $this->abortWithError(403, 'You are not authorized to access this resource.');
        }
    }

    protected function ensureAdmin(): void
    {
        $user = Auth::user();

        // Use role-based check instead of is_admin property
        if (! $user || ! $user->hasRole('admin')) {
            $this->abortWithError(403, 'Administrator access required.');
        }
    }

    protected function ensureDoctorOrAdmin(): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->abortWithError(401, 'You must be logged in to access this resource.');
        }

        // Use role-based checks
        if (! $user->hasRole('doctor') && ! $user->hasRole('admin')) {
            $this->abortWithError(403, 'Doctor or administrator access required.');
        }
    }

    /**
     * Check multiple ownership scenarios with UUID support
     */
    private function checkOwnershipScenarios($resource, $userId): bool
    {
        // Convert both to string for consistent comparison (UUIDs are strings)
        $userId = (string) $userId;

        // Scenario 1: Direct ownership (for Patient model)
        if (isset($resource->user_id)) {
            $resourceUserId = (string) $resource->user_id;
            if ($userId === $resourceUserId) {
                return true;
            }
        }

        // Scenario 2: User relationship ownership (for nested resources)
        if (isset($resource->user) && $resource->user) {
            $resourceUserId = (string) $resource->user->id;
            if ($userId === $resourceUserId) {
                return true;
            }
        }

        // Scenario 3: Doctor ownership (for doctor-related resources)
        if (isset($resource->doctor) && $resource->doctor) {
            $doctorUserId = (string) $resource->doctor->user_id;
            if ($userId === $doctorUserId) {
                return true;
            }
        }

        // Scenario 4: Doctor relationship method
        if (method_exists($resource, 'doctor')) {
            $doctor = $resource->doctor;
            if ($doctor && isset($doctor->user_id)) {
                $doctorUserId = (string) $doctor->user_id;
                if ($userId === $doctorUserId) {
                    return true;
                }
            }
        }

        // Scenario 5: Check if resource is the user itself
        if (isset($resource->id)) {
            $resourceId = (string) $resource->id;
            if ($userId === $resourceId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Abort with formatted error response using HttpResponseException
     */
    private function abortWithError(int $statusCode, string $message): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => $message,
                'status' => $statusCode,
            ], $statusCode)
        );
    }
}
