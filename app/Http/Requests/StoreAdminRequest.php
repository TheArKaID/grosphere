<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreAdminRequest extends FormRequest
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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => [
                'nullable', 'email',
                function ($attribute, $value, $fail) {
                    if ($value && $this->username) {
                        $fail('The username field must be null when email is provided.');
                    }
                },
                'unique:users,email'
            ],
            'username' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (!$this->email && !$value) {
                        $fail('The username field is required when email is not provided.');
                    }
                    if ($this->email && $value) {
                        $fail('The username field must be null when email is provided.');
                    }
                },
                'required_without:email', 'unique:users,username'
            ],
            'username' => 'required_without:email|nullable|max:255|unique:users,username',
            'password' => ['required', 'confirmed', 'string', Password::min(8)->letters()->numbers()->mixedCase()],
            'photo' => 'required|string',
        ];
    }

    /**
     * Messages for validation rules.
     * 
     * @return array<string, string>
     */
    public function messages(): array
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
