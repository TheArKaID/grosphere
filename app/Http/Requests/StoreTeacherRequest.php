<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreTeacherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['sometimes', 'email', 'unique:users,email'],
            'username' => 'required_without:email|string|max:255|unique:users,username',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()->mixedCase()],
            'phone' => 'nullable|string|min:8|max:50',
            'photo' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'password.confirmed' => 'Password confirmation does not match',
            'password.min' => 'Password must be at least 8 characters',
            'password.letters' => 'Password must contain at least one letter',
            'password.numbers' => 'Password must contain at least one number',
            'password.mixed' => 'Password must contain at least one uppercase and one lowercase letter',
        ];
    }
}
