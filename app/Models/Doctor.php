<?php

namespace App\Models;

use App\Models\Specialty;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'hpcno',
        'consultancy_fee',
        'user_id',
        'experience',
        'availability',
    ];

    // relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function specialties()
    {
        return $this->hasMany(Specialty::class);
    }

    public function awards()
    {
        return $this->hasMany(Award::class);
    }
}
