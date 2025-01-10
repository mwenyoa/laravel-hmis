<?php
namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait DebugError
{
    public function debugAppError($e)
    {
        if (config('app.debug')) {
            // Log the exception details
            Log::error('Exception caught', [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
