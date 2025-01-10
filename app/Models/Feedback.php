<?php

namespace App\Models;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Feedback extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = [
        "message",
        "patient_id"
    ];


    // model relationships
    public function patient(){
        return $this->belongsTo(Patient::class);
    }
}
