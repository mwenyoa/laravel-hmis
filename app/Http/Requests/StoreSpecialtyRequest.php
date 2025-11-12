<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpecialtyRequest extends FormRequest
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
            //
            'doctor_id' => ['required', 'string'],
            'specialization_name' => ['required', 'string', 'unique:specialties'],
            'description' => ['required', 'string', 'min:10'],
            'years_experience' => ['required', 'integer'],
            'certification' => ['string']
        ];
    }
}
