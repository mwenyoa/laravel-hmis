<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentsResource extends JsonResource
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
            'attributes' => [
                'patient_id' => $this->patient_id,
                'doctor_id' => $this->doctor_id,
                'appointment_date' => $this->appointment_date,
                'appointment_time' => $this->appointment_time,
                'purpose' => $this->purpose,
                'created_on' => $this->created_at,
            ],
            'relationships' => [
                'patient' => [
                    'data' => [
                        'id' => $this->patient->id,
                        'diagnosis' => $this->patient->diagnosis,
                        'home_address' => $this->patient->home_address,
                        'created' => $this->patient->created_at,
                        'user' => [
                            'firstname' => $this->patient->user->first_name,
                            'lastname' => $this->patient->user->last_name,
                            'email' => $this->patient->user->email,
                            'phoneno' => $this->patient->user->phoneno,
                            'photo_url' => $this->patient->user->photo_url,
                            'gender' => $this->patient->user->gender,
                        ],
                    ],
                ],
                'doctor' => [
                    'data' => [
                        'id' => $this->doctor->id,
                        'specialization' => $this->doctor->specialization,
                        'hpcno' => $this->doctor->hpcno,
                        'consultancy_fee' => $this->doctor->consultancy_fee,
                        'user_id' => $this->doctor->user_id,
                        'created_on' => $this->doctor->created_at,
                        'user' => [
                            'firstname' => $this->doctor->user->first_name,
                            'lastname' => $this->doctor->user->last_name,
                            'email' => $this->doctor->user->email,
                            'phoneno' => $this->doctor->user->phoneno,
                            'photo_url' => $this->doctor->user->photo_url,
                            'gender' => $this->doctor->user->gender,
                        ],
                    ],
                ],
            ],
        ];
    }
}
