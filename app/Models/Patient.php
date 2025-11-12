<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'blood_type',
        'allergies',
        'current_medications',
        'emergency_contact',
        'user_id',
    ];

    protected $casts = [
        'allergies' => 'array',
        'current_medications' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
