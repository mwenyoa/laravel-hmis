<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'blood_type' => ['required', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-,unknown'],
            'allergies' => ['nullable', 'array'],
            'current_medications' => ['nullable', 'array'],
            'emergency_contact' => ['required', 'string', 'min:10', 'max:20'],
            'user_id' => ['required', 'string', 'exists:users,id'],
        ];
    }
}
