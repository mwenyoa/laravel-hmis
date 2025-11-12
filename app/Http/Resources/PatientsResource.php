<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PatientsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'blood_type' => $this->blood_type,
            'allergies' => $this->allergies,
            'current_medications' => $this->current_medications,
            'emergency_contact' => $this->emergency_contact,
            'created_at' => $this->created_at,
            'user_id',
            'user' => [
                'user_id' => $this->user->id,
                'firstname' => $this->user->first_name,
                'lastname' => $this->user->last_name,
                'email' => $this->user->email,
                'photo_url' => $this->user->photo_url,
                'phoneno' => $this->user->phoneno,
                'age' => $this->user->age,
                'gender' => $this->user->gender,
            ],
        ];
    }
}
