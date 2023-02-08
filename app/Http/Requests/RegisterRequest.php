<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => ['email', 'required', 'unique:users,email'],
            'name' => ['required'],
            'password' => ['required', 'min:8'],
            'confirmation_password' => ['required', 'same:password']
        ];
    }
}
