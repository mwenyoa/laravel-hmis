<?php

namespace App\Http\Resources;

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
        return  [
            "id" => $this->id,
            "attributes" => [
                "doctor" => [
                    "data" => [
                        "specialization" => $this->specialization,
                        "consultancy_fee" => $this->consultancy_fee,
                        "hpcno" => $this->hpcno,
                        "user_id" => $this->user_id
                    ],
                ]
            ],
            "relationship" => [
                "user" => [
                    "user_id" => $this->user_id,
                    "firstanme" => $this->user->first_name,
                    "lastname" => $this->user->last_name,
                    "age" => $this->user->age,
                    "email" => $this->user->email,
                ]
            ]
        ];
    }
}
