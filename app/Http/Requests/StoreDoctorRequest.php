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
            'hpcno' => ['required', 'unique:doctors', 'string', 'max:10', 'min:4'],
            'consultancy_fee' => ['required', 'integer'],
            'user_id' => ['required', 'string', 'unique:doctors'],
            'experience' => ['required', 'integer'],
            'availability' => ['required', 'string'],
        ];
    }
}
