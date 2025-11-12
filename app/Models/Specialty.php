<?php

namespace App\Models;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'doctor_id',
        'specialization_name',
        'description',
        'years_experience',
        'certification',
    ];

    // relationships
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
