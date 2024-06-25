<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateGuardianRequest extends FormRequest
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
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => [
                'nullable', 'email',
                function ($attribute, $value, $fail) {
                    if ($value && $this->username) {
                        $fail('The username field must be null when email is provided.');
                    }
                },
                'unique:users,email,' . $this->guardian->user_id
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
                'required_without:email', 'unique:users,username,' . $this->guardian->user_id
            ],
            'phone' => 'nullable|string|min:8|max:50',
            'photo' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'password' => ['nullable', 'confirmed', Password::min(8)]
        ];
    }

    /**
     * Messages for validation rules
     * 
     * @return array
     */
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
