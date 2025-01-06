<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:30', 'min:3'],
            'last_name' => ['required', 'string', 'max:30', 'min:3'],
            'phoneno' => ['required', 'string', 'max:30', 'min:3'],
            'gender' => ['required', 'string', 'max:30', 'min:3'],
            'age' => ['required', 'integer', 'min:0'],
            'marital_status' => ['required', 'string', 'max:30', 'min:3'],
            'photo_url' => ['required', 'string', 'max:30', 'min:3'],
            'email' => ['required', 'string', 'max:50', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:6']
        ];
    }
}
