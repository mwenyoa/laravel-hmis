<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Doctor extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        "specialization",
        "hpcno",
        "consultancy_fee"
    ];

    // relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
