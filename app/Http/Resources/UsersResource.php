<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UsersResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phoneno' => $this->phoneno,
            'photo_url' => $this->photo_url ? Storage::url($this->photo_url) : null,
            'gender' => $this->gender,
            'age' => $this->age,
            'created_at' => $this->created_at,
            'marital_status' => $this->marital_status,
            'email_verified_at' => $this->emil_verified_at,
            'doctor' => [
                'id' => $this->id,
                'consultancy_fee' => $this->consultancy_fee,
                'hpcno' => $this->hpcno,
                'user_id' => $this->user_id,
                'experience' => $this->experience,
                'availability' => $this->availability,
                'created_at' => $this->created_at,
            ],
        ];
    }
}
