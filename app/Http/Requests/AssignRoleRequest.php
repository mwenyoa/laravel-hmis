<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignRoleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'role' => [
                'required',
                'string',
                Rule::in(['doctor', 'patient']),
            ],
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'User ID is required',
            'user_id.exists' => 'User not found',
            'role.required' => 'Role is required',
            'role.in' => 'Role must be either doctor or patient',
        ];
    }
}
