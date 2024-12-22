<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
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
            'specialization' => ['required', 'string', 'max:30', 'min:3'],
            'hpcno' => ['required', 'string', 'max:30', 'min:3'],
            'consultancy_fee' => ['required', 'float', 'max:30', 'min:3'],
        ];
    }
}
