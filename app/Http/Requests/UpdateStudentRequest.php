<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateStudentRequest extends FormRequest
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
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'birth_date' => 'nullable|date_format:Y-m-d',
            'birth_place' => 'nullable|string|max:255',
            'gender' => 'nullable|numeric|between:0,1',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|min:8|max:50',
            'identifier' => ['required', 'string', 'max:255', 'min:4'],
            'id_number' => 'nullable|string|max:25',
            'photo' => 'nullable|string',
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
