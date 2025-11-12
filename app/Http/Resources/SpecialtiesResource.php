<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecialtiesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'doctor_id' => $this->doctor_id,
            'specialty_name' => $this->specialization_name,
            'description' => $this->description,
            'experience_in_years' => $this->years_experience,
            'certification' => $this->certification,
            'created_on' => $this->created_at,
        ];
    }
}
