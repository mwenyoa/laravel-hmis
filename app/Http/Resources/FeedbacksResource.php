<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedbacksResource extends JsonResource
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
                "patient_id" => $this->patient_id,
                "message" => $this->message,
                "created_at" => $this->created_at,
            ],
            "relationships" => [
                "patient" => [
                    "data" => [
                        "id" => $this->patient->id,
                        "diagnosis" => $this->patient->diagnosis,
                        "home_address" => $this->patient->home_address,
                    ],
                    "relationships" => [
                        "user" => [
                            "id" => $this->patient->user_id,
                            "firstname" => $this->patient->user->first_name,
                            "lastname" => $this->patient->user->last_name,
                            "photo_url" => $this->patient->user->photo_url,
                            "gender" => $this->patient->user->gender,
                        ],
                    ],
                ],
            ],
        ];
    }
}
