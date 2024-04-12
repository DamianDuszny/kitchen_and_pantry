<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function messages(): array
    {
        return [
            'password.required' => 'Hasło jest wymagane',
            'body.required' => 'A message is required',
        ];
    }

    public function rules()
    {
        return [
            'first_name' => 'required|max:20',
            'last_name' => 'required|max:30',
            'email_address' => ['email','max:50', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()]
        ];
    }
}
