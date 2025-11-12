<?php

namespace App\Policies;

use App\Models\Specialty;
use App\Models\User;

class SpecialtyPolicy
{
    public function update(User $user, Specialty $specialty): bool
    {
        if ($user->role === 'admin') {
            return true;
        }
        $specialty->load('doctor');

        return $user->id === $specialty->doctor->user_id;
    }

    public function delete(User $user, Specialty $specialty)
    {
        return $user->id === $specialty->doctor->user_id;
    }
}
