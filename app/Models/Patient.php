<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        "home_address",
        "diagnosis",
        "user_id"
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
