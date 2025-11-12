<?php

namespace App\Models;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Notifications\QueuedVerifyEmailNotification;
use App\Notifications\VerificationEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phoneno',
        'photo_url',
        'email',
        'gender',
        'age',
        'marital_status',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // model relationships
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    // email queueing
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new QueuedVerifyEmailNotification);
    }
}
