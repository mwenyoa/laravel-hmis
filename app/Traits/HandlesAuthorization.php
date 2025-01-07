<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HandlesAuthorization
{
    protected function ensureAuthenticated()
    {
        if (!Auth::check()) {
            abort(401, 'You must be logged in to access this resource.');
        }
    }

    protected function ensureOwnership($resource)
    {
        dd($resource->user_id);
        if (Auth::id() !== $resource->user_id) {
            abort(403, 'You are not authorized to access this resource.');
        }
    }
}
