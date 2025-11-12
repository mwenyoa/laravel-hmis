<?php

namespace App\Http\Resources;

use App\Http\Resources\SpecialtiesResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorsResource extends JsonResource
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
        'consultancy_fee' => $this->consultancy_fee,
        'hpcno' => $this->hpcno,
        'user_id' => $this->user_id,
        'experience' => $this->experience,
        'availability' => $this->availability,
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
        'user' => [
            'user_id' => $this->user_id,
            'firstname' => $this->user->first_name,
            'lastname' => $this->user->last_name,
            'age' => $this->user->age,
            'email' => $this->user->email,
            'phone' => $this->user->phoneno,
            'photo_url' => $this->user->photo_url,
        ],
        'specialties' => SpecialtiesResource::collection($this->specialties),
    ];
}
}
