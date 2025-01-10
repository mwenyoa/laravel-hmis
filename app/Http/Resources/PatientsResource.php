<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            "id" => $this->id,
            "attributes" => [
                "user_id" => $this->user_id,
                "diagnosis" => $this->diagnosis,
                "address" => $this->home_address,
                "created_on" => $this->created_at,
            ],
            "relationships" => [
                "user" => [
                    "data" => [
                        "user_id" => $this->user->id,
                        "firstname" => $this->user->first_name,
                        "lastname" => $this->user->last_name,
                        "email" => $this->user->email,
                        "photo_url" => $this->user->photo_url,
                        "phoneno" => $this->user->phoneno,
                    ],
                ],
            ],
        ];
    }
}
